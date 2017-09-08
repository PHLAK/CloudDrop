CloudDrop
=========

[![Latest Stable Version](https://img.shields.io/packagist/v/PHLAK/CloudDrop.svg)](https://packagist.org/packages/PHLAK/CloudDrop)
[![Total Downloads](https://img.shields.io/packagist/dt/PHLAK/CloudDrop.svg)](https://packagist.org/packages/PHLAK/CloudDrop)
[![Author](https://img.shields.io/badge/author-Chris%20Kankiewicz-blue.svg)](https://www.ChrisKankiewicz.com)
[![License](https://img.shields.io/packagist/l/PHLAK/CloudDrop.svg)](https://packagist.org/packages/PHLAK/CloudDrop)
[![Build Status](https://img.shields.io/travis/PHLAK/CloudDrop.svg)](https://travis-ci.org/PHLAK/CloudDrop)
<!-- [![StyleCI](https://styleci.io/repos/55566401/shield?branch=master&style=flat)](https://styleci.io/repos/55566401) -->

Store and retrieve files in cloud storage providers (i.e. Dropbox) -- by, [Chris Kankiewicz](https://www.ChrisKankiewicz.com)

Introduction
------------

...

Like this project? Keep me caffeinated by [making a donation](https://paypal.me/ChrisKankiewicz).

Requirements
------------

  - [PHP](https://php.net) >= 5.6

Install with Composer
---------------------

```bash
composer require phlak/clouddrop
```

Initializing the Client
-----------------------

First, import CloudDrop:

```php
use PHLAK\CloudDrop;
```

Then instantiate the class for your cloud storage provider of choice:

```php
$provider = CloudDrop\Provider::init($providerName, array $config);
```

For example, to instantate the Dropbox provider you would use the following:

```php
$dropbox = CloudDrop\Provider::init('dropbox',['access_token' => 'your_access_token']);
```

Configuration
-------------

...

Usage
-----

...

Changelog
---------

A list of changes can be found on the [GitHub Releases](https://github.com/PHLAK/CloudDrop/releases) page.

Troubleshooting
---------------

Please report bugs to the [GitHub Issue Tracker](https://github.com/PHLAK/CloudDrop/issues).

Copyright
---------

This project is licensed under the [MIT License](https://github.com/PHLAK/CloudDrop/blob/master/LICENSE).
