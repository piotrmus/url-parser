<?php
/**
 * Created by PhpStorm.
 * User: piotr
 * Date: 12.02.2018
 * Time: 16:24
 */

namespace UrlParser;


/**
 * Class UrlValidator
 * @package UrlParser
 */
class UrlValidator
{

    public static $pattern = '/^(http|https):\/\/(([A-Z0-9][A-Z0-9_-]*)(\.[A-Z0-9][A-Z0-9_-]*)+)(?::\d{1,5})?(?:$|[?\/#])/i';


    /**
     * @param $string
     * @return bool
     */
    public static function validate($string)
    {
        if (!is_string($string) || strlen($string) > 2000) {
            return false;
        }
        return !!preg_match(self::$pattern, $string);
    }
}