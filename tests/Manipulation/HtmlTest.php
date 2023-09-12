<?php

namespace DOMWrap\Tests\Manipulation;

class HtmlTest extends \PHPUnit\Framework\TestCase
{
    use \DOMWrap\Tests\Harness\TestTrait;

    public function testHtmlSetNode() {
        $expected = '<html><body><div class="test"><table><tr><th>test</th><td>test!</td></tr></table></div></body></html>';
        $doc = $this->document('<html><div class="test"><article><a href="http://example.org/">this is a test</a></article></div></html>');

        $nodes = $doc->find('.test');
        $nodes->first()->html('<table><tr><th>test</th><td>test!</td></tr></table>');

        $this->assertXmlStringEqualsXmlString($expected, $doc->html());
    }

    public function testHtmlSetNodeEmpty() {
        $expected = '<html><body><div class="test"><table><tr><th>test</th><td>test!</td></tr></table></div></body></html>';
        $doc = $this->document('<html><div class="test"></div></html>');

        $nodes = $doc->find('.test');
        $nodes->first()->html('<table><tr><th>test</th><td>test!</td></tr></table>');

        $this->assertXmlStringEqualsXmlString($expected, $doc->html());
    }

    public function testHtmlSetNodeList() {
        $expected = '<html><body><div class="test"><table><tr><th>test</th><td>test!</td></tr></table></div><section><span class="test"><table><tr><th>test</th><td>test!</td></tr></table></span></section></body></html>';
        $doc = $this->document('<html><div class="test"><article><a href="http://example.org/">this is a test</a></article></div><section><span class="test"><em>testing 123..!</em></span></section></html>');

        $nodes = $doc->find('.test');
        $nodes->html('<table><tr><th>test</th><td>test!</td></tr></table>');

        $this->assertXmlStringEqualsXmlString($expected, $doc->html());
    }

    public function testHtmlSetNodeListNone() {
        $expected = '<html><body><div class="example"><article><a href="http://example.org/">this is a test</a></article></div></body></html>';
        $doc = $this->document('<html><div class="example"><article><a href="http://example.org/">this is a test</a></article></div></html>');

        $nodes = $doc->find('.test');
        $nodes->html('<table><tr><th>test</th><td>test!</td></tr></table>');

        $this->assertXmlStringEqualsXmlString($expected, $doc->html());
    }

    public function testHtmlSetClosure() {
        $expected = '<html><body><div class="test"><table><tr><th>test</th><td>test!</td></tr></table></div><section><span class="test"><table><tr><th>test</th><td>test!</td></tr></table></span></section></body></html>';
        $doc = $this->document('<html><div class="test"><article><a href="http://example.org/">this is a test</a></article></div><section><span class="test"><em>testing 123..!</em></span></section></html>');

        $nodes = $doc->find('.test');
        $nodes->html(function($node) {
            return '<table><tr><th>test</th><td>test!</td></tr></table>';
        });

        $this->assertXmlStringEqualsXmlString($expected, $doc->html());
    }

    public function testHtmlGetNode() {
        $expected = '<article><a href="http://example.org/">this is a test</a></article>';
        $doc = $this->document('<html><div class="test"><article><a href="http://example.org/">this is a test</a></article></div></html>');

        $nodes = $doc->find('.test');
        $html = $nodes->first()->html();

        $this->assertSame($expected, $html);
    }

    public function testHtmlGetDocument() {
        $expected = '<html><body><div class="test"><article><a href="http://example.org/">this is a test</a></article></div></body></html>';
        $doc = $this->document('<html><body><div class="test"><article><a href="http://example.org/">this is a test</a></article></div></body></html>');

        $html = $doc->html();

        $this->assertSame($expected, trim($html));
    }

    public function testHtmlGetDocumentLibxml2AttributeUndoEncoding() {
        // Argument order matches the order the attributes are listed in the find() inside Document::saveHTML()
        $expected = '<html><body><a example="abc 123" src="{{example}}" href="{{example}}" action="{{example}}" name="{{example}}">this is a test</a></body></html>';
        $doc = $this->document('<html><body><a example="abc 123" src="{{example}}" href="{{example}}" action="{{example}}" name="{{example}}">this is a test</a></body></html>');

        $html = $doc->html();

        $this->assertSame($expected, trim($html));
    }
}