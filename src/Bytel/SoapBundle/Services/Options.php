<?php

namespace Bytel\SoapBundle\Services;
/**
 * 
 * @author noureddineelmoulaoui
 *
 */
abstract class Options
{
    /**
     * Apply options to $object
     *
     * @param object $object
     * @param array $options
     */
    public static function setOptions($object, $options)
    {
        if ($options instanceof stdClass) {
            $options = (array) $options;
        }

        foreach ($options as $key => $value) {
            $value = is_string($value) ? trim($value) : $value;
            $method = "set" . self::_normalizeKey($key);
            if (method_exists($object, $method)) {
                $object->$method($value);
            }
        }
    }

    /**
     * Normalize $key to lowercamelcase
     *
     * @param string $key key to normalize
     * @return string Normalized key
     */
    protected static function _normalizeKey($key)
    {
        $option = str_replace('_', ' ', strtolower($key));
        $option = str_replace(' ', '', ucwords($option));
        return $option;
    }
}
