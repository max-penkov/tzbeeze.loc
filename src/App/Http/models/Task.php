<?php

namespace App\Http\models;

/**
 * Class Task
 * @package App\Http\models
 */
class Task
{
    const STATUS_NEW = 'New';
    const STATUS_EDIT = 'Edited by Admin';
    /**
     * @var integer
     */
    public $id;
    /**
     * @var \DateTime
     */
    public $date;
    /**
     * @var string
     */
    public $title;
    /**
     * @var string
     */
    public $email;
    /**
     * @var
     */
    public $userName;
    /**
     * @var string
     */
    public $content;
    /**
     * @var string
     */
    public $status;

    /**
     * @param string $title
     * @param string $content
     *
     * @param string $email
     * @param string $userName
     *
     * @return $this
     */
    public function create(string $title, string $content, string $email, string $userName)
    {
        $this->status   = self::STATUS_NEW;
        $this->title    = $title;
        $this->content  = $content;
        $this->email    = $email;
        $this->userName = $userName;
        $this->date     = time();
        return $this;
    }

    /**
     * @param string $title
     * @param string $content
     * @param string $status
     * @param string $email
     */
    public function edit(string $title, string $content, string $status, string $email): void
    {
        $this->title   = $title;
        $this->content = $content;
        $this->status  = $status;
        $this->email   = $email;
    }

    /**
     * Set task as done
     */
    public function done(): void
    {
        $this->status = self::STATUS_EDIT;
    }
}