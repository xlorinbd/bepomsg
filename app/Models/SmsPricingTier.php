<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Collection;

/**
 * @method static where(string $string, mixed $value)
 * @method static create(array $data)
 * @method static find(int $id)
 *
 * @property int    $id
 * @property int    $min_qty
 * @property int    $max_qty
 * @property float  $rate
 * @property bool   $status
 */
class SmsPricingTier extends Model
{
    protected $table = 'sms_pricing_tiers';

    protected $fillable = [
        'min_qty',
        'max_qty',
        'rate',
        'status',
    ];

    protected $casts = [
        'min_qty' => 'integer',
        'max_qty' => 'integer',
        'rate' => 'float',
        'status' => 'boolean',
    ];

    /**
     * Get the applicable rate for a given SMS quantity.
     */
    public static function getRateForQuantity(int $quantity): ?float
    {
        return self::getTierForQuantity($quantity)?->rate;
    }

    /**
     * Get the applicable tier object for a given SMS quantity.
     * Handles NULL max_qty (means unlimited upper bound).
     */
    public static function getTierForQuantity(int $quantity): ?self
    {
        return self::where('status', true)
            ->where('min_qty', '<=', $quantity)
            ->where(function ($q) use ($quantity) {
                $q->whereNull('max_qty')
                    ->orWhere('max_qty', '>=', $quantity);
            })
            ->orderByDesc('min_qty')
            ->first();
    }

    /**
     * Get all active pricing tiers ordered by min_qty.
     */
    public static function getActiveTiers(): Collection
    {
        return self::where('status', true)
            ->orderBy('min_qty')
            ->get();
    }
}
