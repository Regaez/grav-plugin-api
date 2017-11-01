<?php
namespace GravApi\Helpers;

use Grav\Common\Grav;
use Grav\Common\Page\Page;

/**
 * Class PagesHandler
 * @package GravApi\Handlers
 */
class PageHelper
{
    // Our grav instance
    protected $grav;

    protected $route;
    protected $slug;
    protected $template;
    public $page;

    public function __construct($route, $template = '')
    {
        $this->grav = Grav::instance();

        $this->route = $route;
        $this->slug = basename($route);
        $this->template = $template;
    }

    public function getOrCreatePage()
    {
        // We trim slashes otherwise route '/' will unintentionally match homepage
        $page = trim($this->route, '/')
            ? $this->grav['pages']->dispatch($this->route, true)
            : $this->grav['pages']->root();

        if (!$page) {

            // Breaks recursion when we hit the root
            if ($this->slug === '') {
                return null;
            }

            // Recursively find or create parent(s).
            $parentPath = dirname($this->route);
            $parentHelper = new PageHelper($parentPath);
            $parent = $parentHelper->getOrCreatePage();

            // Create new Grav page
            $page = new Page;
            $page->parent($parent);

            // Set page location vars
            $page->name($this->getFilename());
            $page->folder($this->slug);
            // We use parent page's path, as the root will return absolute path folder for Grav instance
            $page->path($parent->path().'/'.$this->slug);

            // Add routing information.
            $this->grav['pages']->addPage($page, $this->route);

            // Set if Modular
            if ($this->slug) {
                $page->modularTwig($this->slug[0] == '_');
            }

            // Set to non-routable if not final destination
            if (!$this->template) {
                $page->routable(false);
            }

            // Set time vars
            $page->modified(time());

            // Save to file
            $page->save();
        }

        $this->page = $page;

        return $page;
    }

    public function getFilename()
    {
        if (!$this->template) {
            return null;
        }

        return $this->template . CONTENT_EXT;
    }
}
