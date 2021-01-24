<?php

namespace DOMWrap\Tests\Manipulation;

class CloneTest extends \PHPUnit\Framework\TestCase
{
    use \DOMWrap\Tests\Harness\TestTrait;

    public function testCloneNode() {
        $doc = $this->document('<html><div class="test"><article><a href="http://example.org/">this is a test</a></article></div></html>');
        $nodes = $doc->find('.test');
        $clone = $nodes->first()->clone();

        $this->assertXmlStringEqualsXmlString($doc->find('.test')->first()->html(), $clone->html());
    }

    public function testCloneNodeEmpty() {
        $doc = $this->document('<html><div class="test"></div></html>');
        $nodes = $doc->find('.test');
        $clone = $nodes->first()->clone();

        $this->assertSame($doc->find('.test')->first()->html(), $clone->html());
    }

    public function testCloneNodeList() {
        $doc = $this->document('<html><div class="test"><article><a href="http://example.org/">this is a test</a></article></div><section><span class="test"><em>testing 123..!</em></span></section></html>');
        $nodes = $doc->find('.test');
        $clones = $nodes->clone();

        foreach ($clones as $key => $clone) {
            $this->assertXmlStringEqualsXmlString($doc->find('.test')->eq($key)->html(), $clone->html());
        }
    }
}