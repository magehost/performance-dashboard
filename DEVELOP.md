## Dev Environment
```
cd ~/magento2
mkdir -p app/code/MageHost
git clone https://github.com/magehost/performance-dashboard app/code/MageHost/PerformanceDashboard
```

## Before Release
```
composer config repositories.magehost_performance-dashboard vcs https://github.com/magento/marketplace-eqp
composer require --dev  squizlabs/php_codesniffer:2.6.2  magento/marketplace-eqp:1.0.5
composer update
{
    vendor/bin/phpcbf  app/code/MageHost/PerformanceDashboard  --standard=MEQP2
    clear
    vendor/bin/phpcs   app/code/MageHost/PerformanceDashboard  --standard=MEQP2
    printf "\e[31m%s\e[0m\n" "$( vendor/bin/phpcs   app/code/MageHost/PerformanceDashboard  --standard=MEQP2 --severity=10)"
}
```

* Update [screenshot](https://github.com/magehost/performance-dashboard/blob/master/doc/screenshot.png)
* Update [CHANGELOG.md](https://github.com/magehost/performance-dashboard/blob/master/CHANGELOG.md)
* Update [Install Guide](https://docs.google.com/document/d/1wN75IXYpYvBBMdMdVLDbbsNS5itp1SdNB0Eysf1zj2M/)
* Update [User Guide](https://docs.google.com/document/d/1gLJVMtEORojexTtku7hn1PGVE1RRkGT2s6PoSZwYdZA/)
