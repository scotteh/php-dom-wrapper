<?php

namespace DOMWrap\Traits;

use DOMWrap\Collections\NodeList;
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
    public function prev($selector = '') {
        $result = $this->prevAll($selector, '[1]');

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
    public function prevAll($selector = '', $postfix = '') {
        return $this->findXPath(CssSelector::toXPath($selector, 'preceding-sibling::') . $postfix);
    }

    /**
     * @param string $selector 
     *
     * @return \DOMNode|null
     */
    public function next($selector = '') {
        $result = $this->nextAll($selector, '[1]');

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
    public function nextAll($selector = '', $postfix = '') {
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
                $node->prevAll($selector)->merge(
                    $node->nextAll($selector)
                )
            );
        }, $this->newNodeList());

        return $results;
    }

    /**
     * @param string|null $xpath 
     *
     * @return NodeList
     */
    public function siblingsXPath($xpath = null) {
        $results = $this->collection()->reduce(function($carry, $node) use ($xpath) {
            return $carry->merge(
                $node->prevAllXPath($xpath)->merge(
                    $node->nextAllXPath($xpath)
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
                $node->newNodeList($node->childNodes)
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
}