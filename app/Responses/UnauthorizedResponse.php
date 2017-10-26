<?php
namespace GravApi\Responses;

/**
 * Class UnauthorizedResponse
 * @package GravApi\Responses
 */
class UnauthorizedResponse extends BaseResponse
{
    public function __construct() {
        parent::__construct();
        $this->message = 'Bad credentials';
    }
}
