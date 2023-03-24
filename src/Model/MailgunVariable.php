<?php

namespace Biegalski\LaravelMailgunWebhooks\Model;

use Illuminate\Database\Eloquent\Model;

/**
 * Class MailgunVariable
 * @package Biegalski\LaravelMailgunWebhooks\Model
 */
class MailgunVariable extends Model
{
    /**
     * @var array
     */
    protected $fillable = [
        'email_id',
        'key',
        'value'
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function email(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(MailgunEmail::class, 'email_id');
    }
}
