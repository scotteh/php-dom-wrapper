<?php

namespace DOMWrap\Traits;

use DOMWrap\NodeList;

/**
 * Common Trait
 *
 * @package DOMWrap\Traits
 * @license http://opensource.org/licenses/BSD-3-Clause BSD 3 Clause
 */
trait CommonTrait
{
    /**
     * @return NodeList
     */
    abstract public function collection();

    /**
     * @return \DOMDocument
     */
    abstract public function document();

    /**
     * @param NodeList $nodeList
     *
     * @return \DOMNode
     */
    abstract public function result($nodeList);

    /**
     * @return bool
     */
    public function isRemoved() {
        return !isset($this->nodeType);
    }
}