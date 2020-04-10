<?php
namespace GravApi\Helpers;

/**
 * Class ArrayHelper
 * @package GravApi\Helpers
 */
class ArrayHelper
{
    public static function merge($current, $new)
    {
        $current = (array) $current;

        foreach ($new as $key => $value) {
            // if a value is null, we remove it from the current
            if ($value === null) {
                unset($current[$key]);
                continue;
            }

            // handle associative array current info:
            // recursively iterate through child arrays,
            // and update nested properties
            if (array_key_exists($key, $current) && is_array($value)) {
                $current[$key] = self::merge($current[$key], $value);
                continue;
            }

            // create new entry, as key doesn't exist
            // or value is a single field
            $current[$key] = $value;
        }

        return $current;
    }

    public static function asStringArray(array $array)
    {
        $filteredArray = [];

        foreach ($array as $item) {
            if (is_string($item)) {
                $filteredArray[] = $item;
            }
        }

        return $filteredArray;
    }

    /**
     * Intersects two arrays, returning an array containing only the shared items.
     * Unlike `array_intersect`, it will work for values which are also arrays.
     *
     * @param array $a
     * @param array $b
     * @return array An empty array will be returned if there is no intersection.
     */
    public static function intersect($a, $b)
    {
        if (!is_array($a) || !is_array($b)) {
            return array();
        }

        if (!empty($a)) {
            foreach ($a as $key => $value) {
                if (!isset($b[$key])) {
                    unset($a[$key]);
                } else {
                    if (serialize($b[$key]) !== serialize($value)) {
                        unset($a[$key]);
                    }
                }
            }

            return $a;
        }

        return array();
    }
}
