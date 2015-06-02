<?php

namespace DOMWrap\Tests\Manipulation;

class PrependTest extends \PHPUnit_Framework_TestCase
{
    use \DOMWrap\Tests\Harness\TestTrait;

    public function testPrependNode() {
        $expected = $this->document('<html><div class="test"><div class="inserted"></div>some test content</div></html>');

        $doc = $this->document('<html><div class="test">some test content</div></html>');
        $nodes = $doc->find('.test');
        $nodes->first()->prepend('<div class="inserted"></div>');

        $this->assertEqualXMLStructure($expected->children()->first(), $doc->children()->first(), true);
    }

    public function testPrependNodeList() {
        $expected = $this->document('<html><div class="test"><div class="inserted"></div>some test content</div><div class="test"><div class="inserted"></div>some test content</div></html>');

        $doc = $this->document('<html><div class="test">some test content</div><div class="test">some test content</div></html>');
        $nodes = $doc->find('.test');
        $nodes->prepend('<div class="inserted"></div>');

        $this->assertEqualXMLStructure($expected->children()->first(), $doc->children()->first(), true);
    }

    public function testPrependNodeListNested() {
        $expected = $this->document('<html><article><div class="test"><div class="inserted"></div>some test content</div></article><a class="test"><div class="inserted"></div>some test content</a></html>');

        $doc = $this->document('<html><article><div class="test">some test content</div></article><a class="test">some test content</a></html>');
        $nodes = $doc->find('.test');
        $nodes->prepend('<div class="inserted"></div>');

        $this->assertEqualXMLStructure($expected->children()->first(), $doc->children()->first(), true);
    }

    public function testPrependNodeClosure() {
        $expected = $this->document('<html><article><div class="test"><div class="inserted"></div>some test content</div></article><a class="test"><div class="inserted"></div>some test content</a></html>');

        $doc = $this->document('<html><article><div class="test">some test content</div></article><a class="test">some test content</a></html>');
        $nodes = $doc->find('.test');
        $nodes->prepend(function($node, $index) {
            return '<div class="inserted"></div>';
        });

        $this->assertEqualXMLStructure($expected->children()->first(), $doc->children()->first(), true);
    }
}