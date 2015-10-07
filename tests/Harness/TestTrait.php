<?php

namespace DOMWrap\Tests\Harness;

use DOMWrap\Element;
use DOMWrap\Document;

/**
 * Test Trait
 *
 * @package DOMWrap\Tests\Harness
 * @license http://opensource.org/licenses/BSD-3-Clause BSD 3 Clause
 */
trait TestTrait {
    /**
     * @param Document|Element $doc
     *
     * @return string
     */
    public function html($doc) {
        if ($doc instanceof Document) {
            $el = $doc->documentElement;
        } else if ($doc instanceof Element) {
            $el = $doc;
            $doc = $doc->ownerDocument;
        }

        return $doc->saveXML($el);
    }

    /**
     * @param string $html
     *
     * @return Document
     */
    private function document($html) {
        $doc = new Document();
        $doc->html($html);

        // Remove any DOMProcessingInstruction/DOMDocumentType nodes from the base document
        //  so we can use \DOMDocument::$firstChild
        $length = $doc->childNodes->length;

        for ($i = 0; $i < $length; $i++) {
            $node = $doc->childNodes->item($i);
            
            if ($node instanceof \DOMProcessingInstruction || $node instanceof \DOMDocumentType) {
                $doc->removeChild($node);
            }
        }

        return $doc;
    }

    /**
     * @param string $method
     * @param mixed $arguments,...
     *
     * @return mixed
     */
    private function call($method) {
        $arguments = func_get_args();

        array_shift($arguments);

        $obj = new self::$CLASS_NAME($this->config());

        $class = new \ReflectionClass(self::$CLASS_NAME);

        if (!$class->hasMethod($method)) {
            throw new BadMethodCallException();
        }

        $fn = $class->getMethod($method);
        $fn->setAccessible(true);
        $result = $fn->invokeArgs($obj, $arguments);

        return $result;
    }
}