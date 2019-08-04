<?php
namespace GravApi\Resources;

use GravApi\Config\Config;
use GravApi\Resources\PageResource;
use Grav\Common\Page\Collection;

/**
 * Class BaseHandler
 * @package GravApi\Handlers
 */
class PageCollectionResource
{
    protected $collection;

    public function __construct(Collection $collection)
    {
        $this->collection = $collection;
    }

    public function toJson($filter = null)
    {
        $data = [];

        $settings = Config::instance();

        foreach ($this->collection as $page) {
            $resource = new PageResource($page);
            $id = $resource->getId();
            $apiUrl = $settings->api->permalink.'/pages/'.$id;
            $data[] = [
                'id' => $id,
                'attributes' => $resource->toJson($filter, true),
                'links' => [
                    'self' => $page->permalink(),
                    'related' => [
                        'self' => $apiUrl
                    ]
                ]
            ];
        }

        // Return Resource object
        return [
            'items' => $data,
            'meta' => [
                'count' => count($data)
            ]
        ];
    }
}
