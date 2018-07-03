### 1.13.0 (2017-12-31)

  * Added check if web server is running HTTP/2
  * Disabled checks for JS/CSS Bundling/Merging if on HTTP/2
  * Fixes issue #4. Thanks to @JeroenVanLeusden for reporting.

### 1.12.3 (2017-11-01) => reverted

  * Changed composer install into 1.* so it doesn't get locked to minor version
  
### 1.12.2 (2017-11-01)

  * Recognise 'db' as (non optimal) session storage
  * Issue #3: Published to Packagist.org. Thanks @paales.
  * Issue #3: Improved composer instructions. Thanks @paales.
  
### 1.12.1 (2017-10-14)

  * Updated documents
  * Bump version for resubmit to Magento Marketplace

### 1.12.0 (2017-09-25)

  * Approved for Magento Marketplace, is called 1.11.4 there
  * Links to DevDocs now contain the actual Magento version
  * Magento 2.2: Removed dev/.../... config settings. Does no longer exist in production mode.
  * Added DevDocs link explaining Cache types
  * Moved ideas for future checks to [Wiki](https://github.com/magehost/performance-dashboard/wiki/Ideas-for-future-checks)
  * Moved Development instructions to [Wiki](https://github.com/magehost/performance-dashboard/wiki/Development)
  * Using Magento's wrapper functions instead of `glob()` and `file()`

### 1.11.5 (2017-09-07)

  * Fix error during setup:di:compile, was double dependency in Buttons Renderer 
  
### 1.11.4 (2017-09-01)

  * Changed composer requirements to allow newer versions
  * Updated Manuals
  * PHPDoc type fixes for Magento 2.2 RC
  * Works on Magento 2.2 RC
  
### 1.11.0 (2017-08-24)

  * Solved Issue #2: Removed check for Flat Category & Product Indexes
  * Added DevDocs links
  * Added links to config, cache & index management
  * Split PHP Version & Configuration rows
  * Show current PHP Configuration

### 1.10.0 (2017-08-21)

  * Added check for Async Indexes
  * Added check for Minify HTML
  * Added check for Async sending of sales emails
  * Moved PHP check to top of list
  * Moved 'grouped' processing of info/problems/warnings/actions to Abstract class
  
### 1.9.0 (2017-08-21)

  * Added check for PHP Version and Settings
  * Fixed some interface strings that were not translatable
  
### 1.8.0 (2017-08-21)

  * Added check if Composer's autoloader is optimized
  * Used constants instead of status 0-3
  
### 1.7.1 (2017-08-21)

  * Fixed issue #1 - Monolog error on Magento 2.1.8

### 1.7.0 (2017-08-09)

  * Added check if Varnish FPC is enabled
  * Config data checks can now use a source model
  * Updated installation instructions

### 1.6.2 (2017-08-09)

  * Updated installation instructions
  
### 1.6.1 (2017-08-08)

  * First version submitted to the Magento Marketplace
  * Improved documentation
  * Improved Composer requirements
  * Tested with Magento 2.0.14 and 2.1.7

### 1.6.0 (2017-08-08)

  * Replaced find+grep on layouts by frontend logger.  …

### 1.5.8 (2017-07-29)

  * Increased truncate size of info + action

### 1.5.7 (2017-07-29)

  * Fixed bugs in 'Non Cacheable Templates'

### 1.5.5 (2017-07-20)

  * Code improved based on: phpcs --standard=MEQP2

### 1.5.1 (2017-07-28)

  * Removed PHP version dependency

### 1.5.0 (2017-07-28)

  * Improved PHPDOC
  * fixed PHPMD warnings.

### 1.4.0 (2017-07-27)

  * Restructured extension files.

### 1.3.0 (2017-07-27)

  * First version with all functions working
