[![Version](http://img.shields.io/packagist/v/phpcq/branch-alias-validation.svg?style=flat-square)](https://packagist.org/packages/phpcq/branch-alias-validation)
[![Stable Build Status](http://img.shields.io/travis/phpcq/branch-alias-validation/master.svg?style=flat-square)](https://travis-ci.org/phpcq/branch-alias-validation)
[![Upstream Build Status](http://img.shields.io/travis/phpcq/branch-alias-validation/develop.svg?style=flat-square)](https://travis-ci.org/phpcq/branch-alias-validation)
[![License](http://img.shields.io/packagist/l/phpcq/branch-alias-validation.svg?style=flat-square)](https://github.com/phpcq/branch-alias-validation/blob/master/LICENSE)
[![Downloads](http://img.shields.io/packagist/dt/phpcq/branch-alias-validation.svg?style=flat-square)](https://packagist.org/packages/phpcq/branch-alias-validation)

Validate branch alias against latest tag.
=========================================

This is useful to ensure that no branch alias is "behind" the most recent tag on the given branch for the alias.

Usage
-----

Add to your `composer.json` in the `require-dev` section:
```
"phpcq/branch-alias-validation": "~1.0"
```

Call the binary:
```
./vendor/bin/validate-branch-alias.php
```

Optionally pass the root of the git repository to check:
```
./vendor/bin/validate-branch-alias.php /path/to/some/git/repository
```
