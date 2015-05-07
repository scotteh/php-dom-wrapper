<?php

namespace DOMWrap\Traits;

use DOMWrap\Collections\NodeList;

/**
 * Manipulation Trait
 *
 * @package DOMWrap\Traits
 * @license http://opensource.org/licenses/BSD-3-Clause BSD 3 Clause
 */
trait ManipulationTrait
{
    /** @see TraversalTrait::find() */
    abstract public function find($selector, $prefix = 'descendant::');

    /** @see TraversalTrait::findXPath() */
    abstract public function findXPath($xpath);

    /** @see Document::collection(), NodeTrait::collection() */
    abstract public function collection();

    /** @see Document::document(), NodeTrait::document() */
    abstract public function document();

    /** @see Document::node(), NodeTrait::node() */
    abstract public function node();

    /** @see TraversalTrait::newNodeList() */
    abstract public function newNodeList($nodes = []);

    /**
     * @param string|null $selector
     *
     * @return self
     */
    public function remove($selector = null) {
        if (!is_null($selector)) {
            $nodes = $this->find($selector);
        } else {
            $nodes = $this->collection();
        }

        $nodes->each(function($node) {
            if ($node instanceof \DOMNode && $node->parent() instanceof \DOMNode) {
                $node->parent()->removeChild($node);
            }
        });

        $nodes->fromArray([]);

        return $this;
    }

    /**
     * @param \DOMNode $newNode
     *
     * @return self
     */
    public function replaceWith($newNode) {
        $this->collection()->each(function($parent) use ($newNode) {
            if ($parent->parent() instanceof \DOMNode) {
                $parent->parent()->replaceChild($newNode, $parent);
            }
        });

        return $this;
    }

    /**
     * @param \DOMNode|NodeList $nodes
     *
     * @return self
     */
    public function append($nodes) {
        if (!($nodes instanceof NodeList)) {
            $nodes = $this->newNodeList([$nodes]);
        }

        $this->collection()->each(function($parent) use ($nodes) {
            foreach ($nodes as $node) {
                $parent->appendChild($node);
            }
        });

        return $this;
    }

    /**
     * @param int $flag
     *
     * @return string
     */
    public function text($flag = 0) {
        $text = $this->collection()->reduce(function($carry, $node) {
            return $carry . $node->textContent;
        });

        if ($flag & DOM_NODE_TEXT_NORMALISED) {
            $text = preg_replace('@[\n\r\s\t]+@', " ", $text);
        }

        if ($flag & (DOM_NODE_TEXT_TRIM | DOM_NODE_TEXT_NORMALISED)) {
            $text = trim($text);
        }

        return $text;
    }
}