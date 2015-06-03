<?php

namespace DOMWrap\Tests\Manipulation;

class TextTest extends \PHPUnit_Framework_TestCase
{
    use \DOMWrap\Tests\Harness\TestTrait;

    public function testTextSetNode() {
        $expected = $this->document('<html><div class="test">basic test</div></html>');
        $doc = $this->document('<html><div class="test"><article><a href="http://example.org/">this is a test</a></article></div></html>');

        $nodes = $doc->find('.test');
        $nodes->first()->text('basic test');

        $this->assertEqualXMLStructure($expected->children()->first(), $doc->children()->first(), true);
    }

    public function testTextSetNodeEmpty() {
        $expected = $this->document('<html><div class="test">basic test</div></html>');
        $doc = $this->document('<html><div class="test"></div></html>');

        $nodes = $doc->find('.test');
        $nodes->first()->text('basic test');

        $this->assertEqualXMLStructure($expected->children()->first(), $doc->children()->first(), true);
    }

    public function testTextSetNodeList() {
        $expected = $this->document('<html><div class="test">nodelist test</div><section><span class="test">nodelist test</span></section></html>');
        $doc = $this->document('<html><div class="test"><article><a href="http://example.org/">this is a test</a></article></div><section><span class="test"><em>testing 123..!</em></span></section></html>');

        $nodes = $doc->find('.test');
        $nodes->text('nodelist test');

        $this->assertEqualXMLStructure($expected->children()->first(), $doc->children()->first(), true);
    }

    public function testTextSetNodeListNone() {
        $expected = $this->document('<html><div class="example"><article><a href="http://example.org/">this is a test</a></article></div></html>');
        $doc = $this->document('<html><div class="example"><article><a href="http://example.org/">this is a test</a></article></div></html>');

        $nodes = $doc->find('.test');
        $nodes->text('none test');

        $this->assertEqualXMLStructure($expected->children()->first(), $doc->children()->first(), true);
    }

    public function testTextSetClosure() {
        $expected = $this->document('<html><div class="test">closure test</div><section><span class="test">closure test</span></section></html>');
        $doc = $this->document('<html><div class="test"><article><a href="http://example.org/">this is a test</a></article></div><section><span class="test"><em>testing 123..!</em></span></section></html>');

        $nodes = $doc->find('.test');
        $nodes->text(function($node) {
            return 'closure test';
        });

        $this->assertEqualXMLStructure($expected->children()->first(), $doc->children()->first(), true);
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