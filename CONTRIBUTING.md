Contributing
=====

License
----

[MIT](https://raw.github.com/gocom/danpu/master/LICENSE).

Versioning
----

[Semantic Versioning](https://semver.org/).

Configure Git
----

For convenience your committer, git user, should be linked to your GitHub account:

    $ git config --global user.name "John Doe"
    $ git config --global user.email john.doe@example.com

Make sure to use an email address that is linked to your GitHub account. It can be a throwaway address or you can use GitHub's email protection features. We don't want your emails, but this is to make sure we know who did what. All commits nicely link to their author, instead of them coming from ``foobar@invalid.tld``.

Dependencies
----

Dependencies are managed using [Composer](https://getcomposer.org). After you have cloned the repository, run composer install:

    $ composer install

And update before testing and committing:

    $ composer update

Coding standard
----

The project follows the [PSR-4](https://www.php-fig.org/psr/psr-4/) and [PSR-2](https://www.php-fig.org/psr/psr-2/) standards. You can use PHP_CodeSniffer to make sure your additions follow them too:

    $ composer cs

Testing
----

The project uses [PHPunit](https://phpunit.de) for running its unit tests. To run tests, first set up your own copy of `phpunit.xml` config, saved to the project root directory. This should be done to define your database connection.

    $ cp phpunit.dist.xml phpunit.xml

The config should follow the [phpunit.dist.xml](https://github.com/gocom/danpu/blob/master/phpunit.dist.xml) template. Tests require an empty MySQL database and a MySQL user with super-privileges. After you have configured phpunit, you can run the tests:

    $ composer test

Tests should pass before the changes can be merged to the codebase. If you create a pull requests that does not pass tests, CI will complain in the pull request thread. To get your changes merged, you should rework the code until everything works smoothly.
