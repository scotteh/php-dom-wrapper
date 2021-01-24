<?php

namespace DOMWrap\Tests\Manipulation;

class TextTest extends \PHPUnit\Framework\TestCase
{
    use \DOMWrap\Tests\Harness\TestTrait;

    public function testTextSetNode() {
        $expected = '<html><body><div class="test">basic test</div></body></html>';
        $doc = $this->document('<html><div class="test"><article><a href="http://example.org/">this is a test</a></article></div></html>');

        $nodes = $doc->find('.test');
        $nodes->first()->text('basic test');

        $this->assertXmlStringEqualsXmlString($expected, $doc->html());
    }

    public function testTextSetNodeEmpty() {
        $expected = '<html><body><div class="test">basic test</div></body></html>';
        $doc = $this->document('<html><div class="test"></div></html>');

        $nodes = $doc->find('.test');
        $nodes->first()->text('basic test');

        $this->assertXmlStringEqualsXmlString($expected, $doc->html());
    }

    public function testTextSetNodeList() {
        $expected = '<html><body><div class="test">nodelist test</div><section><span class="test">nodelist test</span></section></body></html>';
        $doc = $this->document('<html><div class="test"><article><a href="http://example.org/">this is a test</a></article></div><section><span class="test"><em>testing 123..!</em></span></section></html>');

        $nodes = $doc->find('.test');
        $nodes->text('nodelist test');

        $this->assertXmlStringEqualsXmlString($expected, $doc->html());
    }

    public function testTextSetNodeListNone() {
        $expected = '<html><body><div class="example"><article><a href="http://example.org/">this is a test</a></article></div></body></html>';
        $doc = $this->document('<html><div class="example"><article><a href="http://example.org/">this is a test</a></article></div></html>');

        $nodes = $doc->find('.test');
        $nodes->text('none test');

        $this->assertXmlStringEqualsXmlString($expected, $doc->html());
    }

    public function testTextSetClosure() {
        $expected = '<html><body><div class="test">closure test</div><section><span class="test">closure test</span></section></body></html>';
        $doc = $this->document('<html><div class="test"><article><a href="http://example.org/">this is a test</a></article></div><section><span class="test"><em>testing 123..!</em></span></section></html>');

        $nodes = $doc->find('.test');
        $nodes->text(function($node) {
            return 'closure test';
        });

        $this->assertXmlStringEqualsXmlString($expected, $doc->html());
    }

    public function testTextGetNode() {
        $expected = 'this is a test';
        $doc = $this->document('<html><div class="test"><article><a href="http://example.org/">this is a test</a></article></div></html>');

        $nodes = $doc->find('.test');
        $html = $nodes->first()->text();

        $this->assertSame($expected, $html);
    }

    public function testTextGetDocument() {
        $expected = 'article textthis is a test';
        $doc = $this->document('<html><body><div class="test"><article>article text<a href="http://example.org/">this is a test</a></article></div></body></html>');

        $html = $doc->text();

        $this->assertSame($expected, trim($html));
    }
}