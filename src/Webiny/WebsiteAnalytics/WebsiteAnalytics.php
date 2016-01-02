<?php
/**
 * Webiny (http://www.webiny.com/)
 *
 * @link      http://www.webiny.com/ for the canonical source repository
 * @copyright Copyright (c) 2009-2016 Webiny LTD. (http://www.webiny.com)
 * @license   MIT
 */
namespace Webiny\WebsiteAnalytics;

use Webiny\AnalyticsDb\AnalyticsDb;
use Webiny\Component\Http\HttpTrait;
use Webiny\GeoIp\GeoIp;

/**
 * Class WebsiteAnalytics
 * @package Webiny\WebsiteAnalytics
 */
class WebsiteAnalytics
{
    use HttpTrait;

    const STAT_PAGE_VIEW = 'page_view';
    const STAT_URL_VIEW = 'url_view';
    const STAT_VISITOR = 'visitor';

    /**
     * @var AnalyticsDb
     */
    private $analyticsDb;

    /**
     * @var StatHandlerObject
     */
    private $statHandlerObject;

    /**
     * @var array List of pre-built stat handlers.
     */
    private $handlers = [
        'Webiny\WebsiteAnalytics\StatHandlers\Browser',
        'Webiny\WebsiteAnalytics\StatHandlers\Device',
        'Webiny\WebsiteAnalytics\StatHandlers\OperatingSystem',
        'Webiny\WebsiteAnalytics\StatHandlers\ReferrerDomain',
        'Webiny\WebsiteAnalytics\StatHandlers\ReferrerType'
    ];

    /**
     * @var array List of stat handler instances.
     */
    private $handlerInstances = [];


    /**
     * Base constructor.
     *
     * @param AnalyticsDb $analyticsDb AnalyticsDb instance.
     * @param GeoIp|null  $geoIp       GeoIp instance. Only required if you wish to store visitor country information.
     */
    public function __construct(AnalyticsDb $analyticsDb, GeoIp $geoIp = null)
    {
        $this->analyticsDb = $analyticsDb;

        // create stat handler object
        $this->statHandlerObject = new StatHandlerObject($this->httpRequest()->server()->httpUserAgent(),
            $this->httpRequest()->header(), $this->httpRequest()->getClientIp());

        // initiate handlers
        $this->initiateHandlers();

        // if we have GeoIp instance, add the Country handler
        if (is_object($geoIp)) {
            $country = $this->addStatHandler('Webiny\WebsiteAnalytics\StatHandlers\Country');
            $country->setGeoIpInstance($geoIp);
        }
    }

    /**
     * Set user agent, otherwise use the current user agent from the http request.
     *
     * @param string $ua User-agent string.
     */
    public function setUserAgent($ua)
    {
        $this->statHandlerObject->setUserAgent($ua);
    }

    /**
     * Set the IP address, otherwise use the current IP address from the client.
     *
     * @param string $ip IPv4 or IPv6 address.
     */
    public function setIp($ip)
    {
        $this->statHandlerObject->setIp($ip);
    }

    /**
     * Set http request headers, otherwise use the headers from the current http request.
     *
     * @param array $headers List of http request headers.
     */
    public function setHeaders($headers)
    {
        $this->statHandlerObject->setHeaders($headers);
    }

    /**
     * Set the timestamp for the analytics db, otherwise use the current timestamp.
     *
     * @param int $time Unix timestamp.
     */
    public function setTimestamp($time)
    {
        $this->analyticsDb->setTimestamp((int)$time);
    }

    /**
     * Save the website analytics data.
     *
     * @return bool
     */
    public function saveStats()
    {
        $session = $this->getSession();
        if ($session) {
            $this->logPageView();
            $this->logUrlView($session);
            $this->saveLog();

            return true;
        }

        // get stat handlers
        $statHandlers = [];

        /**
         * @type $i StatHandlerInterface
         */
        foreach ($this->handlerInstances as $i) {
            try {
                $result = $i->getValue();
                if ($result !== false) {
                    $statHandlers[$i->getName()] = $result;
                }
            } catch (\Exception $e) {
                // skip handler
            }
        }

        $this->logPageView();
        $this->logUrlView($statHandlers);
        $this->logVisitor($statHandlers);
        $this->saveLog();

        $this->httpSession()->save('webiny_website_analytics', json_encode($statHandlers));

        return true;
    }

    /**
     * Deletes the current session information.
     */
    public function deleteSession()
    {
        $this->httpSession()->delete('webiny_website_analytics');
    }

    /**
     * Add an additional stats handler.
     *
     * @param string $handler FQN of the custom stat handler class.
     *
     * @throws WebsiteAnalyticsException
     *
     * @return StatHandlerInterface instance of the given stat handler.
     */
    public function addStatHandler($handler)
    {
        $handlerInstance = new $handler($this->statHandlerObject);

        if (!($handlerInstance instanceof \Webiny\WebsiteAnalytics\StatHandlerInterface)) {
            throw new WebsiteAnalyticsException('Stat handler must implement "\Webiny\WebsiteAnalytics\StatHandlerInterface".');
        }

        $this->handlerInstances[] = $handlerInstance;

        return $handlerInstance;
    }

    /**
     * Query the analytics data.
     *
     * @param array $dateRange Date range [unixFromDate, unixToDate].
     *
     * @return Query
     */
    public function query(array $dateRange)
    {
        return new Query($this->analyticsDb, $dateRange);
    }

    /**
     * Initiates the current built-in handlers.
     */
    private function initiateHandlers()
    {
        foreach ($this->handlers as $h) {
            $this->handlerInstances[] = new $h($this->statHandlerObject);
        }
    }

    /**
     * Checks if the session data exists. If it exists, it decodes the data and returns the session info array.
     * If the session doesn't exist, false is returned.
     *
     * @return bool|array
     */
    private function getSession()
    {
        $session = $this->httpSession()->get('webiny_website_analytics');
        if (empty($session)) {
            return false;
        }

        return json_decode($session, true);
    }

    /**
     * Log a page view.
     *
     * @throws \Webiny\AnalyticsDb\AnalyticsDbException
     */
    private function logPageView()
    {
        $this->analyticsDb->log(self::STAT_PAGE_VIEW);
    }

    /**
     * Log a url view.
     *
     * @param array $statHandlers List of stat handlers that will be added to the analytics db as a dimension.
     *
     * @throws \Webiny\AnalyticsDb\AnalyticsDbException
     */
    private function logUrlView($statHandlers)
    {
        $url = $this->httpRequest()->getCurrentUrl(true)->getPath();

        $log = $this->analyticsDb->log(self::STAT_URL_VIEW, $url);
        foreach ($statHandlers as $k => $v) {
            $log->addDimension($k, $v);
        }
    }

    /**
     * Log a visitor.
     *
     * @param array $statHandlers List of stat handlers that will be added to the analytics db as a dimension.
     *
     * @throws \Webiny\AnalyticsDb\AnalyticsDbException
     */
    private function logVisitor($statHandlers)
    {
        $log = $this->analyticsDb->log(self::STAT_VISITOR);
        foreach ($statHandlers as $k => $v) {
            $log->addDimension($k, $v);
        }
    }

    /**
     * Save the analytics data.
     */
    private function saveLog()
    {
        $this->analyticsDb->save();
    }

}