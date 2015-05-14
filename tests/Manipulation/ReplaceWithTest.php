<?php

namespace DOMWrap\Tests\Manipulation;

class ReplaceWithTest extends \PHPUnit_Framework_TestCase
{
    use \DOMWrap\Tests\Harness\TestTrait;

    public function testReplaceWithNode() {
        $expected = $this->document('<html><div class="inserted">hey! new content</div></html>');

        $doc = $this->document('<html><div class="test">some test content</div></html>');
        $nodes = $doc->find('.test');
        $nodes->first()->replaceWith('<div class="inserted">hey! new content</div>');

        $this->assertEqualXMLStructure($expected->children()->first(), $doc->children()->first(), true);
    }

    public function testReplaceWithNodeList() {
        $expected = $this->document('<html><div class="inserted">hey! new content</div><div class="inserted">hey! new content</div></html>');

        $doc = $this->document('<html><div class="test">some test content</div><div class="test">some test content</div></html>');
        $nodes = $doc->find('.test');
        $nodes->replaceWith('<div class="inserted">hey! new content</div>');

        $this->assertEqualXMLStructure($expected->children()->first(), $doc->children()->first(), true);
    }

    public function testReplaceWithNodeListNested() {
        $expected = $this->document('<html><article><div class="inserted">hey! new content</div></article><div class="inserted">hey! new content</div></html>');

        $doc = $this->document('<html><article><div class="test">some test content</div></article><a class="test">some test content</a></html>');
        $nodes = $doc->find('.test');
        $nodes->replaceWith('<div class="inserted">hey! new content</div>');

        $this->assertEqualXMLStructure($expected->children()->first(), $doc->children()->first(), true);
    }
}