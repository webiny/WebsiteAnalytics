<?php
/**
 * Webiny (http://www.webiny.com/)
 *
 * @link      http://www.webiny.com/ for the canonical source repository
 * @copyright Copyright (c) 2009-2016 Webiny LTD. (http://www.webiny.com)
 * @license   MIT
 */
namespace Webiny\WebsiteAnalytics;

interface StatHandlerInterface
{
    /**
     * Base constructor.
     *
     * @param StatHandlerObject $statHandlerObject
     */
    public function __construct(StatHandlerObject $statHandlerObject);

    /**
     * Returns the stat handler name.
     *
     * @return string
     */
    public function getName();

    /**
     * Returns the stat handler value.
     *
     * @return string
     */
    public function getValue();
}