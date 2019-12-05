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

Install with [Composer](https://getcomposer.org/doc/).

```
composer require scotteh/php-dom-wrapper
```

## Autoloading

This library requires an autoloader, if you aren't already using one you can include [Composers autoloader](https://getcomposer.org/doc/01-basic-usage.md#autoloading).

``` php
require 'vendor/autoload.php';
```

## Methods

### Manipulation

| Method | jQuery Equivalent *(if different)* |
|--------|------------------------------|
| [addClass()](#addClass)    |
| [after()](#after)       |
| [append()](#append)      |
| [appendTo()](#appendTo)    |
| [attr()](#attr)        |
| [before()](#before)      |
| [clone()](#clone)       |
| [detach()](#detach)      |
| [empty()](#empty)       |
| [hasClass()](#hasClass)    |
| [html()](#html)        |
| [prepend()](#prepend)     |
| [prependTo()](#prependTo)   |
| [remove()](#remove)      |
| [removeAttr()](#removeAttr)  |
| [removeClass()](#removeClass) |
| [replaceWith()](#replaceWith) |
| [text()](#text)        |
| [unwrap()](#unwrap)      |
| [wrap()](#wrap)        |
| [wrapAll()](#wrapAll)     |
| [wrapInner()](#wrapInner)   |

### Traversal

| Method | jQuery Equivalent *(if different)* |
|--------|------------------------------|
| [add()](#add)          |
| [children()](#children)     |
| [closest()](#closest)      |
| [contents()](#contents)     |
| [eq()](#eq)           |
| [filter()](#filter)       |
| [find()](#find)         |
| [first()](#first)        |
| [has()](#has)          |
| [is()](#is)           |
| [last()](#last)         |
| [map()](#map)          |
| [following()](#following)         | *next()* |
| [followingAll()](#followingAll)      | *nextAll()* |
| [followingUntil()](#followingUntil)    | *nextUntil()* |
| [not()](#not)          |
| [parent()](#parent)       |
| [parents()](#parents)      |
| [parentsUntil()](#parentsUntil) |
| [preceding()](#preceding)         | *prev()* |
| [precedingAll()](#precedingAll)      | *prevAll()* |
| [precedingUntil()](#precedingUntil)    | *prevUntil()* |
| [siblings()](#siblings)     |
| [slice()](#slice)        |

### Other

| Method | jQuery Equivalent *(if different)* |
|--------|------------------------------|
| [count()](#count)        | *length* (property) |
| [each()](#each)

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
var_dump($doc->html());
```

---

## Methods

### Manipulation

#### addClass

```
self addClass(string|callable $class)
```

##### Example

```php
$doc = (new Document())->html('<p>first paragraph</p><p>second paragraph</p>');
$doc->find('p')->addClass('text-center');
```

*Result:*

``` html
<p class="text-center">first paragraph</p><p class="text-center">second paragraph</p>
```

---

#### after

```
self after(string|NodeList|\DOMNode|callable $input)
```

##### Example

``` php
$doc = (new Document())->html('<ul><li>first</li><li>second</li></ul>');
$doc->find('li')->after('<span> (after)</span>'); 
```

*Result:*

``` html
<ul><li>first<span> (after)</span></li><li>second<span> (after)</span></li></ul>
```

---

#### append

```
self append(string|NodeList|\DOMNode|callable $input)
```

##### Example

``` php
$doc = (new Document())->html('<div>The quick brown fox jumps over the lazy dog</div>');
$doc->find('div')->append('<strong> Appended!</strong>');
```

*Result:*

``` html
<div>The quick brown fox jumps over the lazy dog<strong> Appended!</strong></div>
```

---

#### appendTo

```
self appendTo(string|NodeList|\DOMNode $selector)
```

##### Example

``` php
$doc = (new Document())->html('<div>The quick brown fox jumps over the lazy dog</div>');
$doc->create('<strong> Appended!</strong>')->appendTo('div');
```

*Result:*
``` html
<div>The quick brown fox jumps over the lazy dog<strong> Appended!</strong></div>
```

---

#### attr

```
self|string attr(string $name[, mixed $value = null])
```

##### Example #1 (Set)

``` php
$doc = (new Document())->html('<div class="text-center"></div>');
$doc->attr('class', 'text-left');
```

*Result:*

``` html
<div class="text-left"></div>
```

##### Example #2 (Get)

``` php
$doc = (new Document())->html('<div class="text-center"></div>');
echo $doc->attr('text-center');
```

*Result:*

``` html
text-center
```

---

#### before

```
self before(string|NodeList|\DOMNode|callable $input)
```

##### Example

``` php
$doc = (new Document())->html('<ul><li>first</li><li>second</li></ul>');
$doc->find('li')->after('<span>(before) </span>'); 
```

*Result:*

``` html
<ul><li><span>(before) </span>first</li><li><span>(before) </span>second</li></ul>
```

---

#### clone

```
NodeList|\DOMNode clone()
```

##### Example

``` php
$doc = (new Document())->html('<ul><li>Item</li></ul>');
$doc->find('div')->clone()->appendTo('ul'); 
```

*Result:*

``` html
<ul><li>Item</li><li>Item</li></ul>
```

---

#### detach

```
NodeList detach([string $selector = null])
```

##### Example

``` php
$doc = (new Document())->html('<ul class="first"><li>Item</li></ul><ul class="second"></ul>');
$el = $doc->find('ul.first li')->detach();
$doc->first('ul.second').append($el); 
```

*Result:*

``` html
<ul class="first"></ul><ul class="second"><li>Item</li></ul>
```

---

#### empty

```
self empty()
```

##### Example

``` php
$doc = (new Document())->html('<div>The quick brown fox jumps over the lazy dog</div>');
$doc->find('div')->empty(); 
```

*Result:*

``` html
<div></div>
```

---

#### hasClass

```
bool hasClass(string $class)
```

##### Example

``` php
$doc = (new Document())->html('<div class="text-center"></div>');
echo $doc->first('div')->hasClass('text-center');
```

*Result:*

``` html
true
```

---

#### html

```
string|self html([string|NodeList|\DOMNode|callable $input = null])
```

##### Example #1 (Set)

``` php
$doc = (new Document());
$doc->html('<div class="example"></div>');
```

*Result:*

``` html
<div class="example"></div>
```

##### Example #1 (Get)

``` php
$doc = (new Document())->html('<div class="example"></div>');
$doc->find('div')->append('<span>Example!</span>');
echo $doc->html();
```

*Result:*

``` html
<div class="example"><span>Example!</span></div>
```

---

#### prepend

```
self prepend(string|NodeList|\DOMNode|callable $input)
```

##### Example

``` php
$doc = (new Document())->html('<div>The quick brown fox jumps over the lazy dog</div>');
$doc->find('div')->prepend('<strong>Prepended! </strong>');
```

*Result:*

``` html
<div><strong>Prepended! </strong>The quick brown fox jumps over the lazy dog</div>
```

---

#### prependTo

```
self prependTo(string|NodeList|\DOMNode $selector)
```

##### Example

``` php
$doc = (new Document())->html('<div>The quick brown fox jumps over the lazy dog</div>');
$doc->create('<strong>Prepended! </strong>')->appendTo('div');
```

*Result:*
``` html
<div><strong>Prepended! </strong>The quick brown fox jumps over the lazy dog</div>
```

---

#### remove

```
self remove([string $selector = null])
```

##### Example

``` php
$doc = (new Document())->html('<ul><li class="first"></li><li class="second"></li></ul>');
$doc->find('.first').remove();
```

*Result:*
``` html
<ul><li class="second"></li></ul>
```

---

#### removeAttr

```
self removeAttr(string $name)
```

##### Example

``` php
$doc = (new Document())->html('<div class="first second"></div>');
$doc->find('div').removeAttr('class');
```

*Result:*
``` html
<div></div>
```

---

#### removeClass

```
self removeClass(string|callable $class)
```

##### Example

``` php
$doc = (new Document())->html('<div class="first second"></div>');
$doc->find('div').removeClass('first');
```

*Result:*
``` html
<div class="second"></div>
```

---

#### replaceWith

```
self replaceWith(string|NodeList|\DOMNode|callable $input)
```

##### Example

``` php
```

---

#### text

```
string|self text([string|NodeList|\DOMNode|callable $input = null])
```

##### Example

``` php
```

---

#### unwrap

```
self unwrap()
```

##### Example

``` php
```

---

#### wrap

```
self wrap(string|NodeList|\DOMNode|callable $input)
```

##### Example

``` php
```

---

#### wrapAll

```
self wrapAll(string|NodeList|\DOMNode|callable $input)
```

##### Example

``` php
```

---

#### wrapInner

```
self wrapInner(string|NodeList|\DOMNode|callable $input)
```

##### Example   

``` php
```

---


### Traversal

#### add

```
NodeList add(string|NodeList|\DOMNode $input)
```
    
Add additional node(s) to the existing set.

##### Example

``` php
$nodes = $doc->find('a');
$nodes->add($doc->find('p'));
```

---

#### children

```
NodeList children()
```
    
Return all children of each element node in the current set.

##### Example

``` php
$nodes = $doc->find('p');
$childrenOfParagraphs = $nodes->children();
```

---

#### closest

```
Element|NodeList|null closest(string|NodeList|\DOMNode|callable $input)
```
    
Return the first element matching the supplied input by traversing up through the ancestors of each node in the current set. 

##### Example

``` php
$nodes = $doc->find('a');
$closestAncestors = $nodes->closest('p');
```

---

#### contents

```
NodeList contents()
```
    
Return all children of each node in the current set.

##### Example

``` php
$nodes = $doc->find('p');
$contents = $nodes->contents();
```

---

#### eq

```
\DOMNode|null eq(int $index)
```
    
Return node in the current set at the specified index.

##### Example

``` php
$nodes = $doc->find('a');
$nodeAtIndexOne = $nodes->eq(1);
```

---

#### filter

```
NodeList filter(string|NodeList|\DOMNode|callable $input)
```
    
Return nodes in the current set that match the input. 

##### Example

``` php
$nodes = $doc->filter('a')
$exampleATags = $nodes->filter('[href*=https://example.org/]');
```

---

#### find

```
NodeList find(string $selector[, string $prefix = 'descendant::'])
```
    
Return the decendants of the current set filtered by the selector and optional XPath axes.

##### Example

``` php
$nodes = $doc->find('a');
```

---

#### first

```
mixed first()
```
    
Return the first node of the current set.

##### Example

``` php
$nodes = $doc->find('a');
$firstNode = $nodes->first();
```

---

#### has

```
NodeList has(string|NodeList|\DOMNode|callable $input)
```
    
Return nodes with decendants of the current set matching the input. 

##### Example

``` php
$nodes = $doc->find('a');
$anchorTags = $nodes->has('span');
```

---

#### is

```
bool is(string|NodeList|\DOMNode|callable $input)
```
    
Test if nodes from the current set match the input. 

##### Example

``` php
$nodes = $doc->find('a');
$isAnchor = $nodes->is('[anchor]');
```

---

#### last

```
mixed last()
```
    
Return the last node of the current set.

##### Example

``` php
$nodes = $doc->find('a');
$lastNode = $nodes->last();
```

---

#### map

```
NodeList map(callable $function)
```
    
Apply a callback to nodes in the current set and return a new NodeList.

##### Example

``` php
$nodes = $doc->find('a');
$nodeValues = $nodes->map(function($node) {
    return $node->nodeValue;
});
```

---

#### following

```
\DOMNode|null following([string|NodeList|\DOMNode|callable $selector = null])
```
    
Return the sibling immediately following each element node in the current set. 

*Optionally filtered by selector.*

##### Example

``` php
$nodes = $doc->find('a');
$follwingNodes = $nodes->following();
```

---

#### followingAll

```
NodeList followingAll([string|NodeList|\DOMNode|callable $selector = null])
```
    
Return all siblings following each element node in the current set.

*Optionally filtered by selector.*

##### Example

``` php
$nodes = $doc->find('a');
$follwingAllNodes = $nodes->followingAll('[anchor]');
```

---

#### followingUntil

```
NodeList followingUntil([[string|NodeList|\DOMNode|callable $input = null], string|NodeList|\DOMNode|callable $selector = null])
```
    
Return all siblings following each element node in the current set upto but not including the node matched by $input.

*Optionally filtered by input.*<br>
*Optionally filtered by selector.*

##### Example

``` php
$nodes = $doc->find('a');
$follwingUntilNodes = $nodes->followingUntil('.submit');
```

---

#### not

```
NodeList not(string|NodeList|\DOMNode|callable $input)
```
    
Return element nodes from the current set not matching the input. 

##### Example

``` php
$nodes = $doc->find('a');
$missingHrefAttribute = $nodes->not('[href]');
```

---

#### parent

```
Element|NodeList|null parent([string|NodeList|\DOMNode|callable $selector = null])
```
    
Return the immediate parent of each element node in the current set. 

*Optionally filtered by selector.*

##### Example

``` php
$nodes = $doc->find('a');
$parentNodes = $nodes->parent();
```

---

#### parents

```
NodeList parent([string $selector = null])
```
    
Return the ancestors of each element node in the current set. 

*Optionally filtered by selector.*

##### Example

``` php
$nodes = $doc->find('a');
$ancestorDivNodes = $nodes->parents('div');
```

---

#### parentsUntil

```
NodeList parentsUntil([[string|NodeList|\DOMNode|callable $input, [string|NodeList|\DOMNode|callable $selector = null])
```
    
Return the ancestors of each element node in the current set upto but not including the node matched by $selector.

*Optionally filtered by input.*<br>
*Optionally filtered by selector.*

##### Example

``` php
$nodes = $doc->find('a');
$ancestorDivNodes = $nodes->parentsUntil('div');
```

---

#### preceding

```
\DOMNode|null preceding([string|NodeList|\DOMNode|callable $selector = null])
```
    
Return the sibling immediately preceding each element node in the current set. 

*Optionally filtered by selector.*

##### Example

``` php
$nodes = $doc->find('a');
$precedingNodes = $nodes->preceding();
```

---

#### precedingAll

```
NodeList precedingAll([string|NodeList|\DOMNode|callable $selector = null])
```
    
Return all siblings preceding each element node in the current set.

*Optionally filtered by selector.*

##### Example

``` php
$nodes = $doc->find('a');
$precedingAllNodes = $nodes->precedingAll('[anchor]');
```

---
#### precedingUntil

```
NodeList precedingUntil([[string|NodeList|\DOMNode|callable $input = null], string|NodeList|\DOMNode|callable $selector = null])
```
    
Return all siblings preceding each element node in the current set upto but not including the node matched by $input.

*Optionally filtered by input.*<br>
*Optionally filtered by selector.*

##### Example

``` php
$nodes = $doc->find('a');
$precedingUntilNodes = $nodes->precedingUntil('.submit');
```

---

#### siblings

```
NodeList siblings([[string|NodeList|\DOMNode|callable $selector = null])
```
    
Return siblings of each element node in the current set.

*Optionally filtered by selector.*

##### Example

``` php
$nodes = $doc->find('p');
$siblings = $nodes->siblings();
```

---

#### slice

```
NodeList slice(int $start[, int $end])
```
    
Return a subset of the current set based on the start and end indexes.

##### Example

``` php
$nodes = $doc->find('p');
// Return nodes 1 through to 3 as a new NodeList
$slicedNodes = $nodes->slice(1, 3);
```

---

### Additional Methods

#### count

```
int count()
```

##### Example

``` php
$nodes = $doc->find('p');

echo $nodes->count();
```

---

#### each

```
self each(callable $function)
```

##### Example

``` php
$nodes = $doc->find('p');

$nodes->each(function($node){
    echo $node->nodeName . "\n";
});
```

## Licensing

PHP DOM Wrapper is licensed by Andrew Scott under the BSD 3-Clause License, see the LICENSE file for more details.
