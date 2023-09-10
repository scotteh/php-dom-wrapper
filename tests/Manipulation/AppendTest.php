<?php

namespace DOMWrap\Tests\Manipulation;

class AppendTest extends \PHPUnit\Framework\TestCase
{
    use \DOMWrap\Tests\Harness\TestTrait;

    public function testAppendString() {
        $expected = '<html><body><div class="test">some test content<div class="inserted"></div></div></body></html>';

        $doc = $this->document('<html><div class="test">some test content</div></html>');
        $nodes = $doc->find('.test');
        $nodes->first()->appendWith('<div class="inserted"></div>');

        // @NOTE str_replace is a temporary fix for PHP <= 8.0 where whitespace is added into XML.
        $this->assertXmlStringEqualsXmlString($expected, str_replace("\n", '', $doc->html()));
    }

    public function testAppendNodeList() {
        $expected = '<html><body><div class="test">some test content<div class="inserted"></div></div><div class="test">some test content<div class="inserted"></div></div></body></html>';

        $doc = $this->document('<html><div class="test">some test content</div><div class="test">some test content</div></html>');
        $nodes = $doc->find('.test');
        $nodes->appendWith('<div class="inserted"></div>');

        // @NOTE str_replace is a temporary fix for PHP <= 8.0 where whitespace is added into XML.
        $this->assertXmlStringEqualsXmlString($expected, str_replace("\n", '', $doc->html()));
    }

    public function testAppendNodeListNested() {
        $expected = '<html><body><article><div class="test">some test content<div class="inserted"></div></div></article><a class="test">some test content<div class="inserted"></div></a></body></html>';

        $doc = $this->document('<html><article><div class="test">some test content</div></article><a class="test">some test content</a></html>');
        $nodes = $doc->find('.test');
        $nodes->appendWith('<div class="inserted"></div>');

        // @NOTE str_replace is a temporary fix for PHP <= 8.0 where whitespace is added into XML.
        $this->assertXmlStringEqualsXmlString($expected, str_replace("\n", '', $doc->html()));
    }

    public function testAppendNodeClosure() {
        $expected = '<html><body><article><div class="test">some test content<div class="inserted"></div></div></article><a class="test">some test content<div class="inserted"></div></a></body></html>';

        $doc = $this->document('<html><article><div class="test">some test content</div></article><a class="test">some test content</a></html>');
        $nodes = $doc->find('.test');
        $nodes->appendWith(function($node, $index) {
            return '<div class="inserted"></div>';
        });

        // @NOTE str_replace is a temporary fix for PHP <= 8.0 where whitespace is added into XML.
        $this->assertXmlStringEqualsXmlString($expected, str_replace("\n", '', $doc->html()));
    }

    public function testAppendNode() {
        $expected = '<html><body><ul><li><span></span></li><li><span></span></li></ul></body></html>';

        $doc = $this->document('<html><ul><li></li><li></li></ul></html>');
        $nodes = $doc->find('li');
        $nodes->appendWith($doc->createElement('span'));

        $this->assertXmlStringEqualsXmlString($expected, $doc->html());
    }
}