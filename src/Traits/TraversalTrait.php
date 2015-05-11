<?php

namespace DOMWrap\Traits;

use DOMWrap\Collections\NodeList;
use Symfony\Component\CssSelector\CssSelector;

define('DOM_NODE_TEXT_DEFAULT', 0);
define('DOM_NODE_TEXT_TRIM', 1);
define('DOM_NODE_TEXT_NORMALISED', 2);

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

    /** @see Document::node(), NodeTrait::node() */
    abstract public function node();

    /**
     * @param Traversable|array $nodes
     *
     * @return NodeList
     */
    public function newNodeList($nodes = null) {
        if (!is_array($nodes) && !($nodes instanceof \Traversable)) {
            $nodes = [];
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
                $this->newNodeList($domxpath->query($xpath, $node))
            );
        }

        return $results;
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
     * @param string $xpath
     *
     * @return bool
     */
    public function isXPath($xpath) {
        $nodes = $this->findXPath($xpath);

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
     * @param selector|null $selector 
     *
     * @return \DOMNode|null
     */
    public function prev($selector = null) {
        return $this->prevXPath(CssSelector::toXPath($selector, 'self::'));
    }

    /**
     * @param selector|null $selector 
     *
     * @return NodeList
     */
    public function prevAll($selector = null) {
        return $this->prevAllXPath(CssSelector::toXPath($selector, 'self::'));
    }

    /**
     * @param string|null $xpath
     *
     * @return \DOMNode|null
     */
    public function prevXPath($xpath = null) {
        return $this->prevAllXPath($xpath)->first();
    }

    /**
     * @param string|null $xpath
     *
     * @return NodeList
     */
    public function prevAllXPath($xpath = null) {
        $results = $this->collection()->reduce(function($carry, $child) use ($xpath) {
            $nodes = [];

            for ($sibling = $child; ($sibling = $sibling->previousSibling) !== null;) {
                if (empty($xpath) || $this->isXPath($xpath)) {
                    $nodes[] = $sibling;
                }
            }

            return array_merge($carry, $nodes);
        }, []);

        return $this->newNodeList($results);
    }

    /**
     * @param selector|null $selector 
     *
     * @return \DOMNode|null
     */
    public function next($selector = null) {
        return $this->nextXPath(CssSelector::toXPath($selector, 'self::'));
    }

    /**
     * @param selector|null $selector 
     *
     * @return NodeList
     */
    public function nextAll($selector = null) {
        return $this->nextAllXPath(CssSelector::toXPath($selector, 'self::'));
    }

    /**
     * @param string|null $xpath
     *
     * @return \DOMNode|null
     */
    public function nextXPath($xpath = null) {
        return $this->nextAllXPath($xpath)->first();
    }

    /**
     * @param string|null $xpath
     *
     * @return NodeList
     */
    public function nextAllXPath($xpath = null) {
        $results = $this->collection()->reduce(function($carry, $child) use ($xpath) {
            $nodes = [];

            for ($sibling = $child; ($sibling = $sibling->nextSibling) !== null;) {
                if (empty($xpath) || $this->isXPath($xpath)) {
                    $nodes[] = $sibling;
                }
            }

            return array_merge($carry, $nodes);
        }, []);

        return $this->newNodeList($results);
    }

    /**
     * @param string|null $selector 
     *
     * @return NodeList
     */
    public function siblings($selector = null) {
        return $this->collection()->reduce(function($carry, $node) use ($selector) {
            return $carry->merge(
                $node->prevAll($selector)->merge(
                    $node->nextAll($selector)
                )
            );
        }, $this->newNodeList());
    }

    /**
     * NodeList is only array like. Removing items using foreach() has undesired results.
     *
     * @return NodeList
     */
    public function children() {
        return $this->collection()->reduce(function($carry, $node) {
            return $carry->merge(
                $node->newNodeList($node->childNodes)
            );
        }, $this->newNodeList());
    }

    /**
     * @return Element
     */
    public function parent() {
        return $this->collection()->reduce(function($carry, $node) {
            if ($node->parentNode instanceof \DOMDocument) {
                return $carry;
            }

            return $carry->merge(
                $node->newNodeList($node->parentNode)
            );
        }, $this->newNodeList());
    }
}