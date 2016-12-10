<?php

namespace DOMWrap\Traits;

use DOMWrap\NodeList;

/**
 * Node Trait
 *
 * @package DOMWrap\Traits
 * @license http://opensource.org/licenses/BSD-3-Clause BSD 3 Clause
 * @property \DOMDocument $ownerDocument
 */
trait NodeTrait
{
    /** @see TraversalTrait::newNodeList() */
    abstract public function newNodeList($nodes = []);

    /** @see CommonTrait::isRemoved() */
    abstract public function isRemoved();

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
        if ($this->isRemoved()) {
            return null;
        }

        return $this->ownerDocument;
    }

    /**
     * @param NodeList $nodeList
     *
     * @return \DOMNode
     */
    public function result($nodeList) {
        if ($nodeList->count()) {
            return $nodeList->first();
        }

        return null;
    }
}