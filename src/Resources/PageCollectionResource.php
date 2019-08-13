<?php
namespace GravApi\Resources;

use Grav\Common\Page\Page;
use Grav\Common\Page\Collection;
use GravApi\Resources\PageResource;

/**
 * Class BaseHandler
 * @package GravApi\Handlers
 */
class PageCollectionResource extends CollectionResource
{
    /**
     * @param Collection $collection
     */
    public function __construct(Collection $collection)
    {
        $this->collection = $collection;
    }

    /**
     * Accepts an resource from the collection and
     * returns a new PageResource instance
     *
     * @param  Page $plugin
     * @return PageResource
     */
    public function getResource($page)
    {
        return new PageResource($page);
    }
}
