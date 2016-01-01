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

    private $handlers = [
        'Webiny\WebsiteAnalytics\StatHandlers\Browser',
        'Webiny\WebsiteAnalytics\StatHandlers\Device',
        'Webiny\WebsiteAnalytics\StatHandlers\OperatingSystem',
        'Webiny\WebsiteAnalytics\StatHandlers\ReferrerDomain',
        'Webiny\WebsiteAnalytics\StatHandlers\ReferrerType'
    ];

    private $handlerInstances = [];


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
            $country = new StatHandlers\Country($this->statHandlerObject);
            $country->setGeoIpInstance($geoIp);

            $this->addStatHandler($country);
        }
    }

    public function setUserAgent($ua)
    {
        $this->statHandlerObject->setUserAgent($ua);
    }

    public function setIp($ip)
    {
        $this->statHandlerObject->setIp($ip);
    }

    public function setHeaders($headers)
    {
        $this->statHandlerObject->setHeaders($headers);
    }

    public function setTimestamp($time)
    {
        $this->analyticsDb->setTimestamp((int)$time);
    }

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

    public function deleteSession()
    {
        $this->httpSession()->delete('webiny_website_analytics');
    }

    public function addStatHandler(StatHandlerInterface $handler)
    {
        $this->handlerInstances[] = $handler;
    }

    public function query(array $dateRange)
    {
        return new Query($this->analyticsDb, $dateRange);
    }

    private function initiateHandlers()
    {
        foreach ($this->handlers as $h) {
            $this->handlerInstances[] = new $h($this->statHandlerObject);
        }
    }

    private function getSession()
    {
        $session = $this->httpSession()->get('webiny_website_analytics');
        if (empty($session)) {
            return false;
        }

        return json_decode($session, true);
    }

    private function logPageView()
    {
        $this->analyticsDb->log(self::STAT_PAGE_VIEW);
    }

    private function logUrlView($statHandlers)
    {
        $url = $this->httpRequest()->getCurrentUrl(true)->getPath();

        $log = $this->analyticsDb->log(self::STAT_URL_VIEW, $url);
        foreach ($statHandlers as $k => $v) {
            $log->addDimension($k, $v);
        }
    }

    private function logVisitor($statHandlers)
    {
        $log = $this->analyticsDb->log(self::STAT_VISITOR);
        foreach ($statHandlers as $k => $v) {
            $log->addDimension($k, $v);
        }
    }

    private function saveLog()
    {
        $this->analyticsDb->save();
    }

}