<?php

namespace DOMWrap\Tests\Manipulation;

class BeforeTest extends \PHPUnit\Framework\TestCase
{
    use \DOMWrap\Tests\Harness\TestTrait;

    public function testBeforeNode() {
        $expected = '<html><body><div class="inserted"></div><div class="test"></div></body></html>';

        $doc = $this->document('<html><div class="test"></div></html>');
        $nodes = $doc->find('.test');
        $nodes->first()->precede('<div class="inserted"></div>');

        $this->assertXmlStringEqualsXmlString($expected, $doc->html());
    }

    public function testBeforeNodeList() {
        $expected = '<html><body><div class="inserted"></div><div class="test"></div><div class="inserted"></div><div class="test"></div></body></html>';

        $doc = $this->document('<html><div class="test"></div><div class="test"></div></html>');
        $nodes = $doc->find('.test');
        $nodes->precede('<div class="inserted"></div>');

        $this->assertXmlStringEqualsXmlString($expected, $doc->html());
    }

    public function testBeforeNodeListNested() {
        $expected = '<html><body><article><div class="inserted"></div><div class="test"></div></article><div class="inserted"></div><a class="test"></a></body></html>';

        $doc = $this->document('<html><article><div class="test"></div></article><a class="test"></a></html>');
        $nodes = $doc->find('.test');
        $nodes->precede('<div class="inserted"></div>');

        $this->assertXmlStringEqualsXmlString($expected, $doc->html());
    }

    public function testBeforeNodeClosure() {
        $expected = '<html><body><article><div class="inserted"></div><div class="test"></div></article><div class="inserted"></div><a class="test"></a></body></html>';

        $doc = $this->document('<html><article><div class="test"></div></article><a class="test"></a></html>');
        $nodes = $doc->find('.test');
        $nodes->precede(function($node, $index) {
            return '<div class="inserted"></div>';
        });

        $this->assertXmlStringEqualsXmlString($expected, $doc->html());
    }
}