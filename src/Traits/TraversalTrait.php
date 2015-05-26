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

    /** @see ManipulationTrait::inputAsNodeList() */
    abstract public function inputAsNodeList($input);

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
     * @param string|NodeList|\DOMNode $input
     *
     * @return bool
     */
    public function is($input) {
        if (is_string($input)) {
            $inputNodes = $this->find($input, 'self::');

            return $inputNodes->count() != 0;
        } else {
            $inputNodes = $this->inputAsNodeList($input);

            return $inputNodes->reduce(function($carry, $inputNode) {
                foreach ($this->collection() as $node) {
                    if ($node === $inputNode) {
                        return true;
                    }

                    return $carry;
                }
            }, false);
        }
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
     * @param string $selector
     *
     * @return NodeList
     */
    public function parents($selector = null) {
        return $this->parentsUntil(null, $selector);
    }

    /**
     * @param array $parentLists
     *
     * @return NodeList
     */
    protected function _uniqueParents($parentLists) {
        $results = $this->newNodeList();

        foreach ($parentLists as $parentList) {
            foreach ($parentList as $parentNode) {
                if (!$results->exists($parentNode)) {
                    $results[] = $parentNode;
                }
            }
        }

        return $results->reverse();
    }

    /**
     * @param string|NodeList|\DOMNode $input
     * @param string $selector
     *
     * @return NodeList
     */
    public function parentsUntil($input = null, $selector = null) {
        $parentLists = [];

        $this->collection()->each(function($node) use($input, $selector, &$parentLists) {
            $parentNodes = $this->newNodeList();

            $parent = $node->parent();

            while ($parent instanceof Element) {
                if (is_null($selector) || $parent->is($selector)) {
                    $parentNodes[] = $parent;
                }

                if (!is_null($input) && $parent->is($input)) {
                    break;
                }

                $parent = $parent->parent();
            }

            $parentLists[] = $parentNodes;
        });

        return $this->_uniqueParents($parentLists);
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

    /**
     * @param string|NodeList|\DOMNode $input
     *
     * @return NodeList
     */
    public function add($input) {
        $nodes = $this->inputAsNodeList($input);

        $results = $this->collection()->merge(
            $nodes
        );

        return $results;
    }
}