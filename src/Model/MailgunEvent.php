<?php

namespace Biegalski\LaravelMailgunWebhooks\Model;

use Illuminate\Database\Eloquent\Model;

/**
 * Class MailgunEvent
 * @package Biegalski\LaravelMailgunWebhooks\Model
 */
class MailgunEvent extends Model
{
    /**
     * @var array
     */
    protected $dispatchesEvents = [
        'created' => \Biegalski\LaravelMailgunWebhooks\Events\CreatedEvent::class,
    ];
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'event_type',
        'email_id',
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function email(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(MailgunEmail::class, 'email_id');
    }
}
