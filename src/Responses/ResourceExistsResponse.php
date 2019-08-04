<?php
namespace GravApi\Responses;

/**
 * Class ResourceExistsResponse
 * @package GravApi\Responses
 */
class ResourceExistsResponse extends BaseResponse
{
    public function __construct()
    {
        parent::__construct();
        $this->message = 'Resource already exists.';
    }
}
