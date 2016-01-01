<?php
/**
 * Webiny (http://www.webiny.com/)
 *
 * @link      http://www.webiny.com/ for the canonical source repository
 * @copyright Copyright (c) 2009-2016 Webiny LTD. (http://www.webiny.com)
 * @license   MIT
 */
namespace Webiny\WebsiteAnalytics;

class StatHandlerObject
{
    private $userAgent;
    private $headers;
    private $ip;

    public function __construct($userAgent, $headers, $ip)
    {
        $this->userAgent = $userAgent;
        $this->headers = $headers;
        $this->ip = $ip;
    }

    public function getUserAgent()
    {
        return $this->userAgent;
    }

    public function setUserAgent($ua)
    {
        $this->userAgent = $ua;
    }

    public function getHeaders()
    {
        return $this->headers;
    }

    public function setHeaders($headers)
    {
        $this->headers = $headers;
    }

    public function getIp()
    {
        return $this->ip;
    }

    public function setIp($ip)
    {
        $this->ip = $ip;
    }
}