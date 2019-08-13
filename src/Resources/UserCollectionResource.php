<?php
namespace GravApi\Resources;

use Grav\Common\User\User;
use GravApi\Resources\UserResource;

/**
 * Class PluginCollectionResource
 * @package GravApi\Resources
 */
class UserCollectionResource extends CollectionResource
{
    /**
     * @param User[] $users
     */
    public function __construct($users)
    {
        $this->collection = $users;
    }

    /**
     * Accepts an resource from the collection and
     * returns a new PluginResource instance
     *
     * @param  User $user
     * @return UserResource
     */
    public function getResource($user)
    {
        return new UserResource($user);
    }
}
