<?php
/**
 * Created by PhpStorm.
 * User: piotr
 * Date: 12.02.2018
 * Time: 16:08
 */

namespace UrlParser\Tests;


use PHPUnit\Framework\TestCase;
use UrlParser\Url;
use UrlParser\UrlParser;

/**
 * Class UrlParserTest
 * @package UrlParser\Tests
 */
class UrlParserTest extends TestCase
{


    /**
     * @expectedException \InvalidArgumentException
     */
    public function testParseInvalidUrl()
    {
        $this->expectException(\InvalidArgumentException::class);
        return UrlParser::parse('invalid');
    }


    /**
     * @return null|Url
     */
    public function testParseValidUrl()
    {
        $url = UrlParser::parse('https://sub.codehat.krakow.pl:8080/about?first_parameter=first_parameter-value&second-parameter=second-parameter-value');

        $this->assertInstanceOf(Url::class, $url, "Url is not valid.");

        return $url;
    }

    /**
     * @depends testParseValidUrl
     * @param Url $url
     */
    public function testParseScheme(Url $url)
    {
        $this->assertEquals('https', $url->scheme);
    }

    /**
     * @depends testParseValidUrl
     * @param Url $url
     */
    public function testParseDomain(Url $url)
    {
        $this->assertEquals('codehat.krakow.pl', $url->domain);
        $this->assertEquals('krakow.pl', $url->topDomain);
        $this->assertTrue(in_array('sub', $url->subdomains));
    }

    /**
     * @depends testParseValidUrl
     * @param Url $url
     */
    public function testParsePort(Url $url)
    {
        $this->assertEquals(8080, $url->port);
    }

    /**
     * @depends testParseValidUrl
     * @param Url $url
     */
    public function testParseParameters(Url $url)
    {
        $this->assertArrayHasKey('second-parameter', $url->parameters);
    }
}