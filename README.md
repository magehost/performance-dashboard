Performance Dashboard Extension for Magento 2
=====================

The free Performance Dashboard Extension by [MageHost.pro](https://magehost.pro) adds a screen to the Magento Store Admin called "Performance Dashboard". In this screen you get a clear overview of areas where the performance of your Magento 2 can be improved.

# Install via Composer #

```
composer require magehost/performance-dashboard
php bin/magento module:enable MageHost_PerformanceDashboard
php bin/magento setup:upgrade
php bin/magento setup:di:compile
php bin/magento setup:static-content:deploy --area adminhtml
```

# Usage #

* In Admin go to *_System > Tools > Performance Dashboard_*.

# Uninstall #
```
php bin/magento module:disable MageHost_PerformanceDashboard
composer remove magehost/performance-dashboard
php bin/magento setup:upgrade
php bin/magento setup:di:compile
php bin/magento setup:static-content:deploy --area adminhtml
```

# Screenshot #
![screenshot](https://raw.githubusercontent.com/magehost/performance-dashboard/master/doc/screenshot.png)

# Description #
**This extension is free, licence: [MIT](https://github.com/magehost/performance-dashboard/blob/master/LICENSE).**

Using our experience as [Magento Hosting professionals](https://magehost.pro) we created a list of best-practises for a high performance Magento 2 setup.
Based on this list we have created a dashboard which automatically tests these various config settings and other setup choices.
Checks executed:

* Is PHP 7 in use?
* Is HTTP/2 in use?
* Are the PHP performance settings correct?
* Are the MySQL performance settings tuned?
* Is Magento in Production mode?
* Is the Magento Cache stored in Redis?
* Is the Full Page Cache stored in Redis?
* Are all caches enabled?
* Are sessions stored in Redis or Memcached?
* A check which logs CMS and Catalog pages which can't be cached in full-page-cache because of `cacheable="false"`.
* Is Composer's autoloader optimized?
* Is the Full Page Cache using Varnish?
* For Magento >= 2.3.1:
  * Is Elastic Search in use?
* For Magento >= 2.3.2:
  * Is JavaScript defered?
* For Magento < 2.2:
  * If not on HTTP/2:
    * Is merging CSS files enabled?
  * Is minify of JavaScript files enabled?
  * Is minify of CSS files enabled?
  * Is minify of HTML enabled?
* Asynchronous sending of sales emails enabled?
* All indexes set to Asynchronous?
