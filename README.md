Website Analytics
=================

This component helps you track your website visitors and some common attributes around them.

The component tracks 3 different metrics:
- `visitors` => unique website visitors
- `page views` => non-unique website visitors
- `url views` => page view metrics for a particular url (not unique)

For `visitors` and `url views` additional attributes are tracked:
- `browser` => which browsers did the visitors use, eg, `chrome`, `firefox` .. etc
- `country` => from which country did the visitors come
- `device` => which device did they use, `mobile`, `tablet` or `desktop`
- `os` => which operating system they have
- `referrer domain` => from which domain did they come to your website
- `referrer type` => did they come from a search engine, social network, some other referral or is it a direct visit

## Setup

### Dependencies

The component requires an instance of [`Webiny/AnalyticsDb`](../AnalyticsDb). Optionally, if you wish to track the `country` data, an instance
of [`Webiny/GeoIp`](../GeoIp) is required.

### Tracking
To track the visitor data, you have to create the instance of `WebsiteAnalytics` and call the `saveStats` method, like so:

```php
// get mongo instance for AnalyticsDb
$mongo = new \Webiny\Component\Mongo\Mongo('127.0.0.1:27017', 'MyDatabase');

// create AnalyticsDb instance
$a = new \Webiny\AnalyticsDb\AnalyticsDb($mongo);

// create WebsiteAnalytics instance
$wa = new \Webiny\WebsiteAnalytics\WebsiteAnalytics($a);

// save the stats
$wa->saveStats();
```

In order to track the `country` data, you need to also have the `GeoIp` instance:

``` php
// GeoIp provider
$geo = new \Webiny\GeoIp\GeoIp(new \Webiny\GeoIp\Provider\FreeGeoIp\FreeGeoIp());

$mongo = new \Webiny\Component\Mongo\Mongo('127.0.0.1:27017', 'MyDatabase');
$a = new \Webiny\AnalyticsDb\AnalyticsDb($mongo);


$wa = new \Webiny\WebsiteAnalytics\WebsiteAnalytics($a, $geo);
$wa->saveStats();
```

### Query data

In order to query your data, you need to call the `query` method, which will return an instance of `Query` class.
The `query` method requires 1 parameter and that is the time range within which the data should be queried.
The date range is an array with 2 unix timestamps. Optionally you can use `Webiny\AnalyticsDb\DateHelper` class to get some commonly used date ranges, like `lastMonth`, `thisWeek` ... etc. 

```php
$wa = new \Webiny\WebsiteAnalytics\WebsiteAnalytics($a, $geoIp);
// query between two dates
$stats = $wa->query([1446336000, 1451606400]);

// optionally: use the DateHelper class
$stats = $wa->query(\Webiny\AnalyticsDb\DateHelper::rangeLast90Days());
```

Once you have the `Query` instance, which is returned by the `query` method, you can access the following methods to query your data:

#### `visitorSum`

Return the total number of unique visitors for the given period.

```php
$stats->visitorsSum();
```

Returns a number with a total number of visitors.

```text
10342
```

#### `visitorsByDay`

Returns the total number of visitors, per day for the given period in form of an array.

```php
$stats->visitorsByDay();
```

The `_id` field represents the day timestamp, and the `totalCount` represents the number of visitors for that day.

```text
Array
(
    [0] => Array
        (
            [_id] => 1443916800
            [totalCount] => 20
        )

    [1] => Array
        (
            [_id] => 1444003200
            [totalCount] => 23
        )

    [2] => Array
        (
            [_id] => 1444089600
            [totalCount] => 22
        )
    ...
)
```

#### `visitorsByMonth`

Returns the total number of visitors, per month for the given period in form of an array.


```php
$stats->visitorsByMonth();
```

The `_id` field represents the day timestamp, and the `totalCount` represents the number of visitors for that day.

```text
Array
(
    [0] => Array
        (
            [_id] => 1446336000
            [totalCount] => 797
        )

    [1] => Array
        (
            [_id] => 1448928000
            [totalCount] => 839
        )

    [2] => Array
        (
            [_id] => 1451606400
            [totalCount] => 42
        )

)
```

#### `visitorsDimensionSum`

Dimensions are attributes like `browser`, `country`, `device`, and other prior mentioned attributes on the top of the page.

The `visitorsDimensionSum` method returns a sum of a defined dimension value, of your visitors, for the given time period.

The method takes a dimension name, and optionally a dimension value. If you only provide a dimension name, the results will be grouped by different dimension values, like so:

```php
$stats->visitorsDimensionSum(Webiny\WebsiteAnalytics\StatHandlers\Browser::NAME);
```

Result: 

```text
(
    [0] => Array
        (
            [_id] => Array
                (
                    [name] => browser
                    [value] => safari
                )

            [totalCount] => 1284
        )

    [1] => Array
        (
            [_id] => Array
                (
                    [name] => browser
                    [value] => chrome
                )

            [totalCount] => 468
        )
    ...
)
```

Additionally if you set a dimension value, the method will return a sum only for that given value:

```php
$stats->visitorsDimensionSum(Webiny\WebsiteAnalytics\StatHandlers\Browser::NAME, 'safari');
```

Result:

```text
1284
```

#### `visitorsDimensionByDay`

Returns a the number of visitors for the given dimension, grouped by day.
Note that this method requires that you provide both dimension name and dimension value.

```php
$stats->visitorsDimensionByDay(Webiny\WebsiteAnalytics\StatHandlers\Country::NAME, 'GB');
```

Result:

```text
Array
(
    [0] => Array
        (
            [_id] => 1443916800
            [totalCount] => 3
        )

    [1] => Array
        (
            [_id] => 1444003200
            [totalCount] => 1
        )
    ...
)
```

#### `pageViewsSum`

Same as `visitorSum` method, this one just returns the sum of page views which are not unique visitors.
 
 
```php
$stats->pageViewsSum();
```

#### `pageViewsByDay`

Same as `visitorsByDay` method, this one just returns the page views which are not unique visitors.

```php
$stats->pageViewsByDay();
```

#### `pageViewsByMonth`

Same as `visitorsByMonth` method, this one just returns the page views which are not unique visitors.

```php
$stats->pageViewsByMonth();
```

#### `urlSum`

Returns a sum of page views for a particular url. If you don't specify the url, you can get a list of top visited pages, for example:

```php
// get top 10 pages
$stats->urlSum(null, 10);
```

The `_id` field represents the page url.
**NOTE:** Pages are tracked without the query parameters.

```text
Array
(

    [0] => Array
        (
            [_id] => /page-208/
            [totalCount] => 46
        )

    [1] => Array
        (
            [_id] => /page-396/
            [totalCount] => 45
        )
    ...
)
```

If you specify the page url, then the sum for that particular page is returned:

```php
$stats->urlSum('/page-208/');
```

```text
28
```

#### `urlByDay`

Same as `visitorsByDay` method, this one just returns the page views for a particular page, grouped by days.

```php
$stats->urlByDay('/page-101/');
```

#### `urlByMonth`

Same as `visitorsByMonth` method, this one just returns the page views for a particular page, grouped by months.

```php
$stats->urlByMonth('/page-101/');
```

#### `urlDimensionSum`

Same as `visitorsDimensionSum` method.

```php
$result = $stats->urlDimensionSum('/page-110/', Webiny\WebsiteAnalytics\StatHandlers\Browser::NAME);

// or
$result = $stats->urlDimensionSum('/page-110/', Webiny\WebsiteAnalytics\StatHandlers\Browser::NAME, 'safari');
```


#### `urlDimensionByDay`

Same as `visitorsDimensionByDay` method, with the exception that you don't need to provide a dimension value. 

```php
$stats->urlDimensionByDay('/page-110/', Webiny\WebsiteAnalytics\StatHandlers\Browser::NAME);

// or

$stats->urlDimensionByDay('/page-110/', Webiny\WebsiteAnalytics\StatHandlers\Browser::NAME, 'safari');
```

### Adding custom dimensions (attribute)

To add a custom dimension, create a class and implement `StatHandlerInterface` and then register your stat handler with the `WebsiteAnalytics` instance, like so:

```php
$waInstance = new \Webiny\WebsiteAnalytics\WebsiteAnalytics($analyticDb, $geo);
$myHandlerInstance = $waInstance->addStatHandler('My\Custom\Handler\Name');
```

Now your handler is registered and will track the registered attribute on each url visit and on each unique visitor. 
You can also query your attribute analytics by using any of the provided `*Dimension*` methods, like `urlDimensionByDay`:

```php
$stats->visitorsDimensionSum('my-attribute-name');
```

## License and Contributions

Contributing > Feel free to send PRs.

License > [MIT](LICENSE)

## Resources

To run unit tests, you need to use the following command:
```
$ cd path/to/WebsiteAnalytics/
$ composer install
$ phpunit
```