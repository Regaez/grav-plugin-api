<?php
namespace GravApi\Resources;

use Grav\Common\Plugin;

/**
 * Class BaseHandler
 * @package GravApi\Handlers
 */
class PluginResource
{
    protected $plugin;

    public function __construct(Plugin $plugin)
    {
        $this->plugin = $plugin->config();
    }

    /**
     * Returns the plugin object as an array/json.
     * Also accepts an array of fields by which to filter.
     *
     * @param  [array] $fields optional
     * @return [array]
     */
    public function toJson($fields = null)
    {
        // Filter for requested fields
        if ( $fields ) {
            $data = [];

            foreach ($fields as $field) {
                if ( method_exists($this->plugin, $field) ) {
                    $data[$field] = $this->plugin->{$field}();
                }
            }

            return $data;
        }

        // Otherwise return everything
        return (array) $this->plugin;
    }
}
