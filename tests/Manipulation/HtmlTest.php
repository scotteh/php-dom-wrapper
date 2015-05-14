<?php

namespace DOMWrap\Tests\Manipulation;

class HtmlTest extends \PHPUnit_Framework_TestCase
{
    use \DOMWrap\Tests\Harness\TestTrait;

    public function testHtmlNode() {
        $expected = $this->document('<html><div class="test"><table><tr><th>test</th><td>test!</td></tr></table></div></html>');

        $doc = $this->document('<html><div class="test"><article><a href="http://example.org/">this is a test</a></article></div></html>');
        $nodes = $doc->find('.test');
        $nodes->first()->html('<table><tr><th>test</th><td>test!</td></tr></table>');

        $this->assertEqualXMLStructure($expected->children()->first(), $doc->children()->first(), true);
    }

    public function testHtmlNodeEmpty() {
        $expected = $this->document('<html><div class="test"><table><tr><th>test</th><td>test!</td></tr></table></div></html>');

        $doc = $this->document('<html><div class="test"></div></html>');
        $nodes = $doc->find('.test');
        $nodes->first()->html('<table><tr><th>test</th><td>test!</td></tr></table>');

        $this->assertEqualXMLStructure($expected->children()->first(), $doc->children()->first(), true);
    }

    public function testHtmlNodeList() {
        $expected = $this->document('<html><div class="test"><table><tr><th>test</th><td>test!</td></tr></table></div><section><span class="test"><table><tr><th>test</th><td>test!</td></tr></table></span></section></html>');

        $doc = $this->document('<html><div class="test"><article><a href="http://example.org/">this is a test</a></article></div><section><span class="test"><em>testing 123..!</em></span></section></html>');
        $nodes = $doc->find('.test');
        $nodes->html('<table><tr><th>test</th><td>test!</td></tr></table>');

        $this->assertEqualXMLStructure($expected->children()->first(), $doc->children()->first(), true);
    }

    public function testHtmlNodeListNone() {
        $expected = $this->document('<html><div class="example"><article><a href="http://example.org/">this is a test</a></article></div></html>');

        $doc = $this->document('<html><div class="example"><article><a href="http://example.org/">this is a test</a></article></div></html>');
        $nodes = $doc->find('.test');
        $nodes->html('<table><tr><th>test</th><td>test!</td></tr></table>');

        $this->assertEqualXMLStructure($expected->children()->first(), $doc->children()->first(), true);
    }
}