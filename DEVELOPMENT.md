### Install for development

    composer config repositories.mh_performance_dashboard vcs git@github.com:magehost/performance-dashboard.git
    composer require --prefer-source magehost/performance-dashboard:dev-master
    php bin/magento module:enable MageHost_PerformanceDashboard
    php bin/magento setup:upgrade
    php bin/magento setup:di:compile
    php bin/magento setup:static-content:deploy --area adminhtml
