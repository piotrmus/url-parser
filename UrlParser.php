<?php

namespace UrlParser;


/**
 * Class UrlParser
 * @package UrlParser
 */
class UrlParser
{
    /**
     *
     */
    const VERSION = '0.0.1';

    /**
     * @var string
     */
    public static $suffixListUrl = 'https://publicsuffix.org/list/public_suffix_list.dat';

    /**
     * @return string
     */
    public static function getResourceDirectory()
    {
        return __DIR__ . '/resources';
    }

    /**
     * @return string
     */
    public static function getCacheFile()
    {
        return self::getResourceDirectory() . '/cache.ser';
    }

    /**
     * @return array
     */
    private static function downloadSuffixList()
    {
        $listString = file_get_contents(self::$suffixListUrl);

        $re = '/^[^\/\/\n]{2,}$/m';

        preg_match_all($re, $listString, $domains, PREG_PATTERN_ORDER, 0);

        return self::parseDomainList($domains[0]);
    }

    /**
     * @param $domains
     * @return array
     */
    private static function parseDomainList($domains)
    {
        $parsed = [];
        foreach ($domains as $domain) {
            $currentArr = &$parsed;
            $parsedDomain = array_reverse(explode('.', $domain));
            for ($deep = 0; $deep < count($parsedDomain); $deep++) {
                if ($parsedDomain[$deep] == '*') {
                    continue;
                }
                if (!isset($currentArr[$parsedDomain[$deep]])) {
                    $currentArr[$parsedDomain[$deep]] = [];
                }
                $currentArr = &$currentArr[$parsedDomain[$deep]];
            }
        }
        return $parsed;
    }

    /**
     * @param $domainList
     */
    private static function saveCache($domainList)
    {
        if (!file_exists(self::getResourceDirectory())) {
            mkdir(self::getResourceDirectory());
        }
        $data = [
            'create_time' => time(),
            'data' => $domainList
        ];
        file_put_contents(self::getCacheFile(), serialize($data));
    }

    /**
     * @return array
     */
    public static function createCache()
    {
        $domainList = self::downloadSuffixList();
        self::saveCache($domainList);
        return $domainList;
    }

    /**
     * @return bool
     */
    public static function cacheExist()
    {
        return file_exists(self::getCacheFile());
    }

    /**
     * @return array
     */
    public static function getDomainList()
    {
        if (self::cacheExist()) {
            $data = unserialize(file_get_contents(self::getCacheFile()));
            return $data['data'];
        }
        return self::createCache();
    }

    /**
     * @param $hostname
     * @return null|Url
     * @throws \InvalidArgumentException
     */
    public static function parse($hostname)
    {
        if (!UrlValidator::validate($hostname)) {
            throw new \InvalidArgumentException('Url is not valid.');
        }

        $domainNamesList = self::getDomainList();
        $parsedUrl = parse_url($hostname);

        $host = array_reverse(explode(".", $parsedUrl['host']));

        $subDomains = [];
        $domainNames = [];
        $currentArray = &$domainNamesList;

        for ($deep = 0; $deep < count($host); $deep++) {
            if (isset($currentArray[$host[$deep]])) {
                $domainNames[] = $host[$deep];
                $currentArray = &$domainNamesList[$host[$deep]];
            } else {
                $subDomains[] = $host[$deep];
            }
        }

        if (count($subDomains) == 0) {
            $subDomains[] = $domainNames[count($domainNames) - 1];
            unset($domainNames[count($domainNames) - 1]);
        }

        $url = new Url();

        if(isset($parsedUrl['query'])){
            parse_str($parsedUrl['query'], $output);
            $url->parameters = $output;
        }

        $url->scheme = $parsedUrl['scheme'];
        $url->host = $parsedUrl['host'];
        $url->path = $parsedUrl['path'];
        $url->url = $hostname;
        $url->port = isset($parsedUrl['port']) ? $parsedUrl['port'] : null;
        $url->topDomain = implode('.', array_reverse($domainNames));
        $url->domain = $subDomains[0] . '.' . $url->topDomain;
        $url->subdomains = $subDomains;

        return $url;
    }
}