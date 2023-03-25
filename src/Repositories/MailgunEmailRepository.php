<?php

namespace Biegalski\LaravelMailgunWebhooks\Repositories;

use Biegalski\LaravelMailgunWebhooks\Model\MailgunEmail;
use Biegalski\LaravelMailgunWebhooks\Repositories\MailgunTagRepository;
use Biegalski\LaravelMailgunWebhooks\Repositories\MailgunFlagRepository;
use Biegalski\LaravelMailgunWebhooks\Repositories\MailgunVariableRepository;

/**
 * Class MailgunEmailRepository
 * @package Biegalski\LaravelMailgunWebhooks\Repositories
 */
class MailgunEmailRepository
{
    /**
     * @var MailgunEmail
     */
    private $model;

    /**
     * @var \Biegalski\LaravelMailgunWebhooks\Repositories\MailgunFlagRepository
     */
    private $flags;

    /**
     * @var \Biegalski\LaravelMailgunWebhooks\Repositories\MailgunTagRepository
     */
    private $tag;

    /**
     * @var \Biegalski\LaravelMailgunWebhooks\Repositories\MailgunVariableRepository
     */
    private $variable;

    /**
     * MailgunEmailRepository constructor.
     * @param MailgunEmail $model
     * @param \Biegalski\LaravelMailgunWebhooks\Repositories\MailgunFlagRepository $flags
     * @param \Biegalski\LaravelMailgunWebhooks\Repositories\MailgunTagRepository $tag
     * @param \Biegalski\LaravelMailgunWebhooks\Repositories\MailgunVariableRepository $variable
     */
    public function __construct(
        MailgunEmail $model,
        MailgunFlagRepository $flags,
        MailgunTagRepository $tag,
        MailgunVariableRepository $variable
    )
    {
        $this->model = $model;
        $this->flags = $flags;
        $this->tag = $tag;
        $this->variable = $variable;

        if( config()->has('mailgun-webhooks.custom_database') && config('mailgun-webhooks.custom_database') !== null ){
            $this->model->setConnection(config('mailgun-webhooks.custom_database'));
            $this->flags->setConnection(config('mailgun-webhooks.custom_database'));
            $this->tag->setConnection(config('mailgun-webhooks.custom_database'));
            $this->variable->setConnection(config('mailgun-webhooks.custom_database'));
        }
    }

    /**
     * @param array $data
     * @param int $emailId
     * @return mixed
     */
    public function storeEmail(array $data, $userId)
    {
        $email = $this->model->firstOrCreate([
            'msg_id' => $this->getHeaders('msg_id', $data),
        ], [
            'uuid' => $data['event-data']['id'],
            'recipient_domain' => $data['event-data']['recipient-domain'] ?? null,
            'recipient_user' => $data['event-data']['recipient'] ?? null,
            'msg_to' => $this->getHeaders('to', $data),
            'msg_from' => $this->getHeaders('from', $data),
            'msg_subject' => $this->getHeaders('subject', $data),
            'msg_id' => $this->getHeaders('msg_id', $data),
            'msg_code' => $data['event-data']['delivery-status']['code'] ?? null,
            'attempt_number' => $data['event-data']['delivery-status']['attempt-no'] ?? 1,
            'attachments' => $this->areAttachmentsIncluded($data),
            'user_id' => $userId,
        ]);
        
        /**
         * @desc Check if flag logging is disabled
         */
        if( config('mailgun-webhooks.options.disable_flag_logging') !== true ){
            if( !empty($data['event-data']['flags']) && is_array($data['event-data']['flags']) ){
                $this->flags->createFlags($data['event-data']['flags'], $email->id);
            }
        }

        /**
         * @desc Check if tag logging is disabled
         */
        if( config('mailgun-webhooks.options.disable_tag_logging') !== true ){
            if( !empty($data['event-data']['tags']) && is_array($data['event-data']['tags']) ){
                $this->tag->tagEmail($data['event-data']['tags'], $email->id);
            }
        }

        /**
         * @desc Check if variable logging is disabled
         */
        if( config('mailgun-webhooks.options.disable_variable_logging') !== true ){
            if( !empty($data['event-data']['user-variables']) && is_array($data['event-data']['user-variables']) ){
                $this->variable->processEmailVariables($data['event-data']['user-variables'], $email->id);
            }
        }

        return $email;
    }

    /**
     * @param string $type
     * @param array $data
     * @return mixed|null
     */
    private function getHeaders(string $type, array $data)
    {
        if( isset($data['event-data']['message']['headers']) && is_array($data['event-data']['message']['headers']) ){
            switch ($type){
                case 'to':
                    return $data['event-data']['message']['headers']['to'] ?? null;
                case 'from':
                    return $data['event-data']['message']['headers']['from'] ?? null;
                case 'subject':
                    return $data['event-data']['message']['headers']['subject'] ?? null;
                case 'msg_id':
                    return $data['event-data']['message']['headers']['message-id'] ?? null;
                default:
                    return null;
            }
        }

        return null;
    }

    /**
     * @param array $data
     * @return int
     */
    private function areAttachmentsIncluded(array $data)
    {
        if( isset($data['event-data']['message']['attachments']) && empty($data['event-data']['message']['attachments']) ){
            return 0;
        }

        return 1;
    }
}
