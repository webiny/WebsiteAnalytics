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

class Browser implements StatHandlerInterface
{
    const NAME = 'browser';

    private $statHandlerObject;

    // first match wins
    private $browsers = [
        'opera'    => 'opera',
        'chromium' => 'chrome',
        'chrome'   => 'chrome',
        'safari'   => 'safari',
        'msie'     => 'ie',
        'trident'  => 'ie',
        'edge'     => 'edge',
        'firefox'  => 'firefox'
    ];

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

        $ua = strtolower($this->statHandlerObject->getUserAgent());

        foreach ($this->browsers as $pattern => $name) {
            if (preg_match('/' . $pattern . '/', $ua)) {
                return $name;
            }
        }

        return 'other';
    }
}