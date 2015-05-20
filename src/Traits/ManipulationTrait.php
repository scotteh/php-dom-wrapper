<?php

namespace DOMWrap\Traits;

use DOMWrap\Document;
use DOMWrap\Element;
use DOMWrap\NodeList;

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

    /** @see TraversalTrait::intersect() */
    abstract public function intersect();

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
            $nodes = [$input];
        } else if (is_string($input)) {
            $nodes = $this->nodesFromHtml($input);
        } else if (is_array($input) || $input instanceof \Traversable) {
            $nodes = $input;
        } else {
            throw new \InvalidArgumentException();
        }

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

        return $nodes->filterXPath('self::*')->first();
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
     * @param string|null $selector
     *
     * @return NodeList
     */
    public function detach($selector = null) {
        if (!is_null($selector)) {
            $nodes = $this->find($selector);
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
        if (trim($input) === '') {
            return $this;
        }

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
            $node = $node->children()->filterXPath('self::*')->first();
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
     * @param string|NodeList|\DOMNode $input
     *
     * @return self
     */
    public function wrapInner($input) {
        $this->collection()->each(function($node) use ($input) {
            $inputNode = $this->inputAsFirstNode($input);

            if ($inputNode instanceof Element) {
                // Pre-process wrapper into a stack of first element nodes.
                $stackNodes = $this->_prepareWrapStack($inputNode);

                foreach ($node->children() as $child) {
                    // Remove child from the current node
                    $oldChild = $node->removeChild($child);

                    // Add it back as a child of the top (leaf) node on the stack
                    $stackNodes->top()->append($oldChild);
                }

                // Add the bottom (root) node on the stack
                $node->append($stackNodes->bottom());
            }
        });

        return $this;
    }

    /**
     * @param string|NodeList|\DOMNode $input
     *
     * @return self
     */
    public function wrap($input) {
        $this->collection()->each(function($node) use ($input) {
            $inputNode = $this->inputAsFirstNode($input);

            if ($inputNode instanceof Element) {
                // Pre-process wrapper into a stack of first element nodes.
                $stackNodes = $this->_prepareWrapStack($inputNode);

                // Add the new bottom (root) node after the current node
                $node->after($stackNodes->bottom());

                // Remove the current node
                $oldNode = $node->parent()->removeChild($node);

                // Add the 'current node' back inside the new top (leaf) node.
                $stackNodes->top()->append($oldNode);
            }
        });

        return $this;
    }

    /**
     * @param NodeList $nodeList
     *
     * @return NodeList
     */
    protected function _getNodesBetween(NodeList $nodeList) {
        $nodeList = clone $nodeList;

        if ($nodeList->count() < 2) {
            return $nodeList;
        }

        $parentNode = $nodeList->get(0)->parent();

        $foundStart = false;
        $newNodeList = $this->newNodeList();

        // Loop through children of the common parent
        foreach ($parentNode->children() as $childNode) {
            $childNodeExists = $nodeList->exists($childNode);

            // Find the starting point
            if ($childNodeExists) {
                $nodeList->delete($childNode);

                if (!$newNodeList->count()) {
                    $foundStart = true;
                }
            }

            // Add all nodes start => end.
            // Includes all non-selected siblings located in-between selected nodes.
            if ($foundStart) {
                $newNodeList[] = $childNode;
            }

            // Find the ending point
            if ($childNodeExists) {
                if (!$nodeList->count()) {
                    $foundStart = false;
                }
            }
        }

        return $newNodeList;
    }

    /**
     * @param \DOMNode $commonNode
     *
     * @return NodeList
     */
    protected function _getWrapAllNodes($commonNode) {
        // Determine the nodes / common ancestor nodes relative to $commonNode.
        $matchedNodes = $this->collection()->map(function($node) use($commonNode) {
            $parents = $node->parents()->toArray();

            $index = array_search($commonNode, $parents, true);

            if ($index > 0) {
                return $parents[$index - 1];
            } else {
                return $node;
            }
        });

        // List of all nodes to be wrapped.
        return $this->_getNodesBetween($matchedNodes);
    }

    /**
     * @param string|NodeList|\DOMNode $input
     *
     * @return self
     */
    public function wrapAll($input) {
        $commonNode = $this->intersect();

        if (!($commonNode instanceof Element)) {
            return $this;
        }

        // Collection is a single node, or the common node is already
        //  in the collection, just call self::wrap() instead.
        if ($this->collection()->exists($commonNode)) {
            return $commonNode->wrap($input);
        }

        $inputNode = $this->inputAsFirstNode($input);

        if (!($inputNode instanceof Element)) {
            return $this;
        }

        // Get NodeList of all nodes within $commonNode to be wrapped.
        $wrapNodes = $this->_getWrapAllNodes($commonNode);

        // Pre-process wrapper into a stack of first element nodes.
        $stackNodes = $this->_prepareWrapStack($inputNode);

        // Add the new bottom (root) node after the current node
        $wrapNodes->last()->after($stackNodes->bottom());

        $wrapNodes->each(function($node) use($stackNodes) {
            // Remove the current node
            $oldNode = $node->parent()->removeChild($node);

            // Add the 'current node' back inside the new top (leaf) node.
            $stackNodes->top()->append($oldNode);
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
            $parent->children()->each(function($childNode) use($parent) {
                $oldChildNode = $parent->removeChild($childNode);

                $parent->before($oldChildNode);
            });

            $parent->remove();
        });

        return $this;
    }
}