MageHost Performance Dashboard Extension for Magento 2
=====================
# State #
**Warning: This software is in 'Alpha' state. Do not use.**

# Install #

```
composer config repositories.magehost_performance-dashboard vcs git@github.com:magehost/performance-dashboard.git
composer require magehost/performance-dashboard:^1.0 --no-update
composer update magehost/performance-dashboard
php bin/magento module:enable MageHost_PerformanceDashboard
php bin/magento setup:upgrade
php bin/magento setup:di:compile
```
# Usage #

* In Admin go to _System > Tools > Performance Dashboard_.

# Uninstall #
```
composer remove magehost/performance-dashboard
php bin/magento setup:upgrade
php bin/magento setup:di:compile
```
