<?php

namespace DOMWrap\Traits;

use DOMWrap\NodeList;

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