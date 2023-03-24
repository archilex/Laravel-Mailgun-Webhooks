<?php

namespace Biegalski\LaravelMailgunWebhooks\Repositories;

use Illuminate\Support\Facades\DB;
use Biegalski\LaravelMailgunWebhooks\Model\MailgunEmail;
use Biegalski\LaravelMailgunWebhooks\Model\MailgunEvent;
use Biegalski\LaravelMailgunWebhooks\Model\MailgunEmailContent;

/**
 * Class MailgunEventRepository
 * @package Biegalski\LaravelMailgunWebhooks\Repositories
 */
class MailgunEventRepository
{
    /**
     * @var MailgunEmailContent
     */
    private $content;

    /**
     * @var MailgunEvent
     */
    private $model;

    /**
     * @var MailgunEmail
     */
    private $email;


    /**
     * MailgunEventRepository constructor.
     * @param MailgunEmailContent $content
     * @param MailgunEvent $model
     * @param MailgunEmail $email
     */
    public function __construct(
        MailgunEmailContent $content,
        MailgunEvent $model,
        MailgunEmail $email,
    )
    {
        $this->content = $content;
        $this->model = $model;
        $this->email = $email;

        if( config()->has('mailgun-webhooks.custom_database') && config('mailgun-webhooks.custom_database') !== null ){
            $this->content->setConnection(config('mailgun-webhooks.custom_database'));
            $this->model->setConnection(config('mailgun-webhooks.custom_database'));
            $this->email->setConnection(config('mailgun-webhooks.custom_database'));
        }
    }

    /**
     * @param string $eventType
     * @param array $data
     * @param null $userId
     * @return null
     */
    public function store(string $eventType, array $data, $userId = null)
    {
        $email = $this->email->storeEmail($data, $userId);
        
        $this->storeEvent($eventType, $email->id);

        return $email->id;
    }

    /**
     * @param string $eventType
     * @param array $data
     * @param null $userId
     * @return mixed
     */
    private function storeEvent(string $eventType, $emailId)
    {        
        return $this->model->create([
            'event_type' => $eventType,
            'email_id' => $emailId,
        ]);
    }

    /**
     * @param int $emailId
     * @param array $content
     * @return mixed
     */
    public function storeContent(int $emailId, array $content)
    {
        $data = [
            'email_id' => $emailId,
            'subject' => $content['subject'] ?? null,
            'to' => $content['To'] ?? null,
            'content_type' => $content['Content-Type'] ?? null,
            'message_id' => $content['Message-Id'] ?? null,
            'stripped_text' => null,
            'stripped_html' => null,
            'body_html' => null,
            'body_plain' => null,
        ];

        $data['stripped_text'] = $this->checkStorageOptions('mailgun-webhooks.content_logging.stripped_text', $content['stripped-text']);
        $data['stripped_html'] = $this->checkStorageOptions('mailgun-webhooks.content_logging.stripped_html', $content['stripped-html']);
        $data['body_html'] = $this->checkStorageOptions('mailgun-webhooks.content_logging.body_html', $content['body-html']);
        $data['body_plain'] = $this->checkStorageOptions('mailgun-webhooks.content_logging.body_plain', $content['body-plain']);

        return $this->content->create($data);
    }

    /**
     * @param string $email
     * @return mixed
     */
    public function findUser(string $email)
    {
        return DB::table( config('mailgun-webhooks.user_table.name') )
            ->where( config('mailgun-webhooks.user_table.email_column'), $email)
            ->first();
    }

    /**
     * @param string $key
     * @param string $content
     * @return string|null
     */
    private function checkStorageOptions(string $key, string $content)
    {
        if( config()->has($key) && config($key) === false ){
            return null;
        }

        return $content;
    }
}
