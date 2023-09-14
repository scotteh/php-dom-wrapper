<?php declare(strict_types=1);

namespace DOMWrap\Collections;

/**
 * Node List
 *
 * @package DOMWrap\Collections
 * @license http://opensource.org/licenses/BSD-3-Clause BSD 3 Clause
 */
class NodeCollection implements \Countable, \ArrayAccess, \RecursiveIterator
{
    /** @var array */
    protected array $nodes = [];

    /**
     * @param iterable $nodes
     */
    public function __construct(?iterable $nodes = null) {
        if (!is_iterable($nodes)) {
            $nodes = [];
        }

        foreach ($nodes as $node) {
            $this->nodes[] = $node;
        }
    }

    /**
     * @see \Countable::count()
     *
     * @return int
     */
    public function count(): int {
        return count($this->nodes);
    }

    /**
     * @see \ArrayAccess::offsetExists()
     *
     * @param mixed $offset
     *
     * @return bool
     */
    public function offsetExists(mixed $offset): bool {
        return isset($this->nodes[$offset]);
    }

    /**
     * @see \ArrayAccess::offsetGet()
     *
     * @param mixed $offset
     *
     * @return mixed
     */
    public function offsetGet(mixed $offset): mixed {
        return isset($this->nodes[$offset]) ? $this->nodes[$offset] : null;
    }

    /**
     * @see \ArrayAccess::offsetSet()
     *
     * @param mixed $offset
     * @param mixed $value
     */
    public function offsetSet(mixed $offset, mixed $value): void {
        if (is_null($offset)) {
            $this->nodes[] = $value;
        } else {
            $this->nodes[$offset] = $value;
        }
    }

    /**
     * @see \ArrayAccess::offsetUnset()
     *
     * @param mixed $offset
     */
    public function offsetUnset(mixed $offset): void {
        unset($this->nodes[$offset]);
    }

    /**
     * @see \RecursiveIterator::RecursiveIteratorIterator()
     *
     * @return \RecursiveIteratorIterator
     */
    public function getRecursiveIterator(): \RecursiveIteratorIterator {
        return new \RecursiveIteratorIterator($this, \RecursiveIteratorIterator::SELF_FIRST);
    }

    /**
     * @see \RecursiveIterator::getChildren()
     *
     * @return \RecursiveIterator
     */
    public function getChildren(): \RecursiveIterator {
        $nodes = [];

        if ($this->valid()) {
            $nodes = $this->current()->childNodes;
        }

        return new static($nodes);
    }

    /**
     * @see \RecursiveIterator::hasChildren()
     *
     * @return bool
     */
    public function hasChildren(): bool {
        if ($this->valid()) {
            return $this->current()->hasChildNodes();
        }

        return false;
    }

    /**
     * @see \RecursiveIterator::current()
     * @see \Iterator::current()
     *
     * @return mixed
     */
    public function current(): mixed {
        return current($this->nodes);
    }

    /**
     * @see \RecursiveIterator::key()
     * @see \Iterator::key()
     *
     * @return mixed
     */
    public function key(): mixed {
        return key($this->nodes);
    }

    /**
     * @see \RecursiveIterator::next()
     * @see \Iterator::next()
     *
     * @return void
     */
    public function next(): void {
        next($this->nodes);
    }

    /**
     * @see \RecursiveIterator::rewind()
     * @see \Iterator::rewind()
     *
     * @return void
     */
    public function rewind(): void {
        reset($this->nodes);
    }

    /**
     * @see \RecursiveIterator::valid()
     * @see \Iterator::valid()
     *
     * @return bool
     */
    public function valid(): bool {
        return key($this->nodes) !== null;
    }
}