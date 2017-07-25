# Install #

```
composer config repositories.magehost_performance-dashboard vcs https://github.com/magehost/performance-dashboard
composer require magehost/performance-dashboard --no-update
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
