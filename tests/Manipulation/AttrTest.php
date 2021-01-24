<?php

namespace DOMWrap\Tests\Manipulation;

class AttrTest extends \PHPUnit\Framework\TestCase
{
    use \DOMWrap\Tests\Harness\TestTrait;

    public function testGetAttrNode() {
        $expected = 'example test';
        $doc = $this->document('<html><div class="example test"><article><a href="http://example.org/">this is a test</a></article></div></html>');
        $class = $doc->find('div')->attr('class');

        $this->assertSame($expected, $class);
    }

    public function testGetAttrNodeDoesntExist() {
        $expected = '';
        $doc = $this->document('<html><div class="example test"><article><a href="http://example.org/">this is a test</a></article></div></html>');
        $class = $doc->find('div')->attr('id');

        $this->assertSame($expected, $class);
    }

    public function testGetAttrNodeInvalid() {
        $expected = '';
        $doc = $this->document('<html><div class="example test"><article><a href="http://example.org/">this is a test</a></article></div></html>');
        $class = $doc->find('div')->unshift(new \DOMWrap\Text)->attr('class');

        $this->assertSame($expected, $class);
    }

    public function testGetAttrNodeList() {
        $expected = 'example test';
        $doc = $this->document('<html><div class="example test"><article><a href="http://example.org/">this is a test</a></article></div><section class="test example"><span><em>testing 123..!</em></span></section></html>');
        $class = $doc->find('div, section')->attr('class');

        $this->assertSame($expected, $class);
    }

    public function testGetAttrNodeListDoesntExist() {
        $expected = '';
        $doc = $this->document('<html><div class="example test"><article><a href="http://example.org/">this is a test</a></article></div><section class="test example"><span><em>testing 123..!</em></span></section></html>');
        $class = $doc->find('div, section')->attr('id');

        $this->assertSame($expected, $class);
    }

    public function testSetAttrNode() {
        $expected = '<html><body><div class="test example"><article><a href="http://example.org/">this is a test</a></article></div></body></html>';
        $doc = $this->document('<html><div><article><a href="http://example.org/">this is a test</a></article></div></html>');
        $doc->find('div')->attr('class', 'test example');

        $this->assertXmlStringEqualsXmlString($expected, $doc->html());
    }

    public function testSetAttrNodeEmpty() {
        $expected = '<html><body><div></div></body></html>';
        $doc = $this->document('<html><div></div></html>');
        $doc->find('.test')->attr('class', 'test');

        $this->assertXmlStringEqualsXmlString($expected, $doc->html());
    }

    public function testSetAttrNodeExist() {
        $expected = '<html><body><div class="example"></div></body></html>';
        $doc = $this->document('<html><div class="test"></div></html>');
        $doc->find('.test')->attr('class', 'example');

        $this->assertXmlStringEqualsXmlString($expected, $doc->html());
    }

    public function testSetAttrNodeList() {
        $expected = '<html><body><div class="example test"><article><a href="http://example.org/">this is a test</a></article></div><section class="example test"><span><em>testing 123..!</em></span></section></body></html>';
        $doc = $this->document('<html><div><article><a href="http://example.org/">this is a test</a></article></div><section><span><em>testing 123..!</em></span></section></html>');
        $doc->find('div, section')->attr('class', 'example test');

        $this->assertXmlStringEqualsXmlString($expected, $doc->html());
    }

    public function testSetAttrNodeListDoesntExist() {
        $expected = '<html><body><div class="test"><article><a href="http://example.org/">this is a test</a></article></div><section><span><em>testing 123..!</em></span></section></body></html>';
        $doc = $this->document('<html><div class="test"><article><a href="http://example.org/">this is a test</a></article></div><section><span><em>testing 123..!</em></span></section></html>');
        $doc->find('.example')->attr('class', 'example');

        $this->assertXmlStringEqualsXmlString($expected, $doc->html());
    }
}