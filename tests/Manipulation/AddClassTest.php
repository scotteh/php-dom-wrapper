<?php

namespace DOMWrap\Tests\Manipulation;

class AddClassTest extends \PHPUnit\Framework\TestCase
{
    use \DOMWrap\Tests\Harness\TestTrait;

    public function testAddClassNode() {
        $expected = '<html><body><div class="example test dom"><article><a href="http://example.org/">this is a test</a></article></div></body></html>';
        $doc = $this->document('<html><div class="example test"><article><a href="http://example.org/">this is a test</a></article></div></html>');
        $doc->find('div')->addClass('dom');

        $this->assertXmlStringEqualsXmlString($expected, $doc->html());
    }

    public function testAddClassNodeAlreadyExist() {
        $expected = '<html><body><div class="example test dom"><article><a href="http://example.org/">this is a test</a></article></div></body></html>';
        $doc = $this->document('<html><div class="example test dom"><article><a href="http://example.org/">this is a test</a></article></div></html>');
        $doc->find('div')->addClass('dom');

        $this->assertXmlStringEqualsXmlString($expected, $doc->html());
    }

    public function testAddClassNodeDoesntExist() {
        $expected = '<html><body><div class="dom"><article><a href="http://example.org/">this is a test</a></article></div></body></html>';
        $doc = $this->document('<html><div><article><a href="http://example.org/">this is a test</a></article></div></html>');
        $doc->find('div')->addClass('dom');

        $this->assertXmlStringEqualsXmlString($expected, $doc->html());
    }

    public function testAddClassNodeClosure() {
        $expected = '<html><body><div class="example test dom"><article><a href="http://example.org/">this is a test</a></article></div></body></html>';
        $doc = $this->document('<html><div class="example test"><article><a href="http://example.org/">this is a test</a></article></div></html>');
        $doc->find('div')->addClass(function($node, $index, $attr) {
            return 'dom';
        });

        $this->assertXmlStringEqualsXmlString($expected, $doc->html());
    }

    public function testAddClassNodeList() {
        $expected = '<html><body><div class="example test dom"><article><a href="http://example.org/">this is a test</a></article></div><section class="test example dom"><span><em>testing 123..!</em></span></section></body></html>';
        $doc = $this->document('<html><div class="example test"><article><a href="http://example.org/">this is a test</a></article></div><section class="test example"><span><em>testing 123..!</em></span></section></html>');
        $doc->find('div, section')->addClass('dom');

        $this->assertXmlStringEqualsXmlString($expected, $doc->html());
    }

    public function testAddClassNodeListPartialExists() {
        $expected = '<html><body><div class="example test dom"><article><a href="http://example.org/">this is a test</a></article></div><section class="test example dom"><span><em>testing 123..!</em></span></section></body></html>';
        $doc = $this->document('<html><div class="example test"><article><a href="http://example.org/">this is a test</a></article></div><section class="test dom example"><span><em>testing 123..!</em></span></section></html>');
        $doc->find('div, section')->addClass('dom');

        $this->assertXmlStringEqualsXmlString($expected, $doc->html());
    }

    public function testAddClassNodeListDoesntExist() {
        $expected = '<html><body><div class="dom"><article><a href="http://example.org/">this is a test</a></article></div><section class="dom"><span><em>testing 123..!</em></span></section></body></html>';
        $doc = $this->document('<html><div><article><a href="http://example.org/">this is a test</a></article></div><section><span><em>testing 123..!</em></span></section></html>');
        $doc->find('div, section')->addClass('dom');

        $this->assertXmlStringEqualsXmlString($expected, $doc->html());
    }
}