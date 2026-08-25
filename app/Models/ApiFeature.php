<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ApiFeature extends Model
{
    protected $fillable = ['name', 'slug', 'status'];

    /**
     * Check if a feature is enabled.
     *
     * @param string $slug
     * @return bool
     */
    public static function isEnabled(string $slug): bool
    {
        $feature = self::where('slug', $slug)->first();

        return $feature && $feature->status;
    }
}
