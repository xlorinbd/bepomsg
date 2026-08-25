<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @method static where(string $string, mixed $value)
 * @method static create(array $data)
 *
 * @property int $id
 * @property int $sender_id
 * @property int $sending_server_id
 */
class SenderIdRoute extends Model
{
    protected $table = 'sender_id_routes';

    protected $fillable = [
        'sender_id',
        'sending_server_id',
    ];

    /**
     * Get the sender ID record.
     */
    public function senderid(): BelongsTo
    {
        return $this->belongsTo(Senderid::class, 'sender_id');
    }

    /**
     * Get the sending server.
     */
    public function sendingServer(): BelongsTo
    {
        return $this->belongsTo(SendingServer::class);
    }
}
