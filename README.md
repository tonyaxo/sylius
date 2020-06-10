
Documentation
-------------

COMPOSER_MEMORY_LIMIT=-1 composer require sylius/shop-api-plugin

Install CMS: [https://github.com/BitBagCommerce/SyliusCmsPlugin/blob/master/doc/installation.md]


bin/console cache:clear --no-optional-warmers
bin/console sylius:fixtures:load app --no-interaction

bin/console sylius:install:database --fixture-suite=app