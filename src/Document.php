<?php

namespace DOMWrap;

use DOMWrap\Collections\NodeList;
use DOMWrap\Traits\TraversalTrait;
use DOMWrap\Traits\ManipulationTrait;

/**
 * Document Node
 *
 * @package DOMWrap
 * @license http://opensource.org/licenses/BSD-3-Clause BSD 3 Clause
 */
class Document extends \DOMDocument
{
    use TraversalTrait;
    use ManipulationTrait;

    public function __construct($version = null, $encoding = null) {
        parent::__construct($version, $encoding);

        $this->registerNodeClass('DOMText', 'DOMWrap\\Text');
        $this->registerNodeClass('DOMElement', 'DOMWrap\\Element');
        $this->registerNodeClass('DOMComment', 'DOMWrap\\Comment');
    }

    /**
     * @see TraversalTrait::document()
     *
     * @return Document
     */
    public function document() {
        return $this;
    }

    /**
     * @return NodeList
     */
    public function collection() {
        return $this->newNodeList([$this]);
    }

    /**
     * @return NodeList
     */
    public function node() {
        return $this;
    }

    /**
     * @see TraversalTrait::parent()
     *
     * @return Element
     */
    public function parent() {
        return null;
    }

    /**
     * @see TraversalTrait::parents()
     *
     * @return NodeList
     */
    public function parents() {
        return $this->newNodeList();
    }

    /**
     * @see TraversalTrait::replaceWith()
     *
     * @param \DOMNode $newNode
     *
     * @return self
     */
    public function replaceWith($newNode) {
        $this->replaceChild($newNode, $this);

        return $this;
    }

    /**
     * @param string $html
     *
     * @return self
     */
    public function html($html, $options = 0) {
        $internalErrors = libxml_use_internal_errors(true);
        $disableEntities = libxml_disable_entity_loader(true);

        $fn = function($matches) {
            return (
                isset($matches[1])
                ? '</script> -->'
                : '<!-- <script>'
            );
        };

        $html = preg_replace_callback('@<([/])?script[^>]*>@Ui', $fn, $html);

        if (mb_detect_encoding($html, mb_detect_order(), true) === 'UTF-8') {
            $html = mb_convert_encoding($html, 'HTML-ENTITIES', 'UTF-8');
        }

        $this->loadHTML($html, $options);

        libxml_use_internal_errors($internalErrors);
        libxml_disable_entity_loader($disableEntities);

        return $this;
    }
}
