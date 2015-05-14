<?php

namespace DOMWrap\Tests\Manipulation;

class RemoveAttrTest extends \PHPUnit_Framework_TestCase
{
    use \DOMWrap\Tests\Harness\TestTrait;

    public function testRemoveAttrNode() {
        $expected = $this->document('<html><div><article><a href="http://example.org/">this is a test</a></article></div></html>');
        $doc = $this->document('<html><div class="test"><article><a href="http://example.org/">this is a test</a></article></div></html>');
        $doc->find('.test')->removeAttr('class');

        $this->assertEqualXMLStructure($expected->children()->first(), $doc->children()->first(), true);
    }

    public function testRemoveAttrNodeEmpty() {
        $expected = $this->document('<html><div></div></html>');
        $doc = $this->document('<html><div></div></html>');
        $doc->find('.test')->removeAttr('class');

        $this->assertEqualXMLStructure($expected->children()->first(), $doc->children()->first(), true);
    }

    public function testRemoveAttrNodeDoesntExist() {
        $expected = $this->document('<html><div class="test"></div></html>');
        $doc = $this->document('<html><div class="test"></div></html>');
        $doc->find('.test')->removeAttr('id');

        $this->assertEqualXMLStructure($expected->children()->first(), $doc->children()->first(), true);
    }

    public function testRemoveAttrNodeList() {
        $expected = $this->document('<html><div><article><a href="http://example.org/">this is a test</a></article></div><section><span><em>testing 123..!</em></span></section></html>');
        $doc = $this->document('<html><div class="test"><article><a href="http://example.org/">this is a test</a></article></div><section><span class="test"><em>testing 123..!</em></span></section></html>');
        $doc->find('.test')->removeAttr('class');

        $this->assertEqualXMLStructure($expected->children()->first(), $doc->children()->first(), true);
    }

    public function testRemoveAttrNodeListDoesntExist() {
        $expected = $this->document('<html><div class="test"><article><a href="http://example.org/">this is a test</a></article></div><section><span class="test"><em>testing 123..!</em></span></section></html>');
        $doc = $this->document('<html><div class="test"><article><a href="http://example.org/">this is a test</a></article></div><section><span class="test"><em>testing 123..!</em></span></section></html>');
        $doc->find('.test')->removeAttr('id');

        $this->assertEqualXMLStructure($expected->children()->first(), $doc->children()->first(), true);
    }
}