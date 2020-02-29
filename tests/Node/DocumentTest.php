<?php

namespace DOMWrap\Tests;

class DocumentTest extends \PHPUnit\Framework\TestCase
{
    use \DOMWrap\Tests\Harness\TestTrait;

    public function testDocumentSetHtmlEncoding() {
        $expected = $this->document('<html><meta charset="UTF-8"></html>');
        $doc = $this->document('<html><meta charset="ISO-8859-1"></html>');

        $this->assertSame($expected->getHtml(), $doc->getHtml());
    }

    public function testDocumentParent() {
        $doc = $this->document('<html></html>');

        $this->assertNull($doc->parent());
    }

    public function testDocumentParents() {
        $doc = $this->document('<html></html>');

        $this->assertSame(0, $doc->parents()->count());
    }

    public function testDocumentClosest() {
        $doc = $this->document('<html></html>');

        $this->assertSame(null, $doc->closest('p'));
    }

    public function testDocumentClone() {
        $doc = $this->document('<html></html>');
        $clone = $doc->_clone();

        $this->assertNull($clone);
    }
}
