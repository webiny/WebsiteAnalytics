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
 * Class OperatingSystem
 * @package Webiny\WebsiteAnalytics\StatHandlers
 */
class OperatingSystem implements StatHandlerInterface
{
    const NAME = 'os';

    /**
     * @var StatHandlerObject
     */
    private $statHandlerObject;

    // first match wins
    private $operatingSystems = [
        'win'         => 'windows',
        'macintosh'   => 'osx',
        'osx'         => 'osx',
        'mac_powerpc' => 'osx',
        'ubuntu'      => 'linux',
        'iphone'      => 'ios',
        'ipad'        => 'ios',
        'ipod'        => 'ios',
        'android'     => 'android',
        'linux'       => 'linux',
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

        foreach ($this->operatingSystems as $pattern => $name) {
            if (preg_match('/' . $pattern . '/', $ua)) {
                return $name;
            }
        }

        return 'other';
    }
}