Installing the Extension
--------------------------------------------------

Run

    composer config repositories.product vcs git@github.com:vsevolod-tarasov/magento2-product-disabled.git
    composer require media-lounge/disabled-product-redirect:*
    php bin/magento setup:upgrade
    php bin/magento setup:di:compile
