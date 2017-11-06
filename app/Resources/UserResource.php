<?php
namespace GravApi\Resources;

use GravApi\Config\Config;

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

    public function toJson($fields = null, $attributes_only = false)
    {
        $attributes = [
            'username' => $this->username,
            'email' => $this->email,
            'fullname' => $this->fullname,
            'title' => $this->title,
            'state' => $this->state,
            'access' => $this->access
        ];

        // Filter for requested fields
        if ( $fields ) {
            $attributes = [];

            foreach ($fields as $field) {
                if ( property_exists($this, $field) ) {
                    $attributes[$field] = $this->{$field};
                }
            }
        }

        if ($attributes_only) {
            return $attributes;
        }

        $settings = Config::instance();
        $apiUrl = $settings->api->permalink.'/users/'.$this->username;

        // Return Resource object
        return [
            'type' => 'user',
            'id' => $this->username,
            'attributes' => $attributes,
            'links' => [
                'related' => [
                    'self' => $apiUrl
                ]
            ]
        ];
    }
}
