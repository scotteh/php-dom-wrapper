<?php

namespace DOMWrap\Traits;

use DOMWrap\Element;
use DOMWrap\NodeList;
use Symfony\Component\CssSelector\CssSelector;

/**
 * Traversal Trait
 *
 * @package DOMWrap\Traits
 * @license http://opensource.org/licenses/BSD-3-Clause BSD 3 Clause
 */
trait TraversalTrait
{
    /** @see Document::collection(), NodeTrait::collection() */
    abstract public function collection();

    /** @see Document::document(), NodeTrait::document() */
    abstract public function document();

    /** @see Document::result(), NodeTrait::result() */
    abstract public function result($nodeList);

    /**
     * @param Traversable|array $nodes
     *
     * @return NodeList
     */
    public function newNodeList($nodes = null) {
        if (!is_array($nodes) && !($nodes instanceof \Traversable)) {
            if (!is_null($nodes)) {
                $nodes = [$nodes];
            } else {
                $nodes = [];
            }
        }

        return new NodeList($this->document(), $nodes);
    }

    /**
     * @param string $selector
     * @param string $prefix
     *
     * @return NodeList
     */
    public function find($selector, $prefix = 'descendant::') {
        return $this->findXPath(CssSelector::toXPath($selector, $prefix));
    }

    /**
     * @param string $xpath
     *
     * @return NodeList
     */
    public function findXPath($xpath) {
        $results = $this->newNodeList();

        $domxpath = new \DOMXPath($this->document());

        foreach ($this->collection() as $node) {
            $results = $results->merge(
                $node->newNodeList($domxpath->query($xpath, $node))
            );
        }

        return $results;
    }

    /**
     * @param string $selector
     *
     * @return NodeList
     */
    public function filter($selector) {
        return $this->collection()->map(function($obj) use($selector) {
            if (!$obj->is($selector)) {
                return null;
            }

            return $obj;
        });
    }

    /**
     * @param string $xpath
     *
     * @return NodeList
     */
    public function filterXPath($xpath) {
        return $this->collection()->map(function($obj) use($xpath) {
            if ($obj->findXPath($xpath)->count() == 0) {
                return null;
            }

            return $obj;
        });
    }

    /**
     * @param string $selector
     *
     * @return bool
     */
    public function is($selector) {
        $nodes = $this->find($selector, 'self::');

        return $nodes->count() != 0;
    }

    /**
     * @param string $selector
     *
     * @return bool
     */
    public function has($selector) {
        $nodes = $this->find($selector);

        return $nodes->count() != 0;
    }

    /**
     * @param string $selector 
     *
     * @return \DOMNode|null
     */
    public function preceding($selector = '') {
        $result = $this->precedingAll($selector, '[1]');

        if (!$result->count()) {
            return null;
        }

        return $result->first();
    }

    /**
     * @param string $selector 
     *
     * @return NodeList
     */
    public function precedingAll($selector = '', $postfix = '') {
        return $this->findXPath(CssSelector::toXPath($selector, 'preceding-sibling::') . $postfix);
    }

    /**
     * @param string $selector 
     *
     * @return \DOMNode|null
     */
    public function following($selector = '') {
        $result = $this->followingAll($selector, '[1]');

        if (!$result->count()) {
            return null;
        }

        return $result->first();
    }

    /**
     * @param string $selector 
     *
     * @return NodeList
     */
    public function followingAll($selector = '', $postfix = '') {
        return $this->findXPath(CssSelector::toXPath($selector, 'following-sibling::') . $postfix);
    }

    /**
     * @param string|null $selector 
     *
     * @return NodeList
     */
    public function siblings($selector = null) {
        $results = $this->collection()->reduce(function($carry, $node) use ($selector) {
            return $carry->merge(
                $node->precedingAll($selector)->merge(
                    $node->followingAll($selector)
                )
            );
        }, $this->newNodeList());

        return $results;
    }

    /**
     * NodeList is only array like. Removing items using foreach() has undesired results.
     *
     * @return NodeList
     */
    public function children() {
        $results = $this->collection()->reduce(function($carry, $node) {
            return $carry->merge(
                $node->findXPath('child::*')
            );
        }, $this->newNodeList());

        return $results;
    }

    /**
     * @return Element|NodeList|null
     */
    public function parent() {
        $results = $this->collection()->reduce(function($carry, $node) {
            // Don't try and ready parentNode property on \DOMDocument, it's already the top node.
            if ($node instanceof \DOMDocument) {
                return $carry;
            }

            return $carry->merge(
                $node->newNodeList($node->parentNode)
            );
        }, $this->newNodeList());

        return $this->result($results);
    }

    /**
     * @param int $index
     *
     * @return \DOMNode|null
     */
    public function eq($index) {
        return $this->collection()->offsetGet($index);
    }

    /**
     * @param int $start
     * @param int $end
     *
     * @return NodeList
     */
    public function slice($start, $end = null) {
        $nodeList = array_slice($this->collection()->toArray(), $start, $end);

        return $this->newNodeList($nodeList);
    }

    /**
     * @return NodeList
     */
    public function parents() {
        $parents = $this->newNodeList();

        $this->collection()->each(function($node) use($parents) {
            $parent = $node->parent();

            while ($parent instanceof Element) {
                $parents[] = $parent;

                $parent = $parent->parent();
            }
        });

        return $parents->reverse();
    }

    /**
     * @return \DOMNode
     */
    public function intersect() {
        if ($this->collection()->count() < 2) {
            return $this->collection()->first();
        }

        $nodeParents = [];

        // Build a multi-dimensional array of the collection nodes parent elements
        $this->collection()->each(function($node) use(&$nodeParents) {
            $nodeParents[] = $node->parents()->unshift($node)->toArray();
        });

        // Find the common parent
        $diff = call_user_func_array('array_uintersect', array_merge($nodeParents, [function($a, $b) {
            return strcmp(spl_object_hash($a), spl_object_hash($b));
        }]));

        return array_shift($diff);
    }

    /**
     * @param string $selector
     *
     * @return \DOMNode
     */
    public function closest($selector) {
        return $this->findXPath(CssSelector::toXPath($selector, 'ancestor::') . '[1]')->first();
    }

    /**
     * NodeList is only array like. Removing items using foreach() has undesired results.
     *
     * @return NodeList
     */
    public function contents() {
        $results = $this->collection()->reduce(function($carry, $node) {
            return $carry->merge(
                $node->newNodeList($node->childNodes)
            );
        }, $this->newNodeList());

        return $results;
    }

    /**
     * @param string $selector
     *
     * @return NodeList
     */
    public function not($selector) {
        $results = $this->collection()->reduce(function($carry, $node) use($selector) {
            $nodeList = $this->findXPath(CssSelector::toXPath($selector, 'self::'));

            if (!$nodeList->count()) {
                $carry[] = $node;
            }
        }, $this->newNodeList());

        return $results;
    }
}