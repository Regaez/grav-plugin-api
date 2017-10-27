<?php
namespace GravApi\Resources;

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

        foreach($this->collection as $page) {
            $resource = new PageResource($page);
            $data[] = $resource->toJson($filter, true);
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
