<?php

namespace Biegalski\LaravelMailgunWebhooks\Model;

use Illuminate\Database\Eloquent\Model;

class MailgunEmailContent extends Model
{
    /**
     * @var string
     */
    protected $table = 'mailgun_email_content';

    /**
     * @var array
     */
    protected $fillable = [
        'email_id',
        'subject',
        'to',
        'content_type',
        'message_id',
        'stripped_text',
        'stripped_html',
        'body_html',
        'body_plain'
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function email(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(MailgunEmail::class, 'email_id');
    }
}
