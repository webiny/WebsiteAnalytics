<?php
/**
 * Webiny (http://www.webiny.com/)
 *
 * @link      http://www.webiny.com/ for the canonical source repository
 * @copyright Copyright (c) 2009-2016 Webiny LTD. (http://www.webiny.com)
 * @license   MIT
 */
namespace Webiny\WebsiteAnalytics\StatHandlers;

use Webiny\GeoIp\GeoIp;
use Webiny\WebsiteAnalytics\StatHandlerInterface;
use Webiny\WebsiteAnalytics\StatHandlerObject;

/**
 * Class Country
 * @package Webiny\WebsiteAnalytics\StatHandlers
 */
class Country implements StatHandlerInterface
{
    const NAME = 'country';

    /**
     * @var StatHandlerObject
     */
    private $statHandlerObject;

    /**
     * @var GeoIp
     */
    private $geoIp;


    /**
     * Base constructor.
     *
     * @param StatHandlerObject $statHandlerObject
     */
    public function __construct(StatHandlerObject $statHandlerObject)
    {
        $this->statHandlerObject = $statHandlerObject;
    }

    public function setGeoIpInstance(GeoIp $geoIp)
    {
        $this->geoIp = $geoIp;
    }

    /**
     * Returns the stat handler name.
     *
     * @return string
     */
    public function getName()
    {
        return self::NAME;
    }

    /**
     * Returns the stat handler value.
     *
     * @return string
     */
    public function getValue()
    {
        $location = $this->geoIp->getGeoIpLocation($this->statHandlerObject->getIp());
        if ($location) {
            return $location->getCountryCode();
        }
    }
}