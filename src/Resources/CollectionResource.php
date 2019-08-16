<?php
namespace GravApi\Resources;

abstract class CollectionResource
{
    protected $collection;
    protected $filter = array();

    abstract protected function getResource($resource);

    /**
     * Returns the resource object as an array/json.
     *
     * @return array
     */
    public function toJson()
    {
        $data = [];

        foreach ($this->collection as $item) {
            $resource = $this->getResource($item);

            // If the item exists in the Resource filter, then we skip it
            if (in_array($resource->getId(), $this->filter)) {
                continue;
            }

            $data[] = $resource->toJson();
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
