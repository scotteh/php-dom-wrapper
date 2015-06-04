<?php

namespace DOMWrap\Tests\Manipulation;

class AddClassTest extends \PHPUnit_Framework_TestCase
{
    use \DOMWrap\Tests\Harness\TestTrait;

    public function testAddClassNode() {
        $expected = $this->document('<html><div class="example test dom"><article><a href="http://example.org/">this is a test</a></article></div></html>');
        $doc = $this->document('<html><div class="example test"><article><a href="http://example.org/">this is a test</a></article></div></html>');
        $doc->find('div')->addClass('dom');

        $this->assertEqualXMLStructure($expected->children()->first(), $doc->children()->first(), true);
    }

    public function testAddClassNodeAlreadyExist() {
        $expected = $this->document('<html><div class="example test dom"><article><a href="http://example.org/">this is a test</a></article></div></html>');
        $doc = $this->document('<html><div class="DOM example test"><article><a href="http://example.org/">this is a test</a></article></div></html>');
        $doc->find('div')->addClass('dom');

        $this->assertEqualXMLStructure($expected->children()->first(), $doc->children()->first(), true);
    }

    public function testAddClassNodeDoesntExist() {
        $expected = $this->document('<html><div class="dom"><article><a href="http://example.org/">this is a test</a></article></div></html>');
        $doc = $this->document('<html><div><article><a href="http://example.org/">this is a test</a></article></div></html>');
        $doc->find('div')->addClass('dom');

        $this->assertEqualXMLStructure($expected->children()->first(), $doc->children()->first(), true);
    }

    public function testAddClassNodeClosure() {
        $expected = $this->document('<html><div class="example test dom"><article><a href="http://example.org/">this is a test</a></article></div></html>');
        $doc = $this->document('<html><div class="example test"><article><a href="http://example.org/">this is a test</a></article></div></html>');
        $doc->find('div')->addClass(function($node, $index, $attr) {
            return 'dom';
        });

        $this->assertEqualXMLStructure($expected->children()->first(), $doc->children()->first(), true);
    }

    public function testAddClassNodeList() {
        $expected = $this->document('<html><div class="example test dom"><article><a href="http://example.org/">this is a test</a></article></div><section class="test example dom"><span><em>testing 123..!</em></span></section></html>');
        $doc = $this->document('<html><div class="example test"><article><a href="http://example.org/">this is a test</a></article></div><section class="test example"><span><em>testing 123..!</em></span></section></html>');
        $doc->find('div, section')->addClass('dom');

        $this->assertEqualXMLStructure($expected->children()->first(), $doc->children()->first(), true);
    }

    public function testAddClassNodeListPartialExists() {
        $expected = $this->document('<html><div class="example test dom"><article><a href="http://example.org/">this is a test</a></article></div><section class="test example dom"><span><em>testing 123..!</em></span></section></html>');
        $doc = $this->document('<html><div class="example test"><article><a href="http://example.org/">this is a test</a></article></div><section class="test dom example"><span><em>testing 123..!</em></span></section></html>');
        $doc->find('div, section')->addClass('dom');

        $this->assertEqualXMLStructure($expected->children()->first(), $doc->children()->first(), true);
    }

    public function testAddClassNodeListDoesntExist() {
        $expected = $this->document('<html><div class="dom"><article><a href="http://example.org/">this is a test</a></article></div><section class="dom"><span><em>testing 123..!</em></span></section></html>');
        $doc = $this->document('<html><div><article><a href="http://example.org/">this is a test</a></article></div><section><span><em>testing 123..!</em></span></section></html>');
        $doc->find('div, section')->addClass('dom');

        $this->assertEqualXMLStructure($expected->children()->first(), $doc->children()->first(), true);
    }
}