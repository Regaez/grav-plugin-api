<?php
namespace GravApi\Responses;

/**
 * Class NotFoundResponse
 * @package GravApi\Responses
 */
class NotFoundResponse extends BaseResponse
{
    public function __construct() {
        parent::__construct();
        $this->message = 'Resource not found';
    }
}
