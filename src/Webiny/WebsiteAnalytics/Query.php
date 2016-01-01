<?php
/**
 * Webiny (http://www.webiny.com/)
 *
 * @link      http://www.webiny.com/ for the canonical source repository
 * @copyright Copyright (c) 2009-2016 Webiny LTD. (http://www.webiny.com)
 * @license   MIT
 */
namespace Webiny\WebsiteAnalytics;

use Webiny\AnalyticsDb\AnalyticsDb;

class Query
{
    /**
     * @var array
     */
    private $dateRange;

    /**
     * @var AnalyticsDb
     */
    private $analyticsDb;

    public function __construct(AnalyticsDb $analyticsDb, array $dateRange)
    {
        $this->analyticsDb = $analyticsDb;
        $this->dateRange = $dateRange;
    }

    /**
     * Returns the total number of visitors for the given period.
     */
    public function visitorsSum()
    {
        $result = $this->analyticsDb->query(WebsiteAnalytics::STAT_VISITOR, 0, $this->dateRange)
                                    ->stats()
                                    ->groupByEntityName()
                                    ->getResult();

        if ($result) {
            return $result[0]['totalCount'];
        }

        return false;
    }

    /**
     * Returns the number of visitors for the given period, grouped by day.
     */
    public function visitorsByDay()
    {
        $result = $this->analyticsDb->query(WebsiteAnalytics::STAT_VISITOR, 0, $this->dateRange)
                                    ->stats()
                                    ->groupByTimestamp()
                                    ->getResult();

        if ($result) {
            return $result;
        }

        return false;
    }

    /**
     * Returns the number of visitors for the given period, grouped by month.
     */
    public function visitorsByMonth()
    {
        $result = $this->analyticsDb->query(WebsiteAnalytics::STAT_VISITOR, 0, $this->dateRange)
                                    ->stats()
                                    ->monthly()
                                    ->groupByTimestamp()
                                    ->getResult();

        if ($result) {
            return $result;
        }

        return false;
    }

    /**
     * Returns the sum for the given visitor dimension.
     *
     * @param string      $dimensionName
     * @param null|string $dimensionValue
     *
     * @return bool
     */
    public function visitorsDimensionSum($dimensionName, $dimensionValue = null)
    {
        $result = $this->analyticsDb->query(WebsiteAnalytics::STAT_VISITOR, 0, $this->dateRange)
                                    ->dimension($dimensionName, $dimensionValue)
                                    ->groupByDimensionValue()
                                    ->sortByCount(-1)
                                    ->getResult();

        if ($result) {
            return $result;
        }

        return false;
    }

    /**
     * Returns the number counter sum for the given dimension on a day basis.
     *
     * @param string      $dimensionName
     * @param null|string $dimensionValue
     *
     * @return bool
     */
    public function visitorsDimensionByDay($dimensionName, $dimensionValue = null)
    {
        $result = $this->analyticsDb->query(WebsiteAnalytics::STAT_VISITOR, 0, $this->dateRange)
                                    ->dimension($dimensionName, $dimensionValue)
                                    ->groupByTimestamp()
                                    ->getResult();

        if ($result) {
            return $result;
        }

        return false;
    }

    /**
     * Returns the total number of page views for the given period.
     */
    public function pageViewsSum()
    {
        $result = $this->analyticsDb->query(WebsiteAnalytics::STAT_PAGE_VIEW, 0, $this->dateRange)
                                    ->stats()
                                    ->groupByEntityName()
                                    ->getResult();

        if ($result) {
            return $result[0]['totalCount'];
        }

        return false;
    }

    /**
     * Returns the number of page views for the given period, grouped by day.
     */
    public function pageViewsByDay()
    {
        $result = $this->analyticsDb->query(WebsiteAnalytics::STAT_PAGE_VIEW, 0, $this->dateRange)
                                    ->stats()
                                    ->groupByTimestamp()
                                    ->getResult();

        if ($result) {
            return $result;
        }

        return false;
    }

    /**
     * Returns the number of page views for the given period, grouped by month.
     */
    public function pageViewsByMonth()
    {
        $result = $this->analyticsDb->query(WebsiteAnalytics::STAT_PAGE_VIEW, 0, $this->dateRange)
                                    ->stats()
                                    ->monthly()
                                    ->groupByTimestamp()
                                    ->getResult();

        if ($result) {
            return $result;
        }

        return false;
    }

    /**
     * Sums the views for the given time period for the given url.
     * If url is not given, the sum for all pages is returned and sorted
     *
     * @param $url
     * @param $limit
     *
     * @return int|array|bool
     */
    public function urlSum($url = null, $limit = 10)
    {

        $query = $this->analyticsDb->query(WebsiteAnalytics::STAT_URL_VIEW, $url, $this->dateRange)
                                   ->stats()
                                   ->sortByCount(-1)
                                   ->limit($limit);

        if (is_null($url)) {
            $result = $query->groupByRef()->getResult();
        } else {
            $result = $query->groupByEntityName()->getResult();
        }

        if ($result) {
            if (!is_null($url)) {
                return $result[0]['totalCount'];
            }

            return $result;
        }

        return false;
    }

    /**
     * Returns the number of views for the give page across the defined number of days.
     *
     * @param $url
     *
     * @return array|bool|int
     * @throws WebsiteAnalyticsException
     */
    public function urlByDay($url)
    {
        if (empty($url)) {
            throw new WebsiteAnalyticsException('You must specify a url for in order to use Webiny\WebsiteAnalytics\Query::urlByDay() method.');
        }

        $result = $this->analyticsDb->query(WebsiteAnalytics::STAT_URL_VIEW, $url, $this->dateRange)
                                    ->stats()
                                    ->groupByTimestamp()
                                    ->getResult();

        if ($result) {
            return $result;
        }

        return false;
    }

    /**
     * Returns the number of views for the give page across the defined number of months.
     *
     * @param $url
     *
     * @return array|bool|int
     * @throws WebsiteAnalyticsException
     */
    public function urlByMonth($url)
    {
        if (empty($url)) {
            throw new WebsiteAnalyticsException('You must specify a url for in order to use Webiny\WebsiteAnalytics\Query::urlByDay() method.');
        }

        $result = $this->analyticsDb->query(WebsiteAnalytics::STAT_URL_VIEW, $url, $this->dateRange)
                                    ->stats()
                                    ->monthly()
                                    ->groupByTimestamp()
                                    ->getResult();

        if ($result) {
            return $result;
        }

        return false;
    }

    /**
     * Returns the sum for the given url dimension.
     *
     * @param string      $url
     * @param string      $dimensionName
     * @param null|string $dimensionValue
     *
     * @return bool
     * @throws WebsiteAnalyticsException
     */
    public function urlDimensionSum($url, $dimensionName, $dimensionValue = null)
    {
        if (empty($url)) {
            throw new WebsiteAnalyticsException('You must specify a url for in order to use Webiny\WebsiteAnalytics\Query::visitorsDimensionSum() method.');
        }

        $result = $this->analyticsDb->query(WebsiteAnalytics::STAT_URL_VIEW, $url, $this->dateRange)
                                    ->dimension($dimensionName, $dimensionValue)
                                    ->groupByDimensionValue()
                                    ->sortByCount(-1)
                                    ->getResult();

        if ($result) {
            return $result;
        }

        return false;
    }

    /**
     * Returns the number of views for the given dimension on a day basis for the given page.
     *
     * @param string      $url
     * @param string      $dimensionName
     * @param null|string $dimensionValue
     *
     * @return bool
     * @throws WebsiteAnalyticsException
     */
    public function urlDimensionByDay($url, $dimensionName, $dimensionValue = null)
    {
        if (empty($url)) {
            throw new WebsiteAnalyticsException('You must specify a url for in order to use Webiny\WebsiteAnalytics\Query::urlDimensionByDay() method.');
        }

        $result = $this->analyticsDb->query(WebsiteAnalytics::STAT_URL_VIEW, $url, $this->dateRange)
                                    ->dimension($dimensionName, $dimensionValue)
                                    ->groupByTimestamp()
                                    ->getResult();

        if ($result) {
            return $result;
        }

        return false;
    }
}