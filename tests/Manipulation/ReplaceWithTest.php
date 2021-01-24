<?php

namespace DOMWrap\Tests\Manipulation;

class ReplaceWithTest extends \PHPUnit\Framework\TestCase
{
    use \DOMWrap\Tests\Harness\TestTrait;

    public function testReplaceWithNode() {
        $expected = '<html><body><div class="inserted">hey! new content</div></body></html>';

        $doc = $this->document('<html><div class="test">some test content</div></html>');
        $nodes = $doc->find('.test');
        $nodes->first()->substituteWith('<div class="inserted">hey! new content</div>');

        $this->assertXmlStringEqualsXmlString($expected, $doc->html());
    }

    public function testReplaceWithNodeList() {
        $expected = '<html><body><div class="inserted">hey! new content</div><div class="inserted">hey! new content</div></body></html>';

        $doc = $this->document('<html><div class="test">some test content</div><div class="test">some test content</div></html>');
        $nodes = $doc->find('.test');
        $nodes->substituteWith('<div class="inserted">hey! new content</div>');

        $this->assertXmlStringEqualsXmlString($expected, $doc->html());
    }

    public function testReplaceWithNodeListNested() {
        $expected = '<html><body><article><div class="inserted">hey! new content</div></article><div class="inserted">hey! new content</div></body></html>';

        $doc = $this->document('<html><article><div class="test">some test content</div></article><a class="test">some test content</a></html>');
        $nodes = $doc->find('.test');
        $nodes->substituteWith('<div class="inserted">hey! new content</div>');

        $this->assertXmlStringEqualsXmlString($expected, $doc->html());
    }
}