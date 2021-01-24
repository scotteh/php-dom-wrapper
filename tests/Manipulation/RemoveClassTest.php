<?php

namespace DOMWrap\Tests\Manipulation;

class RemoveClassTest extends \PHPUnit\Framework\TestCase
{
    use \DOMWrap\Tests\Harness\TestTrait;

    public function testRemoveClassNode() {
        $expected = '<html><body><div class="example test"><article><a href="http://example.org/">this is a test</a></article></div></body></html>';
        $doc = $this->document('<html><div class="example DOM test"><article><a href="http://example.org/">this is a test</a></article></div></html>');
        $doc->find('div')->removeClass('dom');

        $this->assertXmlStringEqualsXmlString($expected, $doc->html());
    }

    public function testRemoveClassNodeDoesntExist() {
        $expected = '<html><body><div class="example test"><article><a href="http://example.org/">this is a test</a></article></div></body></html>';
        $doc = $this->document('<html><div class="example test"><article><a href="http://example.org/">this is a test</a></article></div></html>');
        $doc->find('div')->removeClass('dom');

        $this->assertXmlStringEqualsXmlString($expected, $doc->html());
    }

    public function testRemoveClassNodeAttrDoesntExist() {
        $expected = '<html><body><div><article><a href="http://example.org/">this is a test</a></article></div></body></html>';
        $doc = $this->document('<html><div><article><a href="http://example.org/">this is a test</a></article></div></html>');
        $doc->find('div')->removeClass('dom');

        $this->assertXmlStringEqualsXmlString($expected, $doc->html());
    }

    public function testRemoveClassNodeClosure() {
        $expected = '<html><body><div class="example test"><article><a href="http://example.org/">this is a test</a></article></div></body></html>';
        $doc = $this->document('<html><div class="example DOM test"><article><a href="http://example.org/">this is a test</a></article></div></html>');
        $doc->find('div')->removeClass(function($node, $index, $attr) {
            return 'dom';
        });

        $this->assertXmlStringEqualsXmlString($expected, $doc->html());
    }

    public function testRemoveClassNodeList() {
        $expected = '<html><body><div class="example test"><article><a href="http://example.org/">this is a test</a></article></div><section class="test example"><span><em>testing 123..!</em></span></section></body></html>';
        $doc = $this->document('<html><div class="dom example test"><article><a href="http://example.org/">this is a test</a></article></div><section class="test dom example"><span><em>testing 123..!</em></span></section></html>');
        $doc->find('div, section')->removeClass('dom');

        $this->assertXmlStringEqualsXmlString($expected, $doc->html());
    }

    public function testRemoveClassNodeListPartialExists() {
        $expected = '<html><body><div class="example test"><article><a href="http://example.org/">this is a test</a></article></div><section class="test example"><span><em>testing 123..!</em></span></section></body></html>';
        $doc = $this->document('<html><div class="example test dom"><article><a href="http://example.org/">this is a test</a></article></div><section class="test example"><span><em>testing 123..!</em></span></section></html>');
        $doc->find('div, section')->removeClass('dom');

        $this->assertXmlStringEqualsXmlString($expected, $doc->html());
    }

    public function testRemoveClassNodeListDoesntExist() {
        $expected = '<html><body><div class="test"><article><a href="http://example.org/">this is a test</a></article></div><section class="test"><span><em>testing 123..!</em></span></section></body></html>';
        $doc = $this->document('<html><div class="test"><article><a href="http://example.org/">this is a test</a></article></div><section class="test"><span><em>testing 123..!</em></span></section></html>');
        $doc->find('div, section')->removeClass('dom');

        $this->assertXmlStringEqualsXmlString($expected, $doc->html());
    }

    public function testRemoveClassNodeAttrListDoesntExist() {
        $expected = '<html><body><div><article><a href="http://example.org/">this is a test</a></article></div><section><span><em>testing 123..!</em></span></section></body></html>';
        $doc = $this->document('<html><div><article><a href="http://example.org/">this is a test</a></article></div><section><span><em>testing 123..!</em></span></section></html>');
        $doc->find('div, section')->removeClass('dom');

        $this->assertXmlStringEqualsXmlString($expected, $doc->html());
    }
}