<?php

namespace DOMWrap\Tests\Manipulation;

class AfterTest extends \PHPUnit\Framework\TestCase
{
    use \DOMWrap\Tests\Harness\TestTrait;

    public function testAfterNode() {
        $expected = '<html><body><div class="test"></div><div class="inserted"></div></body></html>';

        $doc = $this->document('<html><div class="test"></div></html>');
        $nodes = $doc->find('.test');
        $nodes->first()->follow('<div class="inserted"></div>');

        $this->assertXmlStringEqualsXmlString($expected, $doc->html());
    }

    public function testAfterNodeList() {
        $expected = '<html><body><div class="test"></div><div class="inserted"></div><div class="test"></div><div class="inserted"></div></body></html>';

        $doc = $this->document('<html><div class="test"></div><div class="test"></div></html>');
        $nodes = $doc->find('.test');
        $nodes->follow('<div class="inserted"></div>');

        $this->assertXmlStringEqualsXmlString($expected, $doc->html());
    }

    public function testAfterNodeListNested() {
        $expected = '<html><body><article><div class="test"></div><div class="inserted"></div></article><a class="test"></a><div class="inserted"></div></body></html>';

        $doc = $this->document('<html><article><div class="test"></div></article><a class="test"></a></html>');
        $nodes = $doc->find('.test');
        $nodes->follow('<div class="inserted"></div>');

        $this->assertXmlStringEqualsXmlString($expected, $doc->html());
    }

    public function testAfterNodeClosure() {
        $expected = '<html><body><article><div class="test"></div><div class="inserted"></div></article><a class="test"></a><div class="inserted"></div></body></html>';

        $doc = $this->document('<html><article><div class="test"></div></article><a class="test"></a></html>');
        $nodes = $doc->find('.test');
        $nodes->follow(function($node, $index) {
            return '<div class="inserted"></div>';
        });

        $this->assertXmlStringEqualsXmlString($expected, $doc->html());
    }
}