<?php
/**
 * Webiny (http://www.webiny.com/)
 *
 * @link      http://www.webiny.com/ for the canonical source repository
 * @copyright Copyright (c) 2009-2016 Webiny LTD. (http://www.webiny.com)
 * @license   MIT
 */
namespace Webiny\WebsiteAnalytics;

/**
 * Class StatHandlerObject
 * @package Webiny\WebsiteAnalytics
 */
class StatHandlerObject
{
    /**
     * @var string User-agent string.
     */
    private $userAgent;

    /**
     * @var array List of http request headers.
     */
    private $headers;

    /**
     * @var string Client ip address.
     */
    private $ip;


    /**
     * Base constructor.
     *
     * @param string $userAgent User-agent string.
     * @param array  $headers   List of http request headers.
     * @param string $ip        Client ip address.
     */
    public function __construct($userAgent, $headers, $ip)
    {
        $this->userAgent = $userAgent;
        $this->headers = $headers;
        $this->ip = $ip;
    }

    /**
     * Returns the user-agent string.
     *
     * @return string
     */
    public function getUserAgent()
    {
        return $this->userAgent;
    }

    /**
     * Set the user-agent string.
     *
     * @param string $ua User-agent string.
     */
    public function setUserAgent($ua)
    {
        $this->userAgent = $ua;
    }

    /**
     * Return the http request headers.
     *
     * @return array
     */
    public function getHeaders()
    {
        return $this->headers;
    }

    /**
     * Set the http request headers.
     *
     * @param array $headers List of http request headers.
     */
    public function setHeaders($headers)
    {
        $this->headers = $headers;
    }

    /**
     * Return the client ip.
     *
     * @return string
     */
    public function getIp()
    {
        return $this->ip;
    }

    /**
     * Set the client ip.
     *
     * @param string $ip Client ip.
     */
    public function setIp($ip)
    {
        $this->ip = $ip;
    }
}