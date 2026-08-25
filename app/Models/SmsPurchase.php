<?php

namespace App\Models;

use App\Library\Traits\HasUid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @method static where(string $string, mixed $value)
 * @method static create(array $data)
 * @method static find(int $id)
 * @method static cursor()
 *
 * @property int    $id
 * @property string $uid
 * @property int    $user_id
 * @property int    $sms_quantity
 * @property int    $credits_added
 * @property float  $rate
 * @property float  $total_price
 * @property string $payment_method
 * @property string $transaction_id
 * @property string $status
 * @property string $notes
 */
class SmsPurchase extends Model
{
    use HasUid;

    protected $table = 'sms_purchases';

    const STATUS_PENDING = 'pending';
    const STATUS_COMPLETED = 'completed';
    const STATUS_FAILED = 'failed';
    const STATUS_REFUNDED = 'refunded';

    protected $fillable = [
        'user_id',
        'sms_quantity',
        'credits_added',
        'rate',
        'total_price',
        'payment_method',
        'transaction_id',
        'status',
        'notes',
    ];

    protected $casts = [
        'sms_quantity' => 'integer',
        'credits_added' => 'integer',
        'rate' => 'float',
        'total_price' => 'float',
    ];

    /**
     * Get the user who made this purchase.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scope: completed purchases only.
     */
    public function scopeCompleted($query)
    {
        return $query->where('status', self::STATUS_COMPLETED);
    }

    /**
     * Scope: purchases for a specific user.
     */
    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }
}
