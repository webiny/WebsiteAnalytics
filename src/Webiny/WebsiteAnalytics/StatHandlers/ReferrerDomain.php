<?php
/**
 * Webiny (http://www.webiny.com/)
 *
 * @link      http://www.webiny.com/ for the canonical source repository
 * @copyright Copyright (c) 2009-2016 Webiny LTD. (http://www.webiny.com)
 * @license   MIT
 */
namespace Webiny\WebsiteAnalytics\StatHandlers;

use Webiny\Component\Http\HttpTrait;
use Webiny\Component\StdLib\StdObjectTrait;
use Webiny\WebsiteAnalytics\StatHandlerInterface;
use Webiny\WebsiteAnalytics\StatHandlerObject;

/**
 * Class ReferrerDomain
 * @package Webiny\WebsiteAnalytics\StatHandlers
 */
class ReferrerDomain implements StatHandlerInterface
{
    use HttpTrait, StdObjectTrait;

    const NAME = 'referrer_domain';

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
        $ref = $this->httpRequest()->server()->httpReferer();

        if ($ref) {
            $url = $this->url($ref);

            return $url->getHost();
        }

        return false;
    }
}