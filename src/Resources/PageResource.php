<?php
namespace GravApi\Resources;

use Grav\Common\Grav;
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

        $this->setParent();

        // Set the attribute filter
        $this->setFilter();
    }

    /**
     * Returns the hypermedia array for this resource
     *
     * @return string
     */
    public function getHypermedia()
    {
        return [
            'self' => $this->resource->permalink(),
            'children' => $this->getChildrenHypermedia(),
            'parent' => $this->getParentHypermedia(),
            'related' => $this->getRelatedHypermedia()
        ];
    }

    /**
     * Returns the releated hypermedia array for this resource type
     *
     * @return array
     */
    public function getRelatedHypermedia()
    {
        return [
            'self' => $this->getRelatedSelf(),
            'children' => $this->getChildrenHypermedia(true),
            'parent' => $this->getParentHypermedia(true),
            'resource' => $this->getResourceEndpoint()
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
    public function getResourceAttributes()
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
            // 'getRawContent' => $this->resource-> getRawContent(),

            'header' => (array) $this->resource->header(),
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

            // This should be included in hypermedia links
            // 'parent' => $this->resource->parent()->rawRoute(),

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
                    } elseif ($field == 'header') {
                        $attributes[$field] = $this->processHeader();
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
    public function getResourceType()
    {
        return Constants::TYPE_PAGE;
    }

    /**
     * Returns the fully qualified children URLs
     *
     * @param boolean $related Set true to return API endpoints
     * @return array
     */
    public function getChildrenHypermedia($related = false)
    {
        $children = [];

        foreach ($this->processChildren() as $child) {
            $path = ltrim($child, '/');

            if ($related) {
                $children[] = $this->getResourceEndpoint() . $path;
            } else {
                $children[] = Config::instance()->rootUrl . $path;
            }
        }

        return $children;
    }

    /**
     * Returns the fully qualified parent URL, or null if no parent exists
     *
     * @param boolean $related Set true to return API endpoints
     * @return string|null
     */
    public function getParentHypermedia($related = false)
    {
        $parent = $this->resource->parent();
        $path = ltrim($parent->rawRoute(), '/');

        if (!$parent || !$path) {
            return null;
        }

        if ($related) {
            return $this->getResourceEndpoint() . $path;
        } else {
            return Config::instance()->rootUrl . $path;
        }
    }

    /**
     * Sets the parent of the page to the previous route component
     */
    private function setParent()
    {
        $routeComponents = explode('/', $this->resource->rawRoute());
        $parentRoute = implode('/', array_slice($routeComponents, 0, -1));

        $parent = Grav::instance()['pages']->find($parentRoute);

        if ($parent) {
            $this->resource->parent($parent);
        }
    }

    /**
     * We process the children because the collection's
     * toArray method will expose our server directory structure
     *
     * @return array
     */
    public function processChildren()
    {
        $children = [];
        foreach ($this->resource->children()->toArray() as $child) {
            // we generate the routes for each child
            $children[] = $this->resource->rawRoute().'/'.$child['slug'];
        }
        return $children;
    }

    /**
     * We process the media to return a prettier respresentation of the media.
     *
     * @return array
     */
    public function processMedia()
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
     * We process the header to return a stricter respresentation of the taxonomy (all arrays, all strings).
     *
     * @return array
     */
    public function processHeader()
    {
        $header = $this->resource->header();
        if (property_exists($header, 'taxonomy') && !is_null($header->taxonomy)) {
            foreach ($header->taxonomy as $key => $value) {
                if (!is_array($value)) {
                    $header->taxonomy[$key] = array(is_string($value) ? $value : json_encode($value));
                } else {
                    $header->taxonomy[$key] = array_map(function ($e) {
                        return is_string($e) ? $e : json_encode($e);
                    }, $header->taxonomy[$key]);
                }
            }
        }

        return $header;
    }

    /**
     * Sets a filter for the list of attributes based on the
     * API plugin's config setting.

     * @return void
     */
    private function setFilter()
    {
        $filter = null;

        if (Config::instance()->pages) {
            $filter = Config::instance()->pages->get->fields;
        }

        // TODO: improve validation of filter input
        if ($filter) {
            $this->filter = $filter;
        }
    }
}
