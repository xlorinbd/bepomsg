<?php

namespace App\Services;

use App\Library\SMSCounter;
use App\Models\SmsPricingTier;
use App\Models\SmsPurchase;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class SMSCreditService
{
    /**
     * Calculate the number of SMS credits needed for a message.
     *
     * Uses SMSCounter to determine the number of segments,
     * then multiplies by recipient count.
     *
     * @param string $message       The message content
     * @param string $smsType       SMS type (plain, unicode, voice, mms, whatsapp, viber, otp)
     * @param int    $recipientCount Number of recipients
     *
     * @return int Total credits needed
     */
    public function calculateCreditsNeeded(string $message, string $smsType = 'plain', int $recipientCount = 1): int
    {
        $counter = new SMSCounter();
        $messageData = $counter->count($message, $smsType == 'whatsapp' ? 'WHATSAPP' : null);
        $segments = max($messageData->messages, 1);

        return $segments * $recipientCount;
    }

    /**
     * Check if a user has enough SMS credits.
     *
     * @param User $user           The user to check
     * @param int  $creditsNeeded  Credits required
     *
     * @return bool
     */
    public function hasEnoughCredits(User $user, int $creditsNeeded): bool
    {
        return $user->sms_balance >= $creditsNeeded;
    }

    /**
     * Deduct SMS credits from a user's balance (atomic operation).
     *
     * This method uses a database transaction with row locking
     * to prevent race conditions during concurrent deductions.
     *
     * @param User $user    The user to deduct from
     * @param int  $credits Number of credits to deduct
     *
     * @return bool True if deduction was successful, false if insufficient balance
     */
    public function deductCredits(User $user, int $credits): bool
    {
        return DB::transaction(function () use ($user, $credits) {
            $freshUser = User::lockForUpdate()->find($user->id);

            if ($freshUser->sms_balance < $credits) {
                return false;
            }

            $freshUser->decrement('sms_balance', $credits);

            // Refresh the original user instance
            $user->refresh();

            return true;
        });
    }

    /**
     * Add SMS credits to a user's balance.
     *
     * @param User $user    The user to credit
     * @param int  $credits Number of credits to add
     */
    public function addCredits(User $user, int $credits): void
    {
        $user->increment('sms_balance', $credits);
    }

    /**
     * Get the applicable rate for a given SMS quantity.
     *
     * @param int $quantity The SMS quantity to look up
     *
     * @return float|null The rate, or null if no tier matches
     */
    public function getRate(int $quantity): ?float
    {
        return SmsPricingTier::getRateForQuantity($quantity);
    }

    /**
     * Calculate the total price for a given SMS quantity.
     *
     * Returns an array with quantity, rate, and total price.
     *
     * @param int $quantity         The SMS quantity to purchase
     * @param int $bonusCredits     Optional bonus credits to add (e.g., promotional)
     *
     * @return array{quantity: int, credits_added: int, rate: float, total: float}|null
     */
    public function calculatePrice(int $quantity, int $bonusCredits = 0): ?array
    {
        $rate = $this->getRate($quantity);

        if (is_null($rate)) {
            return null;
        }

        return [
            'quantity' => $quantity,
            'credits_added' => $quantity + $bonusCredits,
            'rate' => $rate,
            'total' => round($quantity * $rate, 2),
        ];
    }

    /**
     * Create a pending SMS purchase record.
     *
     * @param User   $user
     * @param int    $quantity
     * @param int    $creditsAdded
     * @param float  $rate
     * @param float  $totalPrice
     * @param string $paymentMethod
     *
     * @return SmsPurchase
     */
    public function createPurchase(
        User $user,
        int $quantity,
        int $creditsAdded,
        float $rate,
        float $totalPrice,
        string $paymentMethod = 'offline'
    ): SmsPurchase {
        return SmsPurchase::create([
            'user_id' => $user->id,
            'sms_quantity' => $quantity,
            'credits_added' => $creditsAdded,
            'rate' => $rate,
            'total_price' => $totalPrice,
            'payment_method' => $paymentMethod,
            'status' => SmsPurchase::STATUS_PENDING,
        ]);
    }

    /**
     * Complete a purchase: mark as completed and add credits.
     *
     * @param SmsPurchase $purchase
     * @param string|null $transactionId
     *
     * @return bool
     */
    public function completePurchase(SmsPurchase $purchase, ?string $transactionId = null): bool
    {
        return DB::transaction(function () use ($purchase, $transactionId) {
            $purchase->update([
                'status' => SmsPurchase::STATUS_COMPLETED,
                'transaction_id' => $transactionId,
            ]);

            $this->addCredits($purchase->user, $purchase->credits_added);

            return true;
        });
    }

    /**
     * Fail a purchase.
     *
     * @param SmsPurchase $purchase
     * @param string|null $reason
     */
    public function failPurchase(SmsPurchase $purchase, ?string $reason = null): void
    {
        $purchase->update([
            'status' => SmsPurchase::STATUS_FAILED,
            'notes' => $reason,
        ]);
    }
}
