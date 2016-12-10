<?php

namespace DOMWrap\Traits;

use DOMWrap\Element;
use DOMWrap\NodeList;
use Symfony\Component\CssSelector\CssSelectorConverter;

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
        $converter = new CssSelectorConverter();

        return $this->findXPath($converter->toXPath($selector, $prefix));
    }

    /**
     * @param string $xpath
     *
     * @return NodeList
     */
    public function findXPath($xpath) {
        $results = $this->newNodeList();

        if ($this->isRemoved()) {
            return $results;
        }

        $domxpath = new \DOMXPath($this->document());

        foreach ($this->collection() as $node) {
            $results = $results->merge(
                $node->newNodeList($domxpath->query($xpath, $node))
            );
        }

        return $results;
    }

    /**
     * @param string|NodeList|\DOMNode|\Closure $input
     * @param bool $matchType
     *
     * @return NodeList
     */
    protected function getNodesMatchingInput($input, $matchType = true) {
        if ($input instanceof NodeList || $input instanceof \DOMNode) {
            $inputNodes = $this->inputAsNodeList($input);

            $fn = function($node) use ($inputNodes) {
                return $inputNodes->exists($node);
            };

        } elseif ($input instanceof \Closure) {
            // Since we're at the behest of the input \Closure, the 'matched'
            //  return value is always true.
            $matchType = true;

            $fn = $input;

        } elseif (is_string($input)) {
            $fn = function($node) use ($input) {
                return $node->find($input, 'self::')->count() != 0;
            };

        } else {
            throw new \InvalidArgumentException('Unexpected input value of type "' . gettype($input) . '"');
        }

        // Build a list of matching nodes.
        return $this->collection()->map(function($node) use ($fn, $matchType) {
            if ($fn($node) !== $matchType) {
                return null;
            }

            return $node;
        });
    }

    /**
     * @param string|NodeList|\DOMNode|\Closure $input
     *
     * @return bool
     */
    public function is($input) {
        return $this->getNodesMatchingInput($input)->count() != 0;
    }

    /**
     * @param string|NodeList|\DOMNode|\Closure $input
     *
     * @return NodeList
     */
    public function not($input) {
        return $this->getNodesMatchingInput($input, false);
    }

    /**
     * @param string|NodeList|\DOMNode|\Closure $input
     *
     * @return NodeList
     */
    public function filter($input) {
        return $this->getNodesMatchingInput($input);
    }

    /**
     * @param string|NodeList|\DOMNode|\Closure $input
     *
     * @return NodeList
     */
    public function has($input) {
        if ($input instanceof NodeList || $input instanceof \DOMNode) {
            $inputNodes = $this->inputAsNodeList($input);

            $fn = function($node) use ($inputNodes) {
                $descendantNodes = $node->find('*', 'descendant::');

                // Determine if we have a descendant match.
                return $inputNodes->reduce(function($carry, $inputNode) use ($descendantNodes) {
                    // Match descendant nodes against input nodes.
                    if ($descendantNodes->exists($inputNode)) {
                        return true;
                    }

                    return $carry;
                }, false);
            };

        } elseif (is_string($input)) {
            $fn = function($node) use ($input) {
                return $node->find($input, 'descendant::')->count() != 0;
            };

        } elseif ($input instanceof \Closure) {
            $fn = $input;

        } else {
            throw new \InvalidArgumentException('Unexpected input value of type "' . gettype($input) . '"');
        }

        return $this->getNodesMatchingInput($fn);
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
     * @param string|NodeList|\DOMNode|\Closure $input
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
     * @param string|NodeList|\DOMNode|\Closure $input
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
     * @param string|NodeList|\DOMNode|\Closure $selector
     *
     * @return Element|NodeList|null
     */
    public function parent($selector = null) {
        $results = $this->_walkPathUntil('parentNode', null, $selector, self::$MATCH_TYPE_FIRST);

        return $this->result($results);
    }

    /**
     * @param int $index
     *
     * @return \DOMNode|null
     */
    public function eq($index) {
        if ($index < 0) {
            $index = $this->collection()->count() + $index;
        }

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
     * @param string|NodeList|\DOMNode|\Closure $input
     * @param string|NodeList|\DOMNode|\Closure $selector
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
     * @param string|NodeList|\DOMNode|\Closure $input
     *
     * @return Element|NodeList|null
     */
    public function closest($input) {
        $results = $this->_walkPathUntil('parentNode', $input, null, self::$MATCH_TYPE_LAST);

        return $this->result($results);
    }

    /**
     * NodeList is only array like. Removing items using foreach() has undesired results.
     *
     * @return NodeList
     */
    public function contents() {
        $results = $this->collection()->reduce(function($carry, $node) {
            if($node->isRemoved())
                return $this->newNodeList();
            return $carry->merge(
                $node->newNodeList($node->childNodes)
            );
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

    /** @var int */
    private static $MATCH_TYPE_FIRST = 1;

    /** @var int */
    private static $MATCH_TYPE_LAST = 2;

    /**
     * @param \DOMNode $baseNode
     * @param string $property
     * @param string|NodeList|\DOMNode|\Closure $input
     * @param string|NodeList|\DOMNode|\Closure $selector
     * @param int $matchType
     *
     * @return NodeList
     */
    protected function _buildNodeListUntil($baseNode, $property, $input = null, $selector = null, $matchType = null) {
        $resultNodes = $this->newNodeList();

        // Get our first node
        $node = $baseNode->$property;

        // Keep looping until we're either out of nodes, or at the root of the DOM.
        while ($node instanceof \DOMNode &&
               !($node instanceof \DOMDocument))
        {
            // Filter nodes if not matching last
            if ($matchType != self::$MATCH_TYPE_LAST && (is_null($selector) || $node->is($selector))) {
                $resultNodes[] = $node;
            }

            // 'Until' check or first match only
            if ($matchType == self::$MATCH_TYPE_FIRST || (!is_null($input) && $node->is($input))) {
                // Set last match
                if ($matchType == self::$MATCH_TYPE_LAST) {
                    $resultNodes[] = $node;
                }

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
     * @param string|NodeList|\DOMNode|\Closure $input
     * @param string|NodeList|\DOMNode|\Closure $selector
     * @param int $matchType
     *
     * @return NodeList
     */
    protected function _walkPathUntil($property, $input = null, $selector = null, $matchType = null) {
        $nodeLists = [];

        $this->collection()->each(function($node) use($property, $input, $selector, $matchType, &$nodeLists) {
            $nodeLists[] = $this->_buildNodeListUntil($node, $property, $input, $selector, $matchType);
        });

        return $this->_uniqueNodes($nodeLists);
    }
}