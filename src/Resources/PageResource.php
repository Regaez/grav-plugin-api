<?php
namespace GravApi\Resources;

use Grav\Common\Page\Page;
use GravApi\Config\Config;
use GravApi\Config\Constants;
use GravApi\Resources\Resource;

/**
 * Class BaseHandler
 * @package GravApi\Handlers
 */
class PageResource extends Resource
{
    /**
     * @var Page
     */
    protected $resource;

    public function __construct(Page $page)
    {
        $this->resource = $page;

        // Set the attribute filter
        $this->setFilter();
    }

    /**
     * Returns the hypermedia array for this resource
     *
     * @return string
     */
    protected function getHypermedia()
    {
        return [
            'self' => $this->resource->permalink(),
            'related' => $this->getRelatedHypermedia()
        ];
    }

    /**
     * Returns the identifier for this resource
     *
     * @return string
     */
    public function getId()
    {
        // we use raw route so that the homepage
        // returns a query-able identifier
        return ltrim($this->resource->rawRoute(), '/');
    }

    /**
     * Returns the attributes associated with this resource
     *
     * @param array|null $fields
     * @return array
     */
    protected function getResourceAttributes()
    {
        // All our Page attributes
        $attributes = [
            // TODO: is this useful here?
            // 'active' => $this->resource->active(),

            // TODO: is this useful here?
            // 'activeChild' => $this->resource->activeChild(),

            // TODO: how to use this? always seems empty.
            // 'adjacentSibling' => $this->resource->adjacentSibling(),

            // TODO: Are blueprints really necessary output???
            // 'blueprintName' => $this->resource->blueprintName(),
            // 'blueprints' => $this->resource->blueprints(),

            'children' => $this->processChildren(),
            'childType' => $this->resource->childType(),
            'content' => $this->resource->content(),
            'date' => $this->resource->date(),
            'eTag' => $this->resource->eTag(),
            'expires' => $this->resource->expires(),
            'exists' => $this->resource->exists(),
            'extension' => $this->resource->extension(),
            'extra' => $this->resource->extra(),

            // TODO: would this be any use?
            // 'file' => $this->resource->file(),

            // This would expose server directory structure, so shouldn't be returned
            // 'filePath' => $this->resource->filePath(),

            'filePathClean' => $this->resource->filePathClean(),
            'folder' => $this->resource->folder(),
            'frontmatter' => $this->resource->frontmatter(),

            // TODO: do we really need this and `content` field?
            // 'getRawContent' => $this->resource->getRawContent(),

            'header' => $this->resource->header(),
            'home' => $this->resource->home(),
            'id' => $this->resource->id(),
            'isDir' => $this->resource->isDir(),
            'isFirst' => $this->resource->isFirst(),
            'isLast' => $this->resource->isLast(),
            'isPage' => $this->resource->isPage(),
            'language' => $this->resource->language(),
            'lastModified' => $this->resource->lastModified(),

            // Already have `route` and `permalink` fields
            // 'link' => $this->resource->link(),

            'maxCount' => $this->resource->maxCount(),
            'media' => $this->processMedia(),
            'menu' => $this->resource->menu(),
            'metadata' => $this->resource->metadata(),
            'modified' => $this->resource->modified(),
            'modularTwig' => $this->resource->modularTwig(),
            'modular' => $this->resource->modular(),
            'name' => $this->resource->name(),

            // TODO: how to use this? always seems empty.
            // 'nextSibling' => $this->resource->nextSibling(),

            'order' => $this->resource->order(),
            'orderDir' => $this->resource->orderDir(),
            'orderBy' => $this->resource->orderBy(),
            'orderManual' => $this->resource->orderManual(),
            'parent' => $this->resource->parent()->route(),

            // This would expose server directory structure, so shouldn't be returned
            // 'path' => $this->resource->path(),

            'permalink' => $this->resource->permalink(),

            // TODO: how to use this? always seems empty.
            // 'prevSibling' => $this->resource->prevSibling(),

            'publishDate' => $this->resource->publishDate(),
            'published' => $this->resource->published(),
            'raw' => $this->resource->raw(),
            'rawMarkdown' => $this->resource->rawMarkdown(),
            'rawRoute' => $this->resource->rawRoute(),
            'root' => $this->resource->root(),
            'routable' => $this->resource->routable(),
            'route' => $this->resource->route(),
            'routeCanonical' => $this->resource->routeCanonical(),
            'slug' => $this->resource->slug(),
            'summary' => $this->resource->summary(),
            'taxonomy' => $this->resource->taxonomy(),
            'template' => $this->resource->template(),
            'title' => $this->resource->title(),
            'translatedLanguages' => $this->resource->translatedLanguages(),
            'unpublishDate' => $this->resource->unpublishDate(),
            'untranslatedLanguages' => $this->resource->untranslatedLanguages(),

            // Already have `route` and `permalink` fields
            // 'url' => $this->resource->url(),

            'visible' => $this->resource->visible(),
        ];

        // Filter for requested fields
        if ($this->filter) {
            $attributes = [];

            foreach ($this->filter as $field) {
                // We have to handle certain fields differently by calling
                // helper functions to preprocess the data
                if (method_exists($this->resource, $field)) {
                    if ($field === 'children') {
                        $attributes[$field] = $this->processChildren();
                    } elseif ($field === 'media') {
                        $attributes[$field] = $this->processMedia();
                    } else {
                        // Call the method with the associated field name
                        $attributes[$field] = $this->resource->{$field}();
                    }
                }
            }
        }

        return $attributes;
    }
    /**
     * Returns the resource type
     *
     * @return string
     */
    protected function getResourceType()
    {
        return Constants::TYPE_PAGE;
    }

    /**
     * We process the children because the collection's
     * toArray method will expose our server directory structure
     *
     * @return array
     */
    protected function processChildren()
    {
        $children = [];
        foreach ($this->resource->children()->toArray() as $child) {
            // we generate the routes for each child
            $children[] = $this->resource->route().'/'.$child['slug'];
        }
        return $children;
    }

    /**
     * We process the media to return a prettier respresentation of the media.
     *
     * @return array
     */
    protected function processMedia()
    {
        // TODO: handle other page media types; probably make this a resource type, or its own class
        $media = [];

        foreach ($this->resource->media()->all() as $medium) {
            $url = $this->resource->permalink() . '/' . rawurlencode($medium->filename);

            // We also need to omit some media properties, since
            // they would expose our server directory structure.
            $media[] = [
                'height' => $medium->height,
                'mime' => $medium->mime,
                'name' => $medium->basename,
                'size' => $medium->size,
                'type' => $medium->type,
                'url' => $url,
                'width' => $medium->width
            ];
        }

        return $media;
    }

    /**
     * Sets a filter for the list of attributes based on the
     * API plugin's config setting.

     * @return void
     */
    private function setFilter()
    {
        $filter = Config::instance()->pages->get['fields'];

        // TODO: improve validation of filter input
        if ($filter) {
            $this->filter = $filter;
        }
    }
}
