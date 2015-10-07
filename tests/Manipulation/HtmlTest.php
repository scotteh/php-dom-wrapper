<?php

namespace DOMWrap\Tests\Manipulation;

class HtmlTest extends \PHPUnit_Framework_TestCase
{
    use \DOMWrap\Tests\Harness\TestTrait;

    public function testHtmlSetNode() {
        $expected = $this->document('<html><div class="test"><table><tr><th>test</th><td>test!</td></tr></table></div></html>');
        $doc = $this->document('<html><div class="test"><article><a href="http://example.org/">this is a test</a></article></div></html>');

        $nodes = $doc->find('.test');
        $nodes->first()->html('<table><tr><th>test</th><td>test!</td></tr></table>');

        $this->assertEqualXMLStructure($expected->children()->first(), $doc->children()->first(), true);
    }

    public function testHtmlSetNodeEmpty() {
        $expected = $this->document('<html><div class="test"><table><tr><th>test</th><td>test!</td></tr></table></div></html>');
        $doc = $this->document('<html><div class="test"></div></html>');

        $nodes = $doc->find('.test');
        $nodes->first()->html('<table><tr><th>test</th><td>test!</td></tr></table>');

        $this->assertEqualXMLStructure($expected->children()->first(), $doc->children()->first(), true);
    }

    public function testHtmlSetNodeList() {
        $expected = $this->document('<html><div class="test"><table><tr><th>test</th><td>test!</td></tr></table></div><section><span class="test"><table><tr><th>test</th><td>test!</td></tr></table></span></section></html>');
        $doc = $this->document('<html><div class="test"><article><a href="http://example.org/">this is a test</a></article></div><section><span class="test"><em>testing 123..!</em></span></section></html>');

        $nodes = $doc->find('.test');
        $nodes->html('<table><tr><th>test</th><td>test!</td></tr></table>');

        $this->assertEqualXMLStructure($expected->children()->first(), $doc->children()->first(), true);
    }

    public function testHtmlSetNodeListNone() {
        $expected = $this->document('<html><div class="example"><article><a href="http://example.org/">this is a test</a></article></div></html>');
        $doc = $this->document('<html><div class="example"><article><a href="http://example.org/">this is a test</a></article></div></html>');

        $nodes = $doc->find('.test');
        $nodes->html('<table><tr><th>test</th><td>test!</td></tr></table>');

        $this->assertEqualXMLStructure($expected->children()->first(), $doc->children()->first(), true);
    }

    public function testHtmlSetClosure() {
        $expected = $this->document('<html><div class="test"><table><tr><th>test</th><td>test!</td></tr></table></div><section><span class="test"><table><tr><th>test</th><td>test!</td></tr></table></span></section></html>');
        $doc = $this->document('<html><div class="test"><article><a href="http://example.org/">this is a test</a></article></div><section><span class="test"><em>testing 123..!</em></span></section></html>');

        $nodes = $doc->find('.test');
        $nodes->html(function($node) {
            return '<table><tr><th>test</th><td>test!</td></tr></table>';
        });

        $this->assertEqualXMLStructure($expected->children()->first(), $doc->children()->first(), true);
    }

    public function testHtmlGetNode() {
        $expected = '<article><a href="http://example.org/">this is a test</a></article>';
        $doc = $this->document('<html><div class="test"><article><a href="http://example.org/">this is a test</a></article></div></html>');

        $nodes = $doc->find('.test');
        $html = $nodes->first()->html();

        $this->assertSame($expected, $html);
    }

    public function testHtmlGetDocument() {
        $expected = '<?xml encoding="utf-8"?><html><body><div class="test"><article><a href="http://example.org/">this is a test</a></article></div></body></html>';
        $doc = $this->document('<html><body><div class="test"><article><a href="http://example.org/">this is a test</a></article></div></body></html>');

        $html = $doc->html();

        $this->assertSame($expected, trim($html));
    }
}