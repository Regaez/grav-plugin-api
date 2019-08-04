<?php
namespace GravApi\Responses;

use Symfony\Component\Yaml\Yaml;

/**
 * Class BaseResponse
 * @package GravApi\Responses
 */
class BaseResponse
{
    // URL to API documentation
    private $documentation;

    protected $message;

    public function __construct()
    {
        $blueprints = Yaml::parse(file_get_contents(__DIR__.'/../../blueprints.yaml'));
        $this->documentation = $blueprints['docs'];
    }

    public function get()
    {
        return  [
            'message' => $this->message,
            'documentation' => $this->documentation
        ];
    }
}
