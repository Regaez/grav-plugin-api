<?php
namespace GravApi\Helpers;

/**
 * Class TaxonomyHelper
 * @package GravApi\Helpers
 */
class TaxonomyHelper
{
    /**
     * Merges two taxonomy arrays together, removing any duplicate values.
     *
     * @param array $a
     * @param array $b
     * @return array
     */
    public static function merge(array $a, array $b)
    {
        $result = array();

        foreach (array_merge_recursive($a, $b) as $key => $value) {
            $result[$key] = array_unique($value);
        }

        return $result;
    }

    /**
     * Intersects two taxonomy arrays, returning an array containing only the shared taxonomy values.
     *
     * @param array $a
     * @param array $b
     * @return array An empty array will be returned if there is no intersection.
     */
    public static function intersect(array $a, array $b)
    {
        $result = array();

        $matchingKeys = array_filter($a, function ($key) use ($b) {
            return array_key_exists($key, $b);
        }, ARRAY_FILTER_USE_KEY);


        foreach ($matchingKeys as $key => $value) {
            $intersect = array_intersect($value, $b[$key]);
            if (count($intersect) > 0) {
                $result[$key] = $intersect;
            }
        }

        return $result;
    }

    /**
     * Determines if two taxonomy arrays share any values
     *
     * @param array $a
     * @param array $b
     * @return bool
     */
    public static function hasIntersect($a, $b)
    {
        return count(self::intersect($a, $b)) > 0;
    }
}
