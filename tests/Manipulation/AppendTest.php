<?php

namespace DOMWrap\Tests\Manipulation;

class AppendTest extends \PHPUnit_Framework_TestCase
{
    use \DOMWrap\Tests\Harness\TestTrait;

    public function testAppendNode() {
        $expected = $this->document('<html><div class="test">some test content<div class="inserted"></div></div></html>');

        $doc = $this->document('<html><div class="test">some test content</div></html>');
        $nodes = $doc->find('.test');
        $nodes->first()->append('<div class="inserted"></div>');

        $this->assertEqualXMLStructure($expected->children()->first(), $doc->children()->first(), true);
    }

    public function testAppendNodeList() {
        $expected = $this->document('<html><div class="test">some test content<div class="inserted"></div></div><div class="test">some test content<div class="inserted"></div></div></html>');

        $doc = $this->document('<html><div class="test">some test content</div><div class="test">some test content</div></html>');
        $nodes = $doc->find('.test');
        $nodes->append('<div class="inserted"></div>');

        $this->assertEqualXMLStructure($expected->children()->first(), $doc->children()->first(), true);
    }

    public function testAppendNodeListNested() {
        $expected = $this->document('<html><article><div class="test">some test content<div class="inserted"></div></div></article><a class="test">some test content<div class="inserted"></div></a></html>');

        $doc = $this->document('<html><article><div class="test">some test content</div></article><a class="test">some test content</a></html>');
        $nodes = $doc->find('.test');
        $nodes->append('<div class="inserted"></div>');

        $this->assertEqualXMLStructure($expected->children()->first(), $doc->children()->first(), true);
    }

    public function testAppendNodeClosure() {
        $expected = $this->document('<html><article><div class="test">some test content<div class="inserted"></div></div></article><a class="test">some test content<div class="inserted"></div></a></html>');

        $doc = $this->document('<html><article><div class="test">some test content</div></article><a class="test">some test content</a></html>');
        $nodes = $doc->find('.test');
        $nodes->append(function($node, $index) {
            return '<div class="inserted"></div>';
        });

        $this->assertEqualXMLStructure($expected->children()->first(), $doc->children()->first(), true);
    }
}