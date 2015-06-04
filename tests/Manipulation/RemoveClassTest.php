<?php

namespace DOMWrap\Tests\Manipulation;

class RemoveClassTest extends \PHPUnit_Framework_TestCase
{
    use \DOMWrap\Tests\Harness\TestTrait;

    public function testRemoveClassNode() {
        $expected = $this->document('<html><div class="example test"><article><a href="http://example.org/">this is a test</a></article></div></html>');
        $doc = $this->document('<html><div class="example DOM test"><article><a href="http://example.org/">this is a test</a></article></div></html>');
        $doc->find('div')->removeClass('dom');

        $this->assertEqualXMLStructure($expected->children()->first(), $doc->children()->first(), true);
    }

    public function testRemoveClassNodeDoesntExist() {
        $expected = $this->document('<html><div class="example test"><article><a href="http://example.org/">this is a test</a></article></div></html>');
        $doc = $this->document('<html><div class="example test"><article><a href="http://example.org/">this is a test</a></article></div></html>');
        $doc->find('div')->removeClass('dom');

        $this->assertEqualXMLStructure($expected->children()->first(), $doc->children()->first(), true);
    }

    public function testRemoveClassNodeAttrDoesntExist() {
        $expected = $this->document('<html><div><article><a href="http://example.org/">this is a test</a></article></div></html>');
        $doc = $this->document('<html><div><article><a href="http://example.org/">this is a test</a></article></div></html>');
        $doc->find('div')->removeClass('dom');

        $this->assertEqualXMLStructure($expected->children()->first(), $doc->children()->first(), true);
    }

    public function testRemoveClassNodeClosure() {
        $expected = $this->document('<html><div class="example test"><article><a href="http://example.org/">this is a test</a></article></div></html>');
        $doc = $this->document('<html><div class="example DOM test"><article><a href="http://example.org/">this is a test</a></article></div></html>');
        $doc->find('div')->removeClass(function($node, $index, $attr) {
            return 'dom';
        });

        $this->assertEqualXMLStructure($expected->children()->first(), $doc->children()->first(), true);
    }

    public function testRemoveClassNodeList() {
        $expected = $this->document('<html><div class="example test"><article><a href="http://example.org/">this is a test</a></article></div><section class="test example"><span><em>testing 123..!</em></span></section></html>');
        $doc = $this->document('<html><div class="dom example test"><article><a href="http://example.org/">this is a test</a></article></div><section class="test dom example"><span><em>testing 123..!</em></span></section></html>');
        $doc->find('div, section')->removeClass('dom');

        $this->assertEqualXMLStructure($expected->children()->first(), $doc->children()->first(), true);
    }

    public function testRemoveClassNodeListPartialExists() {
        $expected = $this->document('<html><div class="example test"><article><a href="http://example.org/">this is a test</a></article></div><section class="test example"><span><em>testing 123..!</em></span></section></html>');
        $doc = $this->document('<html><div class="example test dom"><article><a href="http://example.org/">this is a test</a></article></div><section class="test example"><span><em>testing 123..!</em></span></section></html>');
        $doc->find('div, section')->removeClass('dom');

        $this->assertEqualXMLStructure($expected->children()->first(), $doc->children()->first(), true);
    }

    public function testRemoveClassNodeListDoesntExist() {
        $expected = $this->document('<html><div class="test"><article><a href="http://example.org/">this is a test</a></article></div><section class="test"><span><em>testing 123..!</em></span></section></html>');
        $doc = $this->document('<html><div class="test"><article><a href="http://example.org/">this is a test</a></article></div><section class="test"><span><em>testing 123..!</em></span></section></html>');
        $doc->find('div, section')->removeClass('dom');

        $this->assertEqualXMLStructure($expected->children()->first(), $doc->children()->first(), true);
    }

    public function testRemoveClassNodeAttrListDoesntExist() {
        $expected = $this->document('<html><div><article><a href="http://example.org/">this is a test</a></article></div><section><span><em>testing 123..!</em></span></section></html>');
        $doc = $this->document('<html><div><article><a href="http://example.org/">this is a test</a></article></div><section><span><em>testing 123..!</em></span></section></html>');
        $doc->find('div, section')->removeClass('dom');

        $this->assertEqualXMLStructure($expected->children()->first(), $doc->children()->first(), true);
    }
}