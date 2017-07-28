Performance Dashboard Extension for Magento 2 - by MageHost.pro
=====================

# Install #

```
composer config repositories.magehost_performance-dashboard vcs git@github.com:magehost/performance-dashboard.git
composer require magehost/performance-dashboard --no-update
composer update magehost/performance-dashboard
php bin/magento module:enable MageHost_PerformanceDashboard
php bin/magento setup:upgrade
php bin/magento setup:di:compile
```

# Usage #

* In Admin go to _System > Tools > MH Performance Dashboard_.

# Uninstall #
```
composer remove magehost/performance-dashboard
php bin/magento setup:upgrade
php bin/magento setup:di:compile
```

# Screenshot #
![screenshot](https://raw.githubusercontent.com/magehost/performance-dashboard/master/doc/screenshot.png)
