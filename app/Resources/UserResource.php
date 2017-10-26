<?php
namespace GravApi\Resources;

/**
 * Class UserResource
 * @package GravApi\UserResource
 */
class UserResource
{
    protected $username;
    protected $email;
    protected $fullname;
    protected $title;
    protected $state;
    protected $access;

    public function __construct($details)
    {
        $this->username = $details['username'] ?: null;
        $this->email = $details['email'] ?: null;
        $this->fullname = $details['fullname'] ?: null;
        $this->title = $details['title'] ?: null;
        $this->state = $details['state'] ?: null;
        $this->access = $details['access'] ?: null;
    }

    public function toJson()
    {
        return [
            'username' => $this->username,
            'email' => $this->email,
            'fullname' => $this->fullname,
            'title' => $this->title,
            'state' => $this->state,
            'access' => $this->access
        ];
    }
}
