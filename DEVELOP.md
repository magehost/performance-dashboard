```
composer config repositories.magehost_performance-dashboard vcs https://github.com/magento/marketplace-eqp
composer require --dev  squizlabs/php_codesniffer:2.6.2  magento/marketplace-eqp:1.0.5
composer update
{
    vendor/bin/phpcbf  --standard=MEQP2  app/code/MageHost/PerformanceDashboard
    clear
    vendor/bin/phpcs   --standard=MEQP2  app/code/MageHost/PerformanceDashboard
}
```

* Update [CHANGELOG.md](https://github.com/magehost/performance-dashboard/blob/master/CHANGELOG.md)
* Update [Install Guide](https://docs.google.com/document/d/1wN75IXYpYvBBMdMdVLDbbsNS5itp1SdNB0Eysf1zj2M/)
* Update [User Guide](https://docs.google.com/document/d/1gLJVMtEORojexTtku7hn1PGVE1RRkGT2s6PoSZwYdZA/)
