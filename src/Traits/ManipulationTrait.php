<?php

namespace DOMWrap\Traits;

use DOMWrap\Collections\NodeList;

define('DOM_NODE_TEXT_DEFAULT', 0);
define('DOM_NODE_TEXT_TRIM', 1);
define('DOM_NODE_TEXT_NORMALISED', 2);

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

    /** @see Document::result(), NodeTrait::result() */
    abstract public function result($nodeList);

    /** @see TraversalTrait::newNodeList() */
    abstract public function newNodeList($nodes = []);

    /**
     * @param string|NodeList|\DOMNode $input
     *
     * @return NodeList
     */
    protected function inputAsNodeList($input) {
        if ($input instanceof \DOMNode) {
            $nodes = $this->newNodeList([$input]);
        } else if (is_string($input)) {
            $nodes = $this->nodesFromHtml($input);
        } else if ($input instanceof NodeList) {
            $nodes = $input;
        } else {
            throw new \InvalidArgumentException();
        }

        return $nodes;
    }

    /**
     * @param string $html
     *
     * @return NodeList
     */
    protected function nodesFromHtml($html) {
        $class = get_class($this->document());
        $doc = new $class();
        $nodes = $doc->html($html)->find('body > *');

        $newNodes = $this->newNodeList();

        foreach ($nodes as $node) {
            $newNodes[] = $this->document()->importNode($node, true);
        }

        return $newNodes;
    }

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

    /**
     * @param string|NodeList|\DOMNode $input
     *
     * @return self
     */
    public function before($input) {
        $this->collection()->each(function($node) use($input) {
            $newNodes = $this->inputAsNodeList($input);

            foreach ($newNodes as $newNode) {
                $node->parent()->insertBefore($newNode, $node);
            }
        });

        return $this;
    }

    /**
     * @param string|NodeList|\DOMNode $input
     *
     * @return self
     */
    public function after($input) {
        $this->collection()->each(function($node) use($input) {
            $newNodes = $this->inputAsNodeList($input);

            foreach ($newNodes as $newNode) {
                if (is_null($node->next())) {
                    $node->parent()->appendChild($newNode);
                } else {
                    $node->parent()->insertBefore($newNode, $node->next());
                }
            }
        });

        return $this;
    }

    /**
     * @param string|NodeList|\DOMNode $input
     *
     * @return self
     */
    public function prepend($input) {
        $this->collection()->each(function($node) use($input) {
            $newNodes = $this->inputAsNodeList($input);

            foreach ($newNodes as $newNode) {
                $node->insertBefore($newNode, $node->children()->first());
            }
        });

        return $this;
    }

    /**
     * @param string|NodeList|\DOMNode $input
     *
     * @return self
     */
    public function append($input) {
        $this->collection()->each(function($node) use($input) {
            $newNodes = $this->inputAsNodeList($input);

            foreach ($newNodes as $newNode) {
                $node->appendChild($newNode);
            }
        });

        return $this;
    }

}