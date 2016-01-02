<?php


use Webiny\WebsiteAnalytics\StatHandlers\Browser;
use Webiny\WebsiteAnalytics\StatHandlers\Country;

require_once 'vendor/autoload.php';

$_SERVER['SERVER_NAME'] = 'www.webiny.com';

$geoProvider = new \Webiny\GeoIp\Provider\MaxMindGeoLite2\MaxMindGeoLite2(__DIR__ . '/test-config.yaml');
$geoIp = new \Webiny\GeoIp\GeoIp($geoProvider);

$mongo = new \Webiny\Component\Mongo\Mongo('127.0.0.1:27017', 'LoginFoo');
$a = new \Webiny\AnalyticsDb\AnalyticsDb($mongo);

$wa = new \Webiny\WebsiteAnalytics\WebsiteAnalytics($a, $geoIp);


# queries
$stats = $wa->query(\Webiny\AnalyticsDb\DateHelper::rangeLast90Days());

// visitors (unique)
$result = $stats->visitorsSum();
$result = $stats->visitorsByDay();
$result = $stats->visitorsByMonth();


// dimensions (based on visitors)
$result = $stats->visitorsDimensionSum(Browser::NAME, 'safari');
$result = $stats->visitorsDimensionByDay(Country::NAME, 'HR');


// page views (not unique)
$result = $stats->pageViewsSum();
$result = $stats->pageViewsByDay();
$result = $stats->pageViewsByMonth();

// url - get top 10 pages
$result = $stats->urlSum();


// url - get total number of views for the given page
$result = $stats->urlSum('/page-110/');
$result = $stats->urlByDay('/page-860/');
$result = $stats->urlByMonth('/page-860/');

$result = $stats->urlDimensionSum('/page-110/', Browser::NAME);
$result = $stats->urlDimensionByDay('/page-110/', Browser::NAME);
die(print_r($result));
$result = $stats->urlDimensionByDay('/page-110/', Browser::NAME, 'chrome');
