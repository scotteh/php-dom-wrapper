<?php

namespace DOMWrap\Tests\Manipulation;

class PrependTest extends \PHPUnit\Framework\TestCase
{
    use \DOMWrap\Tests\Harness\TestTrait;

    public function testPrependNode() {
        $expected = '<html><body><div class="test"><div class="inserted"></div>some test content</div></body></html>';

        $doc = $this->document('<html><div class="test">some test content</div></html>');
        $nodes = $doc->find('.test');
        $nodes->first()->prependWith('<div class="inserted"></div>');

        $this->assertXmlStringEqualsXmlString($expected, $doc->html());
    }

    public function testPrependNodeList() {
        $expected = '<html><body><div class="test"><div class="inserted"></div>some test content</div><div class="test"><div class="inserted"></div>some test content</div></body></html>';

        $doc = $this->document('<html><div class="test">some test content</div><div class="test">some test content</div></html>');
        $nodes = $doc->find('.test');
        $nodes->prependWith('<div class="inserted"></div>');

        $this->assertXmlStringEqualsXmlString($expected, $doc->html());
    }

    public function testPrependNodeListNested() {
        $expected = '<html><body><article><div class="test"><div class="inserted"></div>some test content</div></article><a class="test"><div class="inserted"></div>some test content</a></body></html>';

        $doc = $this->document('<html><article><div class="test">some test content</div></article><a class="test">some test content</a></html>');
        $nodes = $doc->find('.test');
        $nodes->prependWith('<div class="inserted"></div>');

        $this->assertXmlStringEqualsXmlString($expected, $doc->html());
    }

    public function testPrependNodeClosure() {
        $expected = '<html><body><article><div class="test"><div class="inserted"></div>some test content</div></article><a class="test"><div class="inserted"></div>some test content</a></body></html>';

        $doc = $this->document('<html><article><div class="test">some test content</div></article><a class="test">some test content</a></html>');
        $nodes = $doc->find('.test');
        $nodes->prependWith(function($node, $index) {
            return '<div class="inserted"></div>';
        });

        $this->assertXmlStringEqualsXmlString($expected, $doc->html());
    }
}