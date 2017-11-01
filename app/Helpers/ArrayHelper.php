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
}
