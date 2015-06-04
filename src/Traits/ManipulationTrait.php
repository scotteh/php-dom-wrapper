<?php

namespace DOMWrap\Traits;

use DOMWrap\Text;
use DOMWrap\Element;
use DOMWrap\NodeList;

/**
 * Manipulation Trait
 *
 * @package DOMWrap\Traits
 * @license http://opensource.org/licenses/BSD-3-Clause BSD 3 Clause
 */
trait ManipulationTrait
{
    /** @see CommonTrait::collection() */
    abstract public function collection();

    /** @see CommonTrait::document() */
    abstract public function document();

    /** @see CommonTrait::result() */
    abstract public function result($nodeList);

    /** @see TraversalTrait::find() */
    abstract public function find($selector, $prefix = 'descendant::');

    /** @see TraversalTrait::findXPath() */
    abstract public function findXPath($xpath);

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
     * @return array|NodeList|\Traversable
     */
    protected function inputPrepareAsTraversable($input) {
        if ($input instanceof \DOMNode) {
            $nodes = [$input];
        } else if (is_string($input)) {
            $nodes = $this->nodesFromHtml($input);
        } else if (is_array($input) || $input instanceof \Traversable) {
            $nodes = $input;
        } else {
            throw new \InvalidArgumentException();
        }

        return $nodes;
    }

    /**
     * @param string|NodeList|\DOMNode $input
     *
     * @return NodeList
     */
    protected function inputAsNodeList($input) {
        $nodes = $this->inputPrepareAsTraversable($input);

        $newNodes = $this->newNodeList();

        foreach ($nodes as $node) {
            if ($node->document() !== $this->document()) {
                $newNodes[] = $this->document()->importNode($node, true);
            } else {
                $newNodes[] = $node;
            }
        }

        return $newNodes;
    }

    /**
     * @param string|NodeList|\DOMNode $input
     *
     * @return NodeList
     */
    protected function inputAsFirstNode($input) {
        $nodes = $this->inputAsNodeList($input);

        return $nodes->findXPath('self::*')->first();
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

        return $nodes;
    }

    /**
     * @param string|NodeList|\DOMNode|\Closure $input
     * @param \Closure $callback
     */
    protected function manipulateNodesWithInput($input, \Closure $callback) {
        $this->collection()->each(function($node, $index) use ($input, $callback) {
            $html = $input;

            if ($input instanceof \Closure) {
                $html = $input($node, $index);
            }

            $newNodes = $this->inputAsNodeList($html);

            $callback($node, $newNodes);
        });
    }

    /**
     * @param string|null $selector
     *
     * @return NodeList
     */
    public function detach($selector = null) {
        if (!is_null($selector)) {
            $nodes = $this->find($selector, 'self::');
        } else {
            $nodes = $this->collection();
        }

        $nodeList = $this->newNodeList();

        $nodes->each(function($node) use($nodeList) {
            if ($node->parent() instanceof \DOMNode) {
                $nodeList[] = $node->parent()->removeChild($node);
            }
        });

        $nodes->fromArray([]);

        return $nodeList;
    }

    /**
     * @param string|null $selector
     *
     * @return self
     */
    public function remove($selector = null) {
        $this->detach($selector);

        return $this;
    }

    /**
     * @param string|NodeList|\DOMNode|\Closure $input
     *
     * @return self
     */
    public function replaceWith($input) {
        $this->manipulateNodesWithInput($input, function($node, $newNodes) {
            foreach ($newNodes as $newNode) {
                $node->parent()->replaceChild($newNode, $node);
            }
        });

        return $this;
    }

    /**
     * @param string|NodeList|\DOMNode|\Closure $input
     *
     * @return string|self
     */
    public function text($input = null) {
        if (is_null($input)) {
            return $this->getText();
        } else {
            return $this->setText($input);
        }
    }

    /**
     * @return string
     */
    public function getText() {
        return $this->collection()->reduce(function($carry, $node) {
            return $carry . $node->textContent;
        }, '');
    }

    /**
     * @param string|NodeList|\DOMNode|\Closure $input
     *
     * @return string
     */
    public function setText($input) {
        if (is_string($input)) {
            $input = new Text($input);
        }

        $this->manipulateNodesWithInput($input, function($node, $newNodes) {
            // Remove old contents from the current node.
            $node->contents()->remove();

            // Add new contents in it's place.
            $node->append(new Text($newNodes->getText()));
        });
    }

    /**
     * @param string|NodeList|\DOMNode|\Closure $input
     *
     * @return self
     */
    public function before($input) {
        $this->manipulateNodesWithInput($input, function($node, $newNodes) {
            foreach ($newNodes as $newNode) {
                $node->parent()->insertBefore($newNode, $node);
            }
        });

        return $this;
    }

    /**
     * @param string|NodeList|\DOMNode|\Closure $input
     *
     * @return self
     */
    public function after($input) {
        $this->manipulateNodesWithInput($input, function($node, $newNodes) {
            foreach ($newNodes as $newNode) {
                if (is_null($node->following())) {
                    $node->parent()->appendChild($newNode);
                } else {
                    $node->parent()->insertBefore($newNode, $node->following());
                }
            }
        });

        return $this;
    }

    /**
     * @param string|NodeList|\DOMNode|\Closure $input
     *
     * @return self
     */
    public function prepend($input) {
        $this->manipulateNodesWithInput($input, function($node, $newNodes) {
            foreach ($newNodes as $newNode) {
                $node->insertBefore($newNode, $node->contents()->first());
            }
        });

        return $this;
    }

    /**
     * @param string|NodeList|\DOMNode|\Closure $input
     *
     * @return self
     */
    public function append($input) {
        $this->manipulateNodesWithInput($input, function($node, $newNodes) {
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
            $node->contents()->remove();
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
     * @param string $name
     *
     * @return bool
     */
    public function hasAttr($name) {
        return $this->collection()->reduce(function($carry, $node) use ($name) {
            if ($node->hasAttribute($name)) {
                return true;
            }

            return $carry;
        }, false);
    }

    /**
     * @internal
     *
     * @param string $name
     *
     * @return string|null
     */
    public function getAttr($name) {
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
    public function setAttr($name, $value) {
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
            return $this->getAttr($name);
        } else {
            return $this->setAttr($name, $value);
        }
    }

    /**
     * @internal
     *
     * @param string $name
     * @param string|\Closure $value
     * @param bool $addValue
     */
    protected function _pushAttrValue($name, $value, $addValue = false) {
        $this->collection()->each(function($node, $index) use($name, $value, $addValue) {
            if ($node instanceof \DOMElement) {
                $attr = $node->getAttribute($name);

                if ($value instanceof \Closure) {
                    $value = $value($node, $index, $attr);
                }

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
     * @param string|\Closure $class
     *
     * @return self
     */
    public function addClass($class) {
        $this->_pushAttrValue('class', $class, true);

        return $this;
    }

    /**
     * @param string|\Closure $class
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
        return $this->collection()->reduce(function($carry, $node) use ($class) {
            $attr = $node->getAttr('class');

            return array_reduce(explode(' ', $attr), function($carry, $item) use ($class) {
                if (strcasecmp($item, $class) == 0) {
                    return true;
                }

                return $carry;
            }, false);
        }, false);
    }

    /**
     * @param Element $node
     *
     * @return \SplStack
     */
    protected function _getFirstChildWrapStack(Element $node) {
        $stack = new \SplStack;

        do {
            // Push our current node onto the stack
            $stack->push($node);

            // Get the first element child node
            $node = $node->children()->first();
        } while ($node instanceof Element);

        // Get the top most node.
        return $stack;
    }

    /**
     * @param Element $node
     *
     * @return \SplStack
     */
    protected function _prepareWrapStack(Element $node) {
        // Generate a stack (root to leaf) of the wrapper.
        // Includes only first element nodes / first element children.
        $stackNodes = $this->_getFirstChildWrapStack($node);

        // Only using the first element, remove any siblings.
        foreach ($stackNodes as $stackNode) {
            $stackNode->siblings()->remove();
        }

        return $stackNodes;
    }

    /**
     * @param string|NodeList|\DOMNode|\Closure $input
     * @param \Closure $callback
     */
    protected function wrapWithInputByCallback($input, \Closure $callback) {
        $this->collection()->each(function($node, $index) use ($input, $callback) {
            $html = $input;

            if ($input instanceof \Closure) {
                $html = $input($node, $index);
            }

            $inputNode = $this->inputAsFirstNode($html);

            if ($inputNode instanceof Element) {
                // Pre-process wrapper into a stack of first element nodes.
                $stackNodes = $this->_prepareWrapStack($inputNode);

                $callback($node, $stackNodes);
            }
        });
    }

    /**
     * @param string|NodeList|\DOMNode|\Closure $input
     *
     * @return self
     */
    public function wrapInner($input) {
        $this->wrapWithInputByCallback($input, function($node, $stackNodes) {
            foreach ($node->contents() as $child) {
                // Remove child from the current node
                $oldChild = $child->detach()->first();

                // Add it back as a child of the top (leaf) node on the stack
                $stackNodes->top()->append($oldChild);
            }

            // Add the bottom (root) node on the stack
            $node->append($stackNodes->bottom());
        });

        return $this;
    }

    /**
     * @param string|NodeList|\DOMNode|\Closure $input
     *
     * @return self
     */
    public function wrap($input) {
        $this->wrapWithInputByCallback($input, function($node, $stackNodes) {
            // Add the new bottom (root) node after the current node
            $node->after($stackNodes->bottom());

            // Remove the current node
            $oldNode = $node->detach()->first();

            // Add the 'current node' back inside the new top (leaf) node.
            $stackNodes->top()->append($oldNode);
        });

        return $this;
    }

    /**
     * @param string|NodeList|\DOMNode|\Closure $input
     *
     * @return self
     */
    public function wrapAll($input) {
        if (!$this->collection()->count()) {
            return $this;
        }

        if ($input instanceof \Closure) {
            $input = $input($this->collection()->first());
        }

        $inputNode = $this->inputAsFirstNode($input);

        if (!($inputNode instanceof Element)) {
            return $this;
        }

        $stackNodes = $this->_prepareWrapStack($inputNode);

        // Add the new bottom (root) node before the first matched node
        $this->collection()->first()->before($stackNodes->bottom());

        $this->collection()->each(function($node) use ($stackNodes) {
            // Detach and add node back inside the new wrappers top (leaf) node.
            $stackNodes->top()->append($node->detach());
        });

        return $this;
    }

    /**
     * @return self
     */
    public function unwrap() {
        $this->collection()->each(function($node) {
            $parent = $node->parent();

            // Replace parent node (the one we're unwrapping) with it's children.
            $parent->contents()->each(function($childNode) use($parent) {
                $oldChildNode = $childNode->detach()->first();

                $parent->before($oldChildNode);
            });

            $parent->remove();
        });

        return $this;
    }

    /**
     * @return string
     */
    public function getOuterHtml() {
        return $this->document()->saveHTML(
            $this->collection()->first()
        );
    }

    /**
     * @return string
     */
    public function getHtml() {
        return $this->collection()->first()->children()->reduce(function($carry, $node) {
            return $carry . $this->document()->saveHTML($node);
        }, '');
    }

    /**
     * @param string|NodeList|\DOMNode|\Closure $input
     *
     * @return self
     */
    public function setHtml($input) {
        $this->manipulateNodesWithInput($input, function($node, $newNodes) {
            // Remove old contents from the current node.
            $node->contents()->remove();

            // Add new contents in it's place.
            $node->append($newNodes);
        });

        return $this;
    }

    /**
     * @param string|NodeList|\DOMNode|\Closure $input
     *
     * @return string|self
     */
    public function html($input = null) {
        if (is_null($input)) {
            return $this->getHtml();
        } else {
            return $this->setHtml($input);
        }
    }
}