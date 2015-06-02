<?php

namespace DOMWrap\Tests\Manipulation;

class HasClassTest extends \PHPUnit_Framework_TestCase
{
    use \DOMWrap\Tests\Harness\TestTrait;

    public function testHasClassNode() {
        $expected = true;
        $doc = $this->document('<html><div class="example DOM test"><article><a href="http://example.org/">this is a test</a></article></div></html>');
        $hasClass = $doc->find('div')->hasClass('dom');

        $this->assertSame($expected, $hasClass);
    }

    public function testHasClassNodeDoesntExist() {
        $expected = false;
        $doc = $this->document('<html><div class="example test"><article><a href="http://example.org/">this is a test</a></article></div></html>');
        $hasClass = $doc->find('div')->hasClass('dom');

        $this->assertSame($expected, $hasClass);
    }

    public function testHasClassNodeAttrDoesntExist() {
        $expected = false;
        $doc = $this->document('<html><div><article><a href="http://example.org/">this is a test</a></article></div></html>');
        $hasClass = $doc->find('div')->hasClass('dom');

        $this->assertSame($expected, $hasClass);
    }

    public function testHasClassNodeList() {
        $expected = true;
        $doc = $this->document('<html><div class="dom example test"><article><a href="http://example.org/">this is a test</a></article></div><section class="test dom example"><span><em>testing 123..!</em></span></section></html>');
        $hasClass = $doc->find('div, section')->hasClass('dom');

        $this->assertSame($expected, $hasClass);
    }

    public function testHasClassNodeListDoesntExist() {
        $expected = false;
        $doc = $this->document('<html><div class="test"><article><a href="http://example.org/">this is a test</a></article></div><section class="test"><span><em>testing 123..!</em></span></section></html>');
        $hasClass = $doc->find('div, section')->hasClass('dom');

        $this->assertSame($expected, $hasClass);
    }

    public function testHasClassNodeAttrListDoesntExist() {
        $expected = false;
        $doc = $this->document('<html><div><article><a href="http://example.org/">this is a test</a></article></div><section><span><em>testing 123..!</em></span></section></html>');
        $hasClass = $doc->find('div, section')->hasClass('dom');

        $this->assertSame($expected, $hasClass);
    }
}