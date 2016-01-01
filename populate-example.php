<?php

require_once 'vendor/autoload.php';

$_SERVER['SERVER_NAME'] = 'www.webiny.com';

$geoProvider = new \Webiny\GeoIp\Provider\MaxMindGeoLite2\MaxMindGeoLite2(__DIR__ . '/test-config.yaml');
$geoIp = new \Webiny\GeoIp\GeoIp($geoProvider);

$mongo = new \Webiny\Component\Mongo\Mongo('127.0.0.1:27017', 'LoginFoo');
$a = new \Webiny\AnalyticsDb\AnalyticsDb($mongo);

$wa = new \Webiny\WebsiteAnalytics\WebsiteAnalytics($a, $geoIp);

$userAgents = [
    'Mozilla/5.0 (iPhone; CPU iPhone OS 6_0 like Mac OS X) AppleWebKit/536.26 (KHTML, like Gecko) Version/6.0 Mobile/10A5376e Safari/8536.25',
    'Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/47.0.2526.106 Safari/537.36',
    'Mozilla/5.0 (Windows NT 6.1; WOW64; rv:42.0) Gecko/20100101 Firefox/42.0',
    'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_11_1) AppleWebKit/601.2.7 (KHTML, like Gecko) Version/9.0.1 Safari/601.2.7',
    'Mozilla/5.0 (Windows NT 6.1; WOW64; Trident/7.0; rv:11.0) like Gecko',
    'Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:42.0) Gecko/20100101 Firefox/42.0',
    'Mozilla/5.0 (iPad; CPU OS 9_1 like Mac OS X) AppleWebKit/601.1.46 (KHTML, like Gecko) Version/9.0 Mobile/13B143 Safari/601.1',
    'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Ubuntu Chromium/45.0.2454.101 Chrome/45.0.2454.101 Safari/537.36',
    'Mozilla/5.0 (iPhone; U; CPU iPhone OS 5_1_1 like Mac OS X; en) AppleWebKit/534.46.0 (KHTML, like Gecko) CriOS/19.0.1084.60 Mobile/9B206 Safari/7534.48.3',
    'Mozilla/5.0 (Linux; U; Android 4.0.3; ko-kr; LG-L160L Build/IML74K) AppleWebkit/534.30 (KHTML, like Gecko) Version/4.0 Mobile Safari/534.30',
    'Mozilla/5.0 (Macintosh; U; Intel Mac OS X 10_6_7; da-dk) AppleWebKit/533.21.1 (KHTML, like Gecko) Version/5.0.5 Safari/533.21.1'
];

$ipList = [
    '23.92.128.123', // canada
    '46.188.128.31', // croatia
    '82.112.64.231', // iceland
    '62.85.64.131', // latvia
    '196.200.48.121', // mali
    '5.77.64.21', // italy
    '14.139.0.12', // india
    '46.23.160.21', // finland
    '101.98.0.213', // new zeland
    '5.10.144.101', // uk
];


for ($i = 0; $i <= 100000; $i++) {
    $page = '/page-' . rand(100, 1000) . '/';
    $_SERVER['REQUEST_URI'] = $page;
    \Webiny\Component\Http\Request::getInstance()->setCurrentUrl('http://www.webiny.com' . $page);

    $ua = $userAgents[array_rand($userAgents)];
    $ip = $ipList[array_rand($ipList)];

    $wa->setUserAgent($ua);
    $wa->setIp($ip);

    $time = time() - (rand(0, 365) * 86400);

    $wa->setTimestamp($time);

    $wa->saveStats();

    if (rand(1, 10) == 5) {
        $wa->deleteSession();
    }
}