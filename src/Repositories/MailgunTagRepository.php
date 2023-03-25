<?php

namespace Biegalski\LaravelMailgunWebhooks\Repositories;

use Biegalski\LaravelMailgunWebhooks\Model\MailgunTag;
use Biegalski\LaravelMailgunWebhooks\Model\MailgunEmailTag;

/**
 * Class MailgunTagRepository
 * @package Biegalski\LaravelMailgunWebhooks\Repositories
 */
class MailgunTagRepository
{
    /**
     * @var MailgunTag
     */
    private $model;

    /**
     * @var MailgunEmailTag
     */
    private $emailTag;

    /**
     * MailgunTagRepository constructor.
     * @param MailgunTag $model
     * @param MailgunEmailTag $emailTag
     */
    public function __construct(MailgunTag $model, MailgunEmailTag $emailTag)
    {
        $this->model = $model;
        $this->emailTag = $emailTag;

        if( config()->has('mailgun-webhooks.custom_database') && config('mailgun-webhooks.custom_database') !== null ){
            $this->model->setConnection(config('mailgun-webhooks.custom_database'));
            $this->emailTag->setConnection(config('mailgun-webhooks.custom_database'));
        }
    }

    /**
     * @param string $tag
     * @return mixed
     */
    public function findOrCreateTag(string $tag)
    {
        return $this->model->firstOrCreate(['tag_name' => $tag]);
    }

    /**
     * @param array $tags
     * @param int $emailId
     */
    public function tagEmail(array $tags, int $emailId)
    {
        foreach ($tags as $tag){
            $findTag = $this->findOrCreateTag($tag);

            if( isset($findTag->id) ){
                $this->emailTag->firstOrCreate([
                        'email_id' => $emailId,
                        'tag_id' => $findTag->id
                    ]);
            }
        }
    }

}
