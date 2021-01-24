<?php

namespace DOMWrap\Tests\Manipulation;

class AppendTest extends \PHPUnit\Framework\TestCase
{
    use \DOMWrap\Tests\Harness\TestTrait;

    public function testAppendString() {
// @FIXME - New line
        $expected = '<html><body><div class="test">some test content<div class="inserted"></div>'."\n".'</div></body></html>';

        $doc = $this->document('<html><div class="test">some test content</div></html>');
        $nodes = $doc->find('.test');
        $nodes->first()->appendWith('<div class="inserted"></div>');

        $this->assertXmlStringEqualsXmlString($expected, $doc->html());
    }

    public function testAppendNodeList() {
        // @FIXME - New line
        $expected = '<html><body><div class="test">some test content<div class="inserted"></div>'."\n".'</div><div class="test">some test content<div class="inserted"></div>'."\n".'</div></body></html>';

        $doc = $this->document('<html><div class="test">some test content</div><div class="test">some test content</div></html>');
        $nodes = $doc->find('.test');
        $nodes->appendWith('<div class="inserted"></div>');

        $this->assertXmlStringEqualsXmlString($expected, $doc->html());
    }

    public function testAppendNodeListNested() {
        // @FIXME - New line
        $expected = '<html><body><article><div class="test">some test content<div class="inserted"></div>'."\n".'</div></article><a class="test">some test content<div class="inserted"></div></a></body></html>';

        $doc = $this->document('<html><article><div class="test">some test content</div></article><a class="test">some test content</a></html>');
        $nodes = $doc->find('.test');
        $nodes->appendWith('<div class="inserted"></div>');

        $this->assertXmlStringEqualsXmlString($expected, $doc->html());
    }

    public function testAppendNodeClosure() {
        // @FIXME - New line
        $expected = '<html><body><article><div class="test">some test content<div class="inserted"></div>'."\n".'</div></article><a class="test">some test content<div class="inserted"></div></a></body></html>';

        $doc = $this->document('<html><article><div class="test">some test content</div></article><a class="test">some test content</a></html>');
        $nodes = $doc->find('.test');
        $nodes->appendWith(function($node, $index) {
            return '<div class="inserted"></div>';
        });

        $this->assertXmlStringEqualsXmlString($expected, $doc->html());
    }

    public function testAppendNode() {
        // @FIXME - New line
        $expected = '<html><body><ul><li><span></span></li><li><span></span></li></ul></body></html>';

        $doc = $this->document('<html><ul><li></li><li></li></ul></html>');
        $nodes = $doc->find('li');
        $nodes->appendWith($doc->createElement('span'));

        $this->assertXmlStringEqualsXmlString($expected, $doc->html());
    }
}