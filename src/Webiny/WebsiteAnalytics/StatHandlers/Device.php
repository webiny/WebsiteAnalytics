<?php
/**
 * Webiny (http://www.webiny.com/)
 *
 * @link      http://www.webiny.com/ for the canonical source repository
 * @copyright Copyright (c) 2009-2016 Webiny LTD. (http://www.webiny.com)
 * @license   MIT
 */
namespace Webiny\WebsiteAnalytics\StatHandlers;

use Webiny\WebsiteAnalytics\StatHandlerInterface;
use Webiny\WebsiteAnalytics\StatHandlerObject;

/**
 * Class Device
 * @package Webiny\WebsiteAnalytics\StatHandlers
 */
class Device implements StatHandlerInterface
{
    const NAME = 'device';

    /**
     * @var StatHandlerObject
     */
    private $statHandlerObject;


    /**
     * Base constructor.
     *
     * @param StatHandlerObject $statHandlerObject
     */
    public function __construct(StatHandlerObject $statHandlerObject)
    {
        $this->statHandlerObject = $statHandlerObject;
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
        $detect = new \Mobile_Detect($this->statHandlerObject->getHeaders(), $this->statHandlerObject->getUserAgent());
        if ($detect->isMobile() && !$detect->isTablet()) {
            return 'mobile';
        } elseif ($detect->isTablet()) {
            return 'tablet';
        }

        return 'desktop';
    }
}