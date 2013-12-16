Contributing
=====

License
----

[MIT](https://raw.github.com/gocom/danpu/master/LICENSE).

Configure Git
----

For convenience your committer, git user, should be linked to your GitHub account:

    $ git config --global user.name "John Doe"
    $ git config --global user.email john.doe@example.com

Make sure to use an email address that is linked to your GitHub account. It can be a throwaway address or you can use GitHub's email protection features. We don't want your emails, but this is to make sure we know who did what. All commits nicely link to their author, instead of them coming from ``foobar@invalid.tld``.

Dependencies
----

Dependencies are managed using [Composer](http://getcomposer.org).

Coding standard
----

The project follows the [PSR-0](https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-0.md) and [PSR-2](https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-2-coding-style-guide-meta.md) standards. You can use PHP_CodeSniffer to make sure your additions follow them too:

    $ composer install
    $ ./vendor/bin/phpcs --standard=PSR1,PSR2 src tests

Testing
----

The project uses [PHPunit](http://phpunit.de) for running its unit tests. Before pushing anything public run the tests and make sure they pass. First make sure you've installed dev-requirements using Composer:

    $ composer install

To run tests, specify your PHPunit config and boom:

    $ ./vendor/bin/phpunit /path/to/your/phpunit.xml

The config should follow the [phpunit.dist.xml](https://github.com/gocom/danpu/blob/master/phpunit.dist.xml) template. Tests require an empty MySQL database and a MySQL user with super-privileges.

Tests should pass before the changes can be merged to the codebase. If you create a pull requests that does not pass tests, CI will complain in the pull request thread. To get your changes merged, you should rework the code until everything works smoothly.
