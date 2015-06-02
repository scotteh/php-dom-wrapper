<?php

namespace DOMWrap\Tests\Manipulation;

class AfterTest extends \PHPUnit_Framework_TestCase
{
    use \DOMWrap\Tests\Harness\TestTrait;

    public function testAfterNode() {
        $expected = $this->document('<html><div class="test"></div><div class="inserted"></div></html>');

        $doc = $this->document('<html><div class="test"></div></html>');
        $nodes = $doc->find('.test');
        $nodes->first()->after('<div class="inserted"></div>');

        $this->assertEqualXMLStructure($expected->children()->first(), $doc->children()->first(), true);
    }

    public function testAfterNodeList() {
        $expected = $this->document('<html><div class="test"></div><div class="inserted"></div><div class="test"></div><div class="inserted"></div></html>');

        $doc = $this->document('<html><div class="test"></div><div class="test"></div></html>');
        $nodes = $doc->find('.test');
        $nodes->after('<div class="inserted"></div>');

        $this->assertEqualXMLStructure($expected->children()->first(), $doc->children()->first(), true);
    }

    public function testAfterNodeListNested() {
        $expected = $this->document('<html><article><div class="test"></div><div class="inserted"></div></article><a class="test"></a><div class="inserted"></div></html>');

        $doc = $this->document('<html><article><div class="test"></div></article><a class="test"></a></html>');
        $nodes = $doc->find('.test');
        $nodes->after('<div class="inserted"></div>');

        $this->assertEqualXMLStructure($expected->children()->first(), $doc->children()->first(), true);
    }

    public function testAfterNodeClosure() {
        $expected = $this->document('<html><article><div class="test"></div><div class="inserted"></div></article><a class="test"></a><div class="inserted"></div></html>');

        $doc = $this->document('<html><article><div class="test"></div></article><a class="test"></a></html>');
        $nodes = $doc->find('.test');
        $nodes->after(function($node, $index) {
            return '<div class="inserted"></div>';
        });

        $this->assertEqualXMLStructure($expected->children()->first(), $doc->children()->first(), true);
    }
}