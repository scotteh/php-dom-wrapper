<?php

namespace DOMWrap\Traits;

use DOMWrap\Collections\NodeList;

/**
 * Node Trait
 *
 * @package DOMWrap\Traits
 * @license http://opensource.org/licenses/BSD-3-Clause BSD 3 Clause
 */
trait NodeTrait
{
    /** @var \DOMWrap\Document */
    protected $ownerDocument;

    /** @see TraversalTrait::newNodeList() */
    abstract public function newNodeList($nodes = []);

    /**
     * @return NodeList
     */
    public function collection() {
        return $this->newNodeList([$this]);
    }

    /**
     * @return \DOMDocument
     */
    public function document() {
        return $this->ownerDocument;
    }

    /**
     * @return self
     */
    public function node() {
        return $this;
    }
}