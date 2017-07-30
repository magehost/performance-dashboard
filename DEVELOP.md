```
composer config repositories.magehost_performance-dashboard vcs https://github.com/magento/marketplace-eqp
composer require --dev  squizlabs/php_codesniffer:2.6.2  magento/marketplace-eqp:1.0.5
composer update
vendor/bin/phpcs --standard=MEQP2 app/code/MageHost/PerformanceDashboard
```
