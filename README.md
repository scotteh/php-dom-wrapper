# PHP DOM Wrapper
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/scotteh/php-dom-wrapper/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/scotteh/php-dom-wrapper/?branch=master) [![Build Status](https://travis-ci.org/scotteh/php-dom-wrapper.svg?branch=master)](https://travis-ci.org/scotteh/php-dom-wrapper)

## Intro

PHP DOM Wrapper is a simple DOM wrapper library to manipulate and traverse HTML documents. Based around jQuery's manipulation and traversal methods, largely mimicking the behaviour of it's jQuery counterparts.

## Author

 - Andrew Scott (andrew@andrewscott.net.au)

## Requirements

 - PHP 7.1 or later
 - PSR-4 compatible autoloader

## Install

This library is designed to be installed via [Composer](https://getcomposer.org/doc/).

Add the dependency into your projects composer.json.
```
{
  "require": {
    "scotteh/php-dom-wrapper": "^1.0"
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

## Autoloading

This library requires an autoloader, if you aren't already using one you can include [Composers autoloader](https://getcomposer.org/doc/01-basic-usage.md#autoloading).

``` php
require('vendor/autoload.php');
```

## Methods

### Manipulation

| Method | jQuery Method Name *(if different)* |
|--------|------------------------------|
| addClass    |
| after       |
| append      |
| attr        |
| before      |
| clone       |
| detach      |
| empty       |
| hasClass    |
| html        |
| prepend     |
| remove      |
| removeAttr  |
| removeClass |
| replaceWith |
| text        |
| unwrap      |
| wrap        |
| wrapAll     |
| wrapInner   |

### Traversal

| Method | jQuery Method Name *(if different)* |
|--------|------------------------------|
| [add](#add)          |
| [children](#children)     |
| [closest](#closest)      |
| [contents](#contents)     |
| [eq](#eq)           |
| [filter](#filter)       |
| [find](#find)         |
| [first](#first)        |
| [has](#has)          |
| [is](#is)           |
| [last](#last)         |
| [map](#map)          |
| [following](#following)         | *next* |
| [followingAll](#followingAll)      | *nextAll* |
| [followingUntil](#followingUntil)    | *nextUntil* |
| [not](#not)          |
| [parent](#parent)       |
| [parents](#parents)      |
| [parentsUntil](#parentsUntil) |
| [preceding](#preceding)         | *prev* |
| [precedingAll](#precedingAll)      | *prevAll* |
| [precedingUntil](#precedingUntil)    | *prevUntil* |
| [siblings](#siblings)     |
| [slice](#slice)        |

Additional Methods:

* count()
* each()

## Usage

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

---

## Methods

### Manipulation

#### addClass
#### after
#### append
#### attr
#### before
#### clone
#### detach
#### empty
#### hasClass
#### html
#### prepend
#### remove
#### removeAttr
#### removeClass
#### replaceWith
#### text
#### unwrap
#### wrap
#### wrapAll
#### wrapInner

### Traversal

#### add

<u>Definition</u>

```
NodeList add(string|NodeList|\DOMNode $input)
```
    
Add additional node(s) to the existing set.

<u>Example</u>

``` php
$nodes = $doc->find('a');
$nodes->add($doc->find('p'));
```

---

#### children

<u>Definition</u>

```
NodeList children()
```
    
Return children of each node in the current set.

<u>Example</u>

``` php
$nodes = $doc->find('p');
$childrenOfParagraphs = $nodes->children();
```

---

#### closest

<u>Definition</u>

```
Element|NodeList|null closest(string|NodeList|\DOMNode|callable $input)
```
    
Return the first element matching the supplied input by traversing up through the ancestors of each node in the current set. 

<u>Example</u>

``` php
$nodes = $doc->find('a');
$closestAncestors = $nodes->closest('p');
```

---

#### contents

<u>Definition</u>

```
NodeList contents()
```
    
Return the children of each node in the current set.

<u>Example</u>

``` php
$nodes = $doc->find('p');
$contents = $nodes->contents();
```

---

#### eq

<u>Definition</u>

```
\DOMNode|null eq(int $index)
```
    
Return node in the current set at the specified index.

<u>Example</u>

``` php
$nodes = $doc->find('a');
$nodeAtIndexOne = $nodes->eq(1);
```

---

#### filter

<u>Definition</u>

```
NodeList filter(string|NodeList|\DOMNode|callable $input)
```
    
Return nodes in the current set that match the input. 

<u>Example</u>

``` php
$nodes = $doc->filter('a')
$exampleATags = $nodes->filter('[href*=https://example.org/]');
```

---

#### find

<u>Definition</u>

```
NodeList find(string $selector[, string $prefix = 'descendant::'])
```
    
Return the decendants of the current set filtered by the selector and optional XPath axes.

<u>Example</u>

``` php
$nodes = $doc->find('a');
```

---

#### first

<u>Definition</u>

```
mixed first(int $index)
```
    
Return the first node of the current set.

<u>Example</u>

``` php
$nodes = $doc->find('a');
$firstNode = $nodes->first();
```

---

#### has

<u>Definition</u>

```
NodeList has(string|NodeList|\DOMNode|callable $input)
```
    
Return nodes with decendants of the current set matching the input. 

<u>Example</u>

``` php
$nodes = $doc->find('a');
$anchorTags = $nodes->has('span');
```

---

#### is

<u>Definition</u>

```
bool is(string|NodeList|\DOMNode|callable $input)
```
    
Test is nodes from the current set match the input. 

<u>Example</u>

``` php
$nodes = $doc->find('a');
$anchorTags = $nodes->has('[anchor]');
```

---

#### last

<u>Definition</u>

```
mixed last(int $index)
```
    
Return the last node of the current set.

<u>Example</u>

``` php
$nodes = $doc->find('a');
$lastNode = $nodes->last();
```

---

#### map

<u>Definition</u>

```
NodeList map(callable $function)
```
    
Apply a callback to nodes in the current set and return a new NodeList.

<u>Example</u>

``` php
$nodes = $doc->find('a');
$lastNode = $nodes->last();
```

---

#### following

<u>Definition</u>

```
\DOMNode|null following([string|NodeList|\DOMNode|callable $selector = null])
```
    
Return the sibling immediately following each element node in the current set. 

*Optionally filtered by selector.*

<u>Example</u>

``` php
$nodes = $doc->find('a');
$follwingNodes = $nodes->following();
```

---

#### followingAll

<u>Definition</u>

```
NodeList followingAll([string|NodeList|\DOMNode|callable $selector = null])
```
    
Return all siblings following each element node in the current set.

*Optionally filtered by selector.*

<u>Example</u>

``` php
$nodes = $doc->find('a');
$follwingAllNodes = $nodes->followingAll('[anchor]');
```

---

#### followingUntil

<u>Definition</u>

```
NodeList followingUntil([[string|NodeList|\DOMNode|callable $selector = null], string|NodeList|\DOMNode|callable $selector = null])
```
    
Return all siblings following each element node in the current set upto but not including the node matched by $input.

*Optionally filtered by input.*
*Optionally filtered by selector.*

<u>Example</u>

``` php
$nodes = $doc->find('a');
$follwingUntilNodes = $nodes->followingUntil('.submit');
```

---

#### not

<u>Definition</u>

```
NodeList not(string|NodeList|\DOMNode|callable $input)
```
    
Return element nodes from the current set not matching the input. 

<u>Example</u>

``` php
$nodes = $doc->find('a');
$missingHrefAttribute = $nodes->not('[href]');
```

---

#### parent

<u>Definition</u>

```
Element|NodeList|null parent([string|NodeList|\DOMNode|callable $selector = null])
```
    
Return the immediate parent of each element node in the current set. 

*Optionally filtered by selector.*

<u>Example</u>

``` php
$nodes = $doc->find('a');
$parentNodes = $nodes->parent();
```

---

#### parents

<u>Definition</u>

```
NodeList parent([string|NodeList|\DOMNode|callable $selector = null])
```
    
Return the ancestors of each element node in the current set. 

*Optionally filtered by selector.*

<u>Example</u>

``` php
$nodes = $doc->find('a');
$ancestorDivNodes = $nodes->parents('div');
```

---

#### parentsUntil

<u>Definition</u>

```
NodeList parent([string|NodeList|\DOMNode|callable $selector = null])
```
    
Return the ancestors of each element node in the current set upto but not including the node matched by $selector.

*Optionally filtered by selector.*

<u>Example</u>

``` php
$nodes = $doc->find('a');
$ancestorDivNodes = $nodes->parents('div');
```

---

#### preceding

<u>Definition</u>

```
\DOMNode|null preceding([string|NodeList|\DOMNode|callable $selector = null])
```
    
Return the sibling immediately preceding each element node in the current set. 

*Optionally filtered by selector.*

<u>Example</u>

``` php
$nodes = $doc->find('a');
$precedingNodes = $nodes->preceding();
```

---

#### precedingAll

<u>Definition</u>

```
NodeList precedingAll([string|NodeList|\DOMNode|callable $selector = null])
```
    
Return all siblings preceding each element node in the current set.

*Optionally filtered by selector.*

<u>Example</u>

``` php
$nodes = $doc->find('a');
$precedingAllNodes = $nodes->precedingAll('[anchor]');
```

---
#### precedingUntil

<u>Definition</u>

```
NodeList precedingUntil([[string|NodeList|\DOMNode|callable $selector = null], string|NodeList|\DOMNode|callable $selector = null])
```
    
Return all siblings preceding each element node in the current set upto but not including the node matched by $input.

*Optionally filtered by input.*
*Optionally filtered by selector.*

<u>Example</u>

``` php
$nodes = $doc->find('a');
$precedingUntilNodes = $nodes->precedingUntil('.submit');
```

---

#### siblings

<u>Definition</u>

```
NodeList siblings([[string|NodeList|\DOMNode|callable $selector = null])
```
    
Return siblings of each element node in the current set.

*Optionally filtered by selector.*

<u>Example</u>

``` php
$nodes = $doc->find('p');
siblings = $nodes->siblings();
```

---

#### slice

<u>Definition</u>

```
NodeList slcie(int $start, int $end)
```
    
Return a subset of the current set based on the start and end indexes.

<u>Example</u>

``` php
$nodes = $doc->find('p');
// Return nodes 1 through to 3 as a new NodeList
siblings = $nodes->slice(1, 3);
```

---

### Additional Methods

#### count
#### each

## Licensing

PHP DOM Wrapper is licensed by Andrew Scott under the BSD 3-Clause License, see the LICENSE file for more details.
