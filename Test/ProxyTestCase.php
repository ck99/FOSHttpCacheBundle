<?php

namespace FOS\HttpCacheBundle\Test;

use FOS\HttpCache\Test\PHPUnit\IsCacheHitConstraint;
use FOS\HttpCache\Test\PHPUnit\IsCacheMissConstraint;
use Guzzle\Http\Message\Response;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ProxyTestCase extends WebTestCase
{
    /**
      * Assert a cache miss
      *
      * @param Response $response
      * @param string   $message  Test failure message (optional)
      */
     public function assertMiss(Response $response, $message = null)
     {
         self::assertThat($response, self::isCacheMiss(), $message);
     }
 
     /**
      * Assert a cache hit
      *
      * @param Response $response
      * @param string   $message  Test failure message (optional)
      */
     public function assertHit(Response $response, $message = null)
     {
         self::assertThat($response, self::isCacheHit(), $message);
     }
 
     public static function isCacheHit()
     {
         return new IsCacheHitConstraint(self::getCacheDebugHeader());
     }
 
     public static function isCacheMiss()
     {
         return new IsCacheMissConstraint(self::getCacheDebugHeader());
     }

    /**
     * Start and clear caching proxy server if test is annotated with @clearCache
     */
    protected function setUp()
    {
        $annotations = \PHPUnit_Util_Test::parseTestMethodAnnotations(
            get_class($this),
            $this->getName()
        );

        if (isset($annotations['class']['clearCache'])
            || isset($annotations['method']['clearCache'])
        ) {
            $this->getProxy()->clear();
        }
    }

    protected function getProxy()
    {
        return static::$kernel->getContainer()->get('fos_http_cache.default_proxy_server');
    }

    /**
     * @return string
     */
    protected static function getCacheDebugHeader()
    {
        return static::$kernel->getContainer()->getParameter('fos_http_cache.debug_header');
    }

    /**
     * Get default caching proxy client
     *
     * @return \FOS\HttpCache\ProxyClient\ProxyClientInterface
     */
    protected function getProxyClient()
    {
        return static::$kernel->getContainer()->get('fos_http_cache.default_proxy_client');
    }

    /**
     * Get caching proxy test client
     *
     * @return \Guzzle\Http\Client
     */
    protected function getTestClient()
    {
        return static::$kernel->getContainer()->get('fos_http_cache.proxy.default_test_client');
    }
}
