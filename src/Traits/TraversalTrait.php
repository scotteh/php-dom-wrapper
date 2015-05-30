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
    /** @see CommonTrait::collection() */
    abstract public function collection();

    /** @see CommonTrait::document() */
    abstract public function document();

    /** @see CommonTrait::result() */
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
     * @param string|NodeList|\DOMNode|\Closure $input
     *
     * @return bool
     */
    public function is($input) {
        if (is_string($input)) {
            $inputNodes = $this->find($input, 'self::');

            return $inputNodes->count() != 0;
        } else if ($input instanceof \Closure) {
            return $this->collection()->reduce(function($carry, $node) use ($input) {
                if ($input($node)) {
                    return true;
                }

                return $carry;
            }, false);
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
     * @param string|NodeList|\DOMNode|\Closure $selector
     *
     * @return \DOMNode|null
     */
    public function preceding($selector = null) {
        return $this->precedingUntil(null, $selector)->first();
    }

    /**
     * @param string|NodeList|\DOMNode|\Closure $selector
     *
     * @return NodeList
     */
    public function precedingAll($selector = null) {
        return $this->precedingUntil(null, $selector);
    }

    /**
     * @param string|NodeList|\DOMNode $input
     * @param string|NodeList|\DOMNode|\Closure $selector
     *
     * @return NodeList
     */
    public function precedingUntil($input = null, $selector = null) {
        return $this->_walkPathUntil('previousSibling', $input, $selector);
    }

    /**
     * @param string|NodeList|\DOMNode|\Closure $selector 
     *
     * @return \DOMNode|null
     */
    public function following($selector = null) {
        return $this->followingUntil(null, $selector)->first();
    }

    /**
     * @param string|NodeList|\DOMNode|\Closure $selector 
     *
     * @return NodeList
     */
    public function followingAll($selector = null) {
        return $this->followingUntil(null, $selector);
    }

    /**
     * @param string|NodeList|\DOMNode $input
     * @param string|NodeList|\DOMNode|\Closure $selector
     *
     * @return NodeList
     */
    public function followingUntil($input = null, $selector = null) {
        return $this->_walkPathUntil('nextSibling', $input, $selector);
    }

    /**
     * @param string|NodeList|\DOMNode|\Closure $selector 
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
     * @param string|NodeList|\DOMNode $input
     * @param string $selector
     *
     * @return NodeList
     */
    public function parentsUntil($input = null, $selector = null) {
        return $this->_walkPathUntil('parentNode', $input, $selector);
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

    /**
     * @param \DOMNode $baseNode
     * @param string $property
     * @param string|NodeList|\DOMNode $input
     * @param string $selector
     *
     * @return NodeList
     */
    protected function _buildNodeListUntil($baseNode, $property, $input = null, $selector = null) {
        $resultNodes = $this->newNodeList();

        // Get our first node
        $node = $baseNode->$property;

        // Keep looping until we're either out of nodes, or at the root of the DOM.
        while ($node instanceof \DOMNode &&
               !($node instanceof \DOMDocument))
        {
            // Filter nodes
            if (is_null($selector) || $node->is($selector)) {
                $resultNodes[] = $node;
            }

            // 'Until' check
            if (!is_null($input) && $node->is($input)) {
                break;
            }

            // Find the next node
            $node = $node->$property;
        }

        return $resultNodes;
    }

    /**
     * @param array $nodeLists
     *
     * @return NodeList
     */
    protected function _uniqueNodes($nodeLists) {
        $resultNodes = $this->newNodeList();

        // Loop through our array of NodeLists
        foreach ($nodeLists as $nodeList) {
            // Each node in the NodeList
            foreach ($nodeList as $node) {
                // We're only interested in unique nodes
                if (!$resultNodes->exists($node)) {
                    $resultNodes[] = $node;
                }
            }
        }

        // Sort resulting NodeList: outer-most => inner-most.
        return $resultNodes->reverse();
    }

    /**
     * @param string $property
     * @param string|NodeList|\DOMNode $input
     * @param string $selector
     *
     * @return NodeList
     */
    protected function _walkPathUntil($property, $input = null, $selector = null) {
        $nodeLists = [];

        $this->collection()->each(function($node) use($property, $input, $selector, &$nodeLists) {
            $nodeLists[] = $this->_buildNodeListUntil($node, $property, $input, $selector);
        });

        return $this->_uniqueNodes($nodeLists);
    }
}