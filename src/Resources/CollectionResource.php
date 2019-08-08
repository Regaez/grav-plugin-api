<?php
namespace GravApi\Resources;

abstract class CollectionResource
{
    protected $collection;

    abstract protected function getResource($resource);

    /**
     * Returns the resource object as an array/json.
     * Also accepts an array of fields by which to filter.
     *
     * @param  [array] $filter optional
     * @param  [bool] $attributes_only optional
     * @return [array]
     */
    public function toJson($filter = array(), $attributes_only = false)
    {
        $data = [];

        foreach ($this->collection as $name => $item) {
            $resource = $this->getResource($item);
            $data[] = $resource->toJson();
        }

        if ($attributes_only) {
            return $data;
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
