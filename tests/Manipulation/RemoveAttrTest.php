<?php

namespace DOMWrap\Tests\Manipulation;

class RemoveAttrTest extends \PHPUnit\Framework\TestCase
{
    use \DOMWrap\Tests\Harness\TestTrait;

    public function testRemoveAttrNode() {
        $expected = '<html><body><div><article><a href="http://example.org/">this is a test</a></article></div></body></html>';
        $doc = $this->document('<html><div class="test"><article><a href="http://example.org/">this is a test</a></article></div></html>');
        $doc->find('.test')->removeAttr('class');

        $this->assertXmlStringEqualsXmlString($expected, $doc->html());
    }

    public function testRemoveAttrNodeEmpty() {
        $expected = '<html><body><div></div></body></html>';
        $doc = $this->document('<html><div></div></html>');
        $doc->find('.test')->removeAttr('class');

        $this->assertXmlStringEqualsXmlString($expected, $doc->html());
    }

    public function testRemoveAttrNodeDoesntExist() {
        $expected = '<html><body><div class="test"></div></body></html>';
        $doc = $this->document('<html><div class="test"></div></html>');
        $doc->find('.test')->removeAttr('id');

        $this->assertXmlStringEqualsXmlString($expected, $doc->html());
    }

    public function testRemoveAttrNodeList() {
        $expected = '<html><body><div><article><a href="http://example.org/">this is a test</a></article></div><section><span><em>testing 123..!</em></span></section></body></html>';
        $doc = $this->document('<html><div class="test"><article><a href="http://example.org/">this is a test</a></article></div><section><span class="test"><em>testing 123..!</em></span></section></html>');
        $doc->find('.test')->removeAttr('class');

        $this->assertXmlStringEqualsXmlString($expected, $doc->html());
    }

    public function testRemoveAttrNodeListDoesntExist() {
        $expected = '<html><body><div class="test"><article><a href="http://example.org/">this is a test</a></article></div><section><span class="test"><em>testing 123..!</em></span></section></body></html>';
        $doc = $this->document('<html><div class="test"><article><a href="http://example.org/">this is a test</a></article></div><section><span class="test"><em>testing 123..!</em></span></section></html>');
        $doc->find('.test')->removeAttr('id');

        $this->assertXmlStringEqualsXmlString($expected, $doc->html());
    }
}