<?php

namespace DOMWrap\Tests\Manipulation;

class EmptyTest extends \PHPUnit_Framework_TestCase
{
    use \DOMWrap\Tests\Harness\TestTrait;

    public function testEmptyNode() {
        $expected = $this->document('<html><div class="test"></div></html>');

        $doc = $this->document('<html><div class="test"><article><a href="http://example.org/">this is a test</a></article></div></html>');
        $nodes = $doc->find('.test');
        $nodes->first()->empty();

        $this->assertEqualXMLStructure($expected->children()->first(), $doc->children()->first(), true);
    }

    public function testEmptyNodeEmpty() {
        $expected = $this->document('<html><div class="test"></div></html>');

        $doc = $this->document('<html><div class="test"></div></html>');
        $nodes = $doc->find('.test');
        $nodes->first()->empty();

        $this->assertEqualXMLStructure($expected->children()->first(), $doc->children()->first(), true);
    }

    public function testEmptyNodeList() {
        $expected = $this->document('<html><div class="test"></div><section><span class="test"></span></section></html>');

        $doc = $this->document('<html><div class="test"><article><a href="http://example.org/">this is a test</a></article></div><section><span class="test"><em>testing 123..!</em></span></section></html>');
        $nodes = $doc->find('.test');
        $nodes->empty();

        $this->assertEqualXMLStructure($expected->children()->first(), $doc->children()->first(), true);
    }

    public function testEmptyNodeListNone() {
        $expected = $this->document('<html><div class="example"><article><a href="http://example.org/">this is a test</a></article></div></html>');

        $doc = $this->document('<html><div class="example"><article><a href="http://example.org/">this is a test</a></article></div></html>');
        $nodes = $doc->find('.test');
        $nodes->empty();

        $this->assertEqualXMLStructure($expected->children()->first(), $doc->children()->first(), true);
    }
}