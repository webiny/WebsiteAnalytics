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
use Webiny\WebsiteAnalytics\StatHandlerInterface;
use Webiny\WebsiteAnalytics\StatHandlerObject;

class ReferrerType implements StatHandlerInterface
{
    use HttpTrait;

    const NAME = 'referrer_type';

    private $statHandlerObject;

    private $searchEngines = [
        '/\.google\./',
        '/\.bing\./',
        '/\.yahoo\./',
        '/\.ask\./',
        '/\.aol\./',
        '/\.wow\./',
        '/\.webcrawler\./',
        '/\.mywebsearch\./',
        '/\.infospace\./',
        '/\.info\./',
        '/\.duckduckgo\./',
        '/\.contenko\./',
        '/\.dogpile\./',
        '/\.alhea\./',
        '/\.ixquick\./',
        '/\.blekko\./',
        '/\.wolframalpha\./',
        '/^archive\./',
        '/\.chacha\./',
        '/\.baidu\./',
        '/\.excite\./',
        '/\.yandex\./',
        '/\.lycos\./'
    ];

    private $socialSites = [
        '/facebook\.com/',
        '/plus\.google\.com/',
        '/plus\.url\.google\.com/',
        '/twitter\.com/',
        '/linkedin\.com/',
        '/hootsuite\.com/',
        '/t\.co/',
        '/pinterest\.com/',
        '/stumbleupon\.com/',
        '/youtube\.com/',
        '/instagram\.com/',
        '/tumblr\.com/',
        '/flickr\.com/',
        '/reddit\.com/',
        '/vk\.com/',
        '/vine\.co/',
        '/meetup\.com/',
        '/tagged\.com/',
        '/ask\.fm/',
        '/meetme\.com/',
        '/classmates\.com/',
        '/xing\.com/',
        '/renren-inc\.com/',
        '/disqus\.com/',
        '/snapchat\.com/',
        '/twoo\.com/',
        '/mymfb\.com/',
        '/whatsapp\.com/',
        '/medium\.com/'
    ];

    private $socialUtmSource = [
        'facebook',
        'fb',
        'linkedin',
        'twitter',
        'instagram',
        'pinterest',
        'youtube',
        'gplus',
        'googleplus',
        'google-plus',
        'instagram',
        'tumblr',
        'reddit',
        'stumbleupon',
        'medium',
        'hootsuite'
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
        $refDomain = new ReferrerDomain($this->statHandlerObject);
        $host = $refDomain->getValue();

        if (!$host) {
            return 'direct';
        } else if ($this->isSearch($host)) {
            return 'search';
        } else if ($this->isSocial($host)) {
            return 'social';
        } else {
            return 'referral';
        }
    }

    private function isSearch($host)
    {
        foreach ($this->searchEngines as $e) {
            if (preg_match($e, $host)) {
                return true;
            }
        }

        return false;
    }

    private function isSocial($host)
    {
        foreach ($this->socialSites as $ss) {
            if (preg_match($ss, $host)) {
                return true;
            }
        }

        $utmSource = $this->httpRequest()->query('utm_source');
        if (!empty($utmSource)) {
            foreach ($this->socialUtmSource as $utm) {
                if (preg_match($utm, $utmSource)) {
                    return true;
                }
            }
        }

        return false;
    }
}