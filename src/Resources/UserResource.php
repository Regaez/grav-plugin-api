<?php
namespace GravApi\Resources;

use GravApi\Config\Config;
use GravApi\Config\Constants;
use GravApi\Resources\Resource;
use Grav\Common\User\User;

/**
 * Class UserResource
 * @package GravApi\Resources
 */
class UserResource extends Resource
{
    /**
     * @var User
     */
    protected $resource;

    public function __construct(User $userDetails)
    {
        $this->resource = $userDetails;

        // Set the attribute filter
        $this->setFilter();
    }

    /**
     * Returns the hypermedia array for this resource
     *
     * @return string
     */
    public function getHypermedia()
    {
        return [
            'related' => $this->getRelatedHypermedia()
        ];
    }

    /**
     * Returns the identifier for this resource
     *
     * @return string
     */
    public function getId()
    {
        return $this->resource->username;
    }

    /**
     * Returns the attributes associated with this resource
     *
     * @param array|null $fields
     * @return array
     */
    public function getResourceAttributes()
    {
        $attributes = [
            'username' => $this->resource->get('username'),
            'email' => $this->resource->get('email'),
            'fullname' => $this->resource->get('fullname'),
            'title' => $this->resource->get('title'),
            'state' => $this->resource->get('state'),
            'access' => $this->resource->get('access')
        ];

        if ($this->filter) {
            $attributes = [];

            foreach ($this->filter as $field) {
                $attributes[$field] = $this->resource->get($field);
            }
        }

        return $attributes;
    }

    /**
     * Returns the resource type
     *
     * @return string
     */
    public function getResourceType()
    {
        return Constants::TYPE_USER;
    }

    /**
     * Sets a filter for the list of attributes based on the
     * API plugin's config setting.

     * @return void
     */
    private function setFilter()
    {
        $filter = Config::instance()->users->get->fields;

        if (!empty($filter)) {
            $this->filter = $filter;
        }
    }
}
