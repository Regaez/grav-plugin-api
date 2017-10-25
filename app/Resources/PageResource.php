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

    public function toJson()
    {
        return [
            'title' => $this->page->title(),
            'slug' => $this->page->slug()
        ];
    }
}
