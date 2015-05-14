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
     * Magic method - Trap function names using reserved keyword (empty, clone, etc..)
     *
     * @param string $name
     * @param mixed $arguments
     *
     * @return mixed
     */
    public function __call($name, $arguments) {
        if (!method_exists($this, '_' . $name)) {
            throw new \BadMethodCallException("Call to undefined method " . get_class($this) . '::' . $name . "()");
        }

        return call_user_func_array([$this, '_' . $name], $arguments);
    }

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
            if ($node->parent() instanceof \DOMNode) {
                $node->parent()->removeChild($node);
            }
        });

        $nodes->fromArray([]);

        return $this;
    }

    /**
     * @param string|NodeList|\DOMNode $input
     *
     * @return self
     */
    public function replaceWith($input) {
        $this->collection()->each(function($node) use ($input) {
            $newNodes = $this->inputAsNodeList($input);

            foreach ($newNodes as $newNode) {
                $node->parent()->replaceChild($newNode, $node);
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

    /**
     * @param string|NodeList|\DOMNode $input
     *
     * @return self
     */
    public function html($input) {
        $this->collection()->each(function($node) use($input) {
            $newNodes = $this->inputAsNodeList($input);

            $node->children()->remove();

            foreach ($newNodes as $newNode) {
                $node->appendChild($newNode);
            }
        });

        return $this;
    }

    /**
     * @return self
     */
    public function _empty() {
        $this->collection()->each(function($node) {
            $node->children()->remove();
        });

        return $this;
    }

    /**
     * @return NodeList|\DOMNode
     */
    public function _clone() {
        $clonedNodes = $this->newNodeList();

        $this->collection()->each(function($node) use($clonedNodes) {
            $clonedNodes[] = $node->cloneNode(true);
        });

        return $this->result($clonedNodes);
    }

    /**
     * @param string $name
     *
     * @return self
     */
    public function removeAttr($name) {
        $this->collection()->each(function($node) use($name) {
            if ($node instanceof \DOMElement) {
                $node->removeAttribute($name);
            }
        });

        return $this;
    }

    /**
     * @internal
     *
     * @param string $name
     *
     * @return string|null
     */
    protected function _getAttr($name) {
        $node = $this->collection()->first();

        if (!($node instanceof \DOMElement)) {
            return null;
        }

        $result = $node->getAttribute($name);

        if (empty($result)) {
            return null;
        }

        return $result;
    }

    /**
     * @internal
     *
     * @param string $name
     * @param string|null $value
     *
     * @return self
     */
    protected function _setAttr($name, $value) {
        $this->collection()->each(function($node) use($name, $value) {
            if ($node instanceof \DOMElement) {
                $node->setAttribute($name, $value);
            }
        });

        return $this;
    }

    /**
     * @param string $name
     * @param string|null $value
     *
     * @return self|mixed
     */
    public function attr($name, $value = null) {
        if (is_null($value)) {
            return $this->_getAttr($name);
        } else {
            return $this->_setAttr($name, $value);
        }
    }

    /**
     * @internal
     *
     * @param string $name
     * @param string $value
     * @param bool $addValue
     */
    protected function _pushAttrValue($name, $value, $addValue = false) {
        $this->collection()->each(function($node) use($name, $value, $addValue) {
            if ($node instanceof \DOMElement) {
                $attr = $node->getAttribute($name);

                // Remove any existing instances of the value, or empty values.
                $values = array_filter(explode(' ', $attr), function($_value) use($value) {
                    if (strcasecmp($_value, $value) == 0 || empty($_value)) {
                        return false;
                    }

                    return true;
                });

                // If required add attr value to array
                if ($addValue) {
                    $values[] = $value;
                }

                // Set the attr if we either have values, or the attr already
                //  existed (we might be removing classes).
                //
                // Don't set the attr if it doesn't already exist.
                if (!empty($values) || $node->hasAttribute($name)) {
                    $node->setAttribute($name, implode(' ', $values));
                }
            }
        });
    }

    /**
     * @param string $class
     *
     * @return self
     */
    public function addClass($class) {
        $this->_pushAttrValue('class', $class, true);

        return $this;
    }

    /**
     * @param string $class
     *
     * @return self
     */
    public function removeClass($class) {
        $this->_pushAttrValue('class', $class);

        return $this;
    }

    /**
     * @param string $class
     *
     * @return bool
     */
    public function hasClass($class) {
        $attr = $this->_getAttr('class');

        $exists = array_reduce(explode(' ', $attr), function($carry, $item) use($class) {
            if ($carry || strcasecmp($item, $class) == 0) {
                return true;
            }

            return false;
        }, false);

        return $exists;
    }
}