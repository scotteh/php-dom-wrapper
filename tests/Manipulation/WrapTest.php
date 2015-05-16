<?php

namespace DOMWrap\Tests\Manipulation;

class WrapTest extends \PHPUnit_Framework_TestCase
{
    use \DOMWrap\Tests\Harness\TestTrait;

    public function testWrapNode() {
        $expected = $this->document('<html><em><strong><article><a href="http://example.org/">this is a test</a></article></strong></em></html>');
        $doc = $this->document('<html><article><a href="http://example.org/">this is a test</a></article></html>');
        $doc->find('article')->first()->wrap('<em><strong></strong></em>');

        $this->assertEqualXMLStructure($expected->children()->first(), $doc->children()->first(), true);
    }

    public function testWrapNodeSingle() {
        $expected = $this->document('<html><em><article><a href="http://example.org/">this is a test</a></article></em></html>');
        $doc = $this->document('<html><article><a href="http://example.org/">this is a test</a></article></html>');
        $doc->find('article')->first()->wrap('<em></em>');

        $this->assertEqualXMLStructure($expected->children()->first(), $doc->children()->first(), true);
    }

    public function testWrapNodeTriple() {
        $expected = $this->document('<html><em><strong><pre><article><a href="http://example.org/">this is a test</a></article></pre></strong></em></html>');
        $doc = $this->document('<html><article><a href="http://example.org/">this is a test</a></article></html>');
        $doc->find('article')->first()->wrap('<em><strong><pre></pre></strong></em>');

        $this->assertEqualXMLStructure($expected->children()->first(), $doc->children()->first(), true);
    }

    public function testWrapNodeShortHand() {
        $expected = $this->document('<html><em><strong><article><a href="http://example.org/">this is a test</a></article></strong></em></html>');
        $doc = $this->document('<html><article><a href="http://example.org/">this is a test</a></article></html>');
        $doc->find('article')->first()->wrap('<em><strong>');

        $this->assertEqualXMLStructure($expected->children()->first(), $doc->children()->first(), true);
    }

    public function testWrapNodeWithSiblings() {
        $expected = $this->document('<html><em><strong><article><a href="http://example.org/">this is a test</a></article></strong></em></html>');
        $doc = $this->document('<html><article><a href="http://example.org/">this is a test</a></article></html>');
        $doc->find('article')->first()->wrap('<em><strong></strong><i></i></em><span></span>');

        $this->assertEqualXMLStructure($expected->children()->first(), $doc->children()->first(), true);
    }

    public function testWrapNodeList() {
        $expected = $this->document('<html><em><strong><section><a href="http://example.org/">this is a test</a></section></strong></em><em><strong><section><p>test!</p></section></strong></em></html>');
        $doc = $this->document('<html><section><a href="http://example.org/">this is a test</a></section><section><p>test!</p></section></html>');
        $doc->find('section')->wrap('<em><strong></strong></em>');

        $this->assertEqualXMLStructure($expected->children()->first(), $doc->children()->first(), true);
    }

    public function testWrapNodeListShortHand() {
        $expected = $this->document('<html><em><strong><section><a href="http://example.org/">this is a test</a></section></strong></em><em><strong><section><p>test!</p></section></strong></em></html>');
        $doc = $this->document('<html><section><a href="http://example.org/">this is a test</a></section><section><p>test!</p></section></html>');
        $doc->find('section')->wrap('<em><strong>');

        $this->assertEqualXMLStructure($expected->children()->first(), $doc->children()->first(), true);
    }

    public function testWrapNodeListWithSiblings() {
        $expected = $this->document('<html><em><strong><section><a href="http://example.org/">this is a test</a></section></strong></em><em><strong><section><p>test!</p></section></strong></em></html>');
        $doc = $this->document('<html><section><a href="http://example.org/">this is a test</a></section><section><p>test!</p></section></html>');
        $doc->find('section')->wrap('<em><strong></strong><i></i></em><span></span>');

        $this->assertEqualXMLStructure($expected->children()->first(), $doc->children()->first(), true);
    }

    public function testWrapNodeListDoesntExist() {
        $expected = $this->document('<html><section><a href="http://example.org/">this is a test</a></section><section><p>test!</p></section></html>');
        $doc = $this->document('<html><section><a href="http://example.org/">this is a test</a></section><section><p>test!</p></section></html>');
        $doc->find('article')->wrap('<em><strong></strong></em>');

        $this->assertEqualXMLStructure($expected->children()->first(), $doc->children()->first(), true);
    }
}