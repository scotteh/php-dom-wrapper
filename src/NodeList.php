<?php

namespace DOMWrap;

use DOMWrap\Document;
use DOMWrap\Traits\CommonTrait;
use DOMWrap\Traits\TraversalTrait;
use DOMWrap\Traits\ManipulationTrait;
use DOMWrap\Collections\NodeCollection;

/**
 * Node List
 *
 * @package DOMWrap
 * @license http://opensource.org/licenses/BSD-3-Clause BSD 3 Clause
 */
class NodeList extends NodeCollection
{
    use CommonTrait;
    use TraversalTrait;
    use ManipulationTrait {
        ManipulationTrait::__call as __manipulationCall;
    }

    /** @var Document */
    protected $document;

    /**
     * @param Document $document
     * @param Traversable|array $nodes
     */
    public function __construct(Document $document = null, $nodes = null) {
        parent::__construct($nodes);

        $this->document = $document;
    }

    /**
     * @param string $name
     * @param mixed $arguments
     *
     * @return mixed
     */
    public function __call($name, $arguments) {
        try {
            $result = $this->__manipulationCall($name, $arguments);
        } catch (\BadMethodCallException $e) {
            if (!method_exists($this->first(), $name)) {
                throw new \BadMethodCallException("Call to undefined method " . get_class($this) . '::' . $name . "()");
            }

            $result = call_user_func_array([$this->first(), $name], $arguments);
        }

        return $result;
    }

    /**
     * {@inheritdoc}
     */
    public function collection() {
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function document() {
        return $this->document;
    }

    /**
     * {@inheritdoc}
     */
    public function result($nodeList) {
        return $nodeList;
    }

    /**
     * @return NodeList
     */
    public function reverse() {
        array_reverse($this->nodes);

        return $this;
    }

    /**
     * @return mixed
     */
    public function first() {
        return !empty($this->nodes) ? $this->rewind() : null;
    }

    /**
     * @return mixed
     */
    public function last() {
        return $this->end();
    }

    /**
     * @return mixed
     */
    public function end() {
        return !empty($this->nodes) ? end($this->nodes) : null;
    }

    /**
     * @param int $key
     *
     * @return mixed
     */
    public function get($key) {
        if (isset($this->nodes[$key])) {
            return $this->nodes[$key];
        }

        return null;
    }

    /**
     * @param mixed $key
     * @param mixed $value
     */
    public function set($key, $value) {
        $this->nodes[$key] = $value;
    }

    /**
     * @param \Closure $function
     *
     * @return self
     */
    public function each(\Closure $function) {
        foreach ($this->nodes as $index => $node) {
            $result = $function($node, $index);

            if ($result === false) {
                break;
            }
        }

        return $this;
    }

    /**
     * @param \Closure $function
     *
     * @return NodeList
     */
    public function map(\Closure $function) {
        $nodes = $this->newNodeList();

        foreach ($this->nodes as $node) {
            $result = $function($node);

            if (!is_null($result) && $result !== false) {
                $nodes[] = $result;
            }
        }

        return $nodes;
    }

    /**
     * @param \Closure $function
     *
     * @return mixed[]
     */
    public function reduce(\Closure $function, $initial = null) {
        return array_reduce($this->nodes, $function, $initial);
    }

    /**
     * @return mixed
     */
    public function toArray() {
        return $this->nodes;
    }

    /**
     * @param \Traversable|array $nodes
     */
    public function fromArray($nodes = null) {
        if (!is_array($nodes) && !($nodes instanceof \Traversable)) {
            $nodes = [];
        }

        $this->nodes = $nodes;
    }

    /**
     * @param NodeList|array $elements
     *
     * @return NodeList
     */
    public function merge($elements = []) {
        if (!is_array($elements)) {
            $elements = $elements->toArray();
        }

        return $this->newNodeList(array_merge($this->toArray(), $elements));
    }

    /**
     * @param int $start
     * @param int $end
     *
     * @return NodeList
     */
    public function slice($start, $end = null) {
        $nodeList = array_slice($this->toArray(), $start, $end);

        return $this->newNodeList($nodeList);
    }

    /**
     * @param \DOMNode $node
     *
     * @return self
     */
    public function push(\DOMNode $node) {
        $this->nodes[] = $node;

        return $this;
    }

    /**
     * @return \DOMNode
     */
    public function pop() {
        return array_pop($this->nodes);
    }

    /**
     * @param \DOMNode $node
     *
     * @return self
     */
    public function unshift(\DOMNode $node) {
        array_unshift($this->nodes, $node);

        return $this;
    }

    /**
     * @return \DOMNode
     */
    public function shift() {
        return array_shift($this->nodes);
    }

    /**
     * @param \DOMNode $node
     *
     * @return bool
     */
    public function exists(\DOMNode $node) {
        return in_array($node, $this->nodes, true);
    }

    /**
     * @param \DOMNode $node
     *
     * @return self
     */
    public function delete(\DOMNode $node) {
        $index = array_search($node, $this->nodes, true);

        if ($index !== false) {
            unset($this->nodes[$index]);
        }

        return $this;
    }

    /**
     * @return bool
     */
    public function isRemoved() {
        return false;
    }
}