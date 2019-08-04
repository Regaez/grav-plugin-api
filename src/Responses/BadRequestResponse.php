<?php
namespace GravApi\Responses;

/**
 * Class BadRequestResponse
 * @package GravApi\Responses
 */
class BadRequestResponse extends BaseResponse
{
    public function __construct($msg = null)
    {
        parent::__construct();

        $this->message = 'Bad request.';

        if ($msg) {
            $this->message .= ' ' . $msg;
        }
    }
}
