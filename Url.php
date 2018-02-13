<?php
/**
 * Created by PhpStorm.
 * User: piotr
 * Date: 12.02.2018
 * Time: 16:02
 */

namespace UrlParser;


/**
 * Class Url
 * @package UrlParser
 */
class Url
{
    /**
     * @var
     */
    public $url;
    /**
     * @var
     */
    public $scheme;
    /**
     * @var
     */
    public $host;
    /**
     * @var
     */
    public $port;
    /**
     * @var
     */
    public $path;
    /**
     * @var
     */
    public $topDomain;
    /**
     * @var
     */
    public $domain;
    /**
     * @var
     */
    public $subdomains;
    /**
     * @var array
     */
    public $parameters = [];

}