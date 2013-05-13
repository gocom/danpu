Testing Danpu
============

The project uses [PHPunit](http://phpunit.de) for running its unit tests. Before running the tests, make sure you've installed dev-requirements using [Composer](http://getcomposer.org):

    $ composer install --dev

 To run a test specify your PHPunit config and boom:

    $ ./vendor/bin/phpunit /path/to/your/phpunit.xml

The config should follow the [../phpunit.dist.xml](https://github.com/gocom/danpu/blob/master/phpunit.xml) template.