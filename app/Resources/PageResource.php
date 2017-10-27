<?php
namespace GravApi\Resources;

use Grav\Common\Page\Page;

/**
 * Class BaseHandler
 * @package GravApi\Handlers
 */
class PageResource
{
    protected $page;

    public function __construct(Page $page)
    {
        $this->page = $page;
    }

    /**
     * Returns the page object as an array/json.
     * Also accepts an array of fields by which to filter.
     *
     * @param  [array] $fields optional
     * @param  [bool] $attributes_only optional
     * @return [array]
     */
    public function toJson($fields = null, $attributes_only = false)
    {
        // All our Page attributes
        $attributes = [
            // TODO: is this useful here?
            // 'active' => $this->page->active(),

            // TODO: is this useful here?
            // 'activeChild' => $this->page->activeChild(),

            // TODO: how to use this? always seems empty.
            // 'adjacentSibling' => $this->page->adjacentSibling(),

            // TODO: Are blueprints really necessary output???
            // 'blueprintName' => $this->page->blueprintName(),
            // 'blueprints' => $this->page->blueprints(),

            'children' => $this->processChildren(),
            'childType' => $this->page->childType(),
            'content' => $this->page->content(),
            'date' => $this->page->date(),
            'eTag' => $this->page->eTag(),
            'expires' => $this->page->expires(),
            'exists' => $this->page->exists(),
            'extension' => $this->page->extension(),
            'extra' => $this->page->extra(),

            // TODO: would this be any use?
            // 'file' => $this->page->file(),

            // this would expose server directory structure, so shouldn't be returned
            // 'filePath' => $this->page->filePath(),

            'filePathClean' => $this->page->filePathClean(),
            'folder' => $this->page->folder(),
            'frontmatter' => $this->page->frontmatter(),
            'getRawContent' => $this->page->getRawContent(),
            'header' => $this->page->header(),
            'home' => $this->page->home(),
            'id' => $this->page->id(),
            'isDir' => $this->page->isDir(),
            'isFirst' => $this->page->isFirst(),
            'isLast' => $this->page->isLast(),
            'isPage' => $this->page->isPage(),
            'language' => $this->page->language(),
            'lastModified' => $this->page->lastModified(),
            'link' => $this->page->link(),
            'maxCount' => $this->page->maxCount(),
            'menu' => $this->page->menu(),
            'metadata' => $this->page->metadata(),
            'modified' => $this->page->modified(),
            'modularTwig' => $this->page->modularTwig(),
            'modular' => $this->page->modular(),
            'name' => $this->page->name(),

            // TODO: how to use this? always seems empty.
            // 'nextSibling' => $this->page->nextSibling(),

            'order' => $this->page->order(),
            'orderDir' => $this->page->orderDir(),
            'orderBy' => $this->page->orderBy(),
            'orderManual' => $this->page->orderManual(),
            'parent' => $this->page->parent()->route(),

            // this would expose server directory structure, so shouldn't be returned
            // 'path' => $this->page->path(),

            'permalink' => $this->page->permalink(),

            // TODO: how to use this? always seems empty.
            // 'prevSibling' => $this->page->prevSibling(),

            'publishDate' => $this->page->publishDate(),
            'published' => $this->page->published(),
            'raw' => $this->page->raw(),
            'rawMarkdown' => $this->page->rawMarkdown(),
            'rawRoute' => $this->page->rawRoute(),
            'root' => $this->page->root(),
            'routable' => $this->page->routable(),
            'route' => $this->page->route(),
            'routeCanonical' => $this->page->routeCanonical(),
            'slug' => $this->page->slug(),
            'summary' => $this->page->summary(),
            'taxonomy' => $this->page->taxonomy(),
            'template' => $this->page->template(),
            'title' => $this->page->title(),
            'translatedLanguages' => $this->page->translatedLanguages(),
            'unpublishDate' => $this->page->unpublishDate(),
            'untranslatedLanguages' => $this->page->untranslatedLanguages(),
            'url' => $this->page->url(),
            'visible' => $this->page->visible(),
        ];

        // Filter for requested fields
        if ( $fields ) {
            $attributes = [];

            foreach ($fields as $field) {
                if ( method_exists($this->page, $field) ) {
                    if ($field === 'children') {
                        $attributes[$field] = $this->processChildren();
                    } else {
                        $attributes[$field] = $this->page->{$field}();
                    }
                }
            }
        }

        if ($attributes_only) {
            return $attributes;
        }

        // Return Resource object
        return [
            'type' => 'page',
            'id' => $this->page->route(),
            'attributes' => $attributes,
            'meta' => [
                'links' => [
                    'self' => $this->page->permalink()
                ]
            ]
        ];
    }

    /**
     * We have to process children because the collection's
     * toArray method will expose our server directory structure
     * @return [array]
     */
    protected function processChildren() {
        $children = [];
        foreach ($this->page->children()->toArray() as $child) {
            // we generate the routes for each child
            $children[] = $this->page->route().'/'.$child['slug'];
        }
        return $children;
    }
}
