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

/**
 * Class ReferrerType
 * @package Webiny\WebsiteAnalytics\StatHandlers
 */
class ReferrerType implements StatHandlerInterface
{
    use HttpTrait;

    const NAME = 'referrer_type';

    /**
     * @var StatHandlerObject
     */
    private $statHandlerObject;

    /**
     * @var array List of most popular search engines.
     */
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

    /**
     * @var array List of most popular social network sites.
     */
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
        '/medium\.com/',
        '/producthunt\.com/',
        '/news\.ycombinator\.com/'
    ];

    /**
     * @var array Some common utm_source identificators for social networks.
     */
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
        'hootsuite',
        'producthunt'
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

        if (!$host || preg_match('/' . preg_quote($host, '/') . '/',
                $this->httpRequest()->getCurrentUrl(true)->getHost())
        ) {
            return 'direct';
        } else if ($this->isSearch($host)) {
            return 'search';
        } else if ($this->isSocial($host)) {
            return 'social';
        } else {
            return 'referral';
        }
    }

    /**
     * Check if the referrer is a search engine.
     *
     * @param string $host Referrer host.
     *
     * @return bool
     */
    private function isSearch($host)
    {
        foreach ($this->searchEngines as $e) {
            if (preg_match($e, $host)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Check if the referrer is a social network.
     *
     * @param string $host Referrer host.
     *
     * @return bool
     */
    private function isSocial($host)
    {
        foreach ($this->socialSites as $ss) {
            if (preg_match($ss, $host)) {
                return true;
            }
        }

        $queryRef = ['utm_source', 'ref'];

        foreach($queryRef as $qr){
            $utmSource = $this->httpRequest()->query($qr);
            if (!empty($utmSource)) {
                foreach ($this->socialUtmSource as $utm) {
                    if (preg_match($utm, $utmSource)) {
                        return true;
                    }
                }
            }
        }

        return false;
    }
}