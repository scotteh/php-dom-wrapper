#PHP DOM Wrapper
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/scotteh/php-dom-wrapper/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/scotteh/php-dom-wrapper/?branch=master) [![Build Status](https://travis-ci.org/scotteh/php-dom-wrapper.svg?branch=master)](https://travis-ci.org/scotteh/php-dom-wrapper)

##Intro

PHP DOM Wrapper is a simple DOM wrapper library to manipulate and traverse HTML documents. Based around jQuery's manipulation and traversal methods, largely mimicking the behaviour of it's jQuery counterparts.

##Author

 - Andrew Scott (andrew@andrewscott.net.au)

##Requirements

 - PHP 5.4 or later
 - PSR-4 compatible autoloader

##Install

This library is designed to be installed via [Composer](https://getcomposer.org/doc/).

Add the dependency into your projects composer.json.
```
{
  "require": {
    "scotteh/php-dom-wrapper": "dev-master"
  }
}
```

Download the composer.phar
``` bash
curl -sS https://getcomposer.org/installer | php
```

Install the library.
``` bash
php composer.phar install
```

##Autoloading

This library requires an autoloader, if you aren't already using one you can include [Composers autoloader](https://getcomposer.org/doc/01-basic-usage.md#autoloading).

``` php
require('vendor/autoload.php');
```

##Methods

###Manipulation

| Method | Implemented |
|--------|-------------|
| addClass    | **Yes** |
| after       | **Yes** |
| append      | **Yes** |
| attr        | **Yes** |
| before      | **Yes** |
| clone       | **Yes** |
| detach      | **Yes** |
| empty       | **Yes** |
| hasClass    | **Yes** |
| html        | **Yes** |
| prepend     | **Yes** |
| remove      | **Yes** |
| removeAttr  | **Yes** |
| removeClass | **Yes** |
| replaceWith | **Yes** |
| text        | **Yes** |
| unwrap      | **Yes** |
| wrap        | **Yes** |
| wrapAll     | **Yes** |
| wrapInner   | **Yes** |

###Traversal

| Method | Implemented | Method Name *(if different)* |
|--------|-------------|------------------------------|
| add          | **Yes** |
| children     | **Yes** |
| closest      | **Yes** |
| contents     | **Yes** |
| eq           | **Yes** |
| filter       | **Yes** |
| find         | **Yes** |
| first        | **Yes** |
| has          | **Yes** |
| is           | **Yes** |
| last         | **Yes** |
| map          | **Yes** |
| next         | **Yes** | *following* |
| nextAll      | **Yes** | *followingAll* |
| nextUntil    | **Yes** | *followingUntil* |
| not          | **Yes** |
| parent       | **Yes** |
| parents      | **Yes** |
| parentsUntil | **Yes** |
| prev         | **Yes** | *preceding* |
| prevAll      | **Yes** | *precedingAll* |
| prevUntil    | **Yes** | *precedingUntil* |
| siblings     | **Yes** |
| slice        | **Yes** |

Additional Methods:

* count()
* each()

##Usage

Example #1:
``` php
use DOMWrap\Document;

$html = '<ul><li>First</li><li>Second</li><li>Third</li></ul>';

$doc = new Document();
$doc->html($html);
$nodes = $doc->find('li');

// Returns '3'
var_dump($nodes->count());

// Append as a child node to each <li>
$nodes->append('<b>!</b>');

// Returns: <html><body><ul><li>First<b>!</b></li><li>Second<b>!</b></li><li>Third<b>!</b></li></ul></body></html>
var_dump($doc->saveHTML($doc));
```

##Licensing

PHP DOM Wrapper is licensed by Andrew Scott under the BSD 3-Clause License, see the LICENSE file for more details.
