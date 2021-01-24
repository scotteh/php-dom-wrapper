<?php

namespace DOMWrap\Tests\Manipulation;

class EmptyTest extends \PHPUnit\Framework\TestCase
{
    use \DOMWrap\Tests\Harness\TestTrait;

    public function testEmptyNode() {
        $expected = '<html><body><div class="test"></div></body></html>';

        $doc = $this->document('<html><div class="test"><article><a href="http://example.org/">this is a test</a></article></div></html>');
        $nodes = $doc->find('.test');
        $nodes->first()->empty();

        $this->assertXmlStringEqualsXmlString($expected, $doc->html());
    }

    public function testEmptyNodeEmpty() {
        $expected = '<html><body><div class="test"></div></body></html>';

        $doc = $this->document('<html><div class="test"></div></html>');
        $nodes = $doc->find('.test');
        $nodes->first()->empty();

        $this->assertXmlStringEqualsXmlString($expected, $doc->html());
    }

    public function testEmptyNodeList() {
        $expected = '<html><body><div class="test"></div><section><span class="test"></span></section></body></html>';

        $doc = $this->document('<html><div class="test"><article><a href="http://example.org/">this is a test</a></article></div><section><span class="test"><em>testing 123..!</em></span></section></html>');
        $nodes = $doc->find('.test');
        $nodes->empty();

        $this->assertXmlStringEqualsXmlString($expected, $doc->html());
    }

    public function testEmptyNodeListNone() {
        $expected = '<html><body><div class="example"><article><a href="http://example.org/">this is a test</a></article></div></body></html>';

        $doc = $this->document('<html><div class="example"><article><a href="http://example.org/">this is a test</a></article></div></html>');
        $nodes = $doc->find('.test');
        $nodes->empty();

        $this->assertXmlStringEqualsXmlString($expected, $doc->html());
    }
}