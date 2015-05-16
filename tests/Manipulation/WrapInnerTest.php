<?php

namespace DOMWrap\Tests\Manipulation;

class WrapInnerTest extends \PHPUnit_Framework_TestCase
{
    use \DOMWrap\Tests\Harness\TestTrait;

    public function testWrapInnerNode() {
        $expected = $this->document('<html><article><em><strong><a href="http://example.org/">this is a test</a></strong></em></article></html>');
        $doc = $this->document('<html><article><a href="http://example.org/">this is a test</a></article></html>');
        $doc->find('article')->first()->wrapInner('<em><strong></strong></em>');

        $this->assertEqualXMLStructure($expected->children()->first(), $doc->children()->first(), true);
    }

    public function testWrapInnerNodeSingle() {
        $expected = $this->document('<html><article><em><a href="http://example.org/">this is a test</a></em></article></html>');
        $doc = $this->document('<html><article><a href="http://example.org/">this is a test</a></article></html>');
        $doc->find('article')->first()->wrapInner('<em></em>');

        $this->assertEqualXMLStructure($expected->children()->first(), $doc->children()->first(), true);
    }

    public function testWrapInnerNodeTriple() {
        $expected = $this->document('<html><article><em><strong><pre><a href="http://example.org/">this is a test</a></pre></strong></em></article></html>');
        $doc = $this->document('<html><article><a href="http://example.org/">this is a test</a></article></html>');
        $doc->find('article')->first()->wrapInner('<em><strong><pre></pre></strong></em>');

        $this->assertEqualXMLStructure($expected->children()->first(), $doc->children()->first(), true);
    }

    public function testWrapInnerNodeShortHand() {
        $expected = $this->document('<html><article><em><strong><a href="http://example.org/">this is a test</a></strong></em></article></html>');
        $doc = $this->document('<html><article><a href="http://example.org/">this is a test</a></article></html>');
        $doc->find('article')->first()->wrapInner('<em><strong>');

        $this->assertEqualXMLStructure($expected->children()->first(), $doc->children()->first(), true);
    }

    public function testWrapInnerNodeWithSiblings() {
        $expected = $this->document('<html><article><em><strong><a href="http://example.org/">this is a test</a></strong></em></article></html>');
        $doc = $this->document('<html><article><a href="http://example.org/">this is a test</a></article></html>');
        $doc->find('article')->first()->wrapInner('<em><strong></strong><i></i></em><span></span>');

        $this->assertEqualXMLStructure($expected->children()->first(), $doc->children()->first(), true);
    }

    public function testWrapInnerNodeList() {
        $expected = $this->document('<html><section><em><strong><a href="http://example.org/">this is a test</a></strong></em></section><section><em><strong><p>test!</p></strong></em></section></html>');
        $doc = $this->document('<html><section><a href="http://example.org/">this is a test</a></section><section><p>test!</p></section></html>');
        $doc->find('section')->wrapInner('<em><strong></strong></em>');

        $this->assertEqualXMLStructure($expected->children()->first(), $doc->children()->first(), true);
    }

    public function testWrapInnerNodeListShortHand() {
        $expected = $this->document('<html><section><em><strong><a href="http://example.org/">this is a test</a></strong></em></section><section><em><strong><p>test!</p></strong></em></section></html>');
        $doc = $this->document('<html><section><a href="http://example.org/">this is a test</a></section><section><p>test!</p></section></html>');
        $doc->find('section')->wrapInner('<em><strong>');

        $this->assertEqualXMLStructure($expected->children()->first(), $doc->children()->first(), true);
    }

    public function testWrapInnerNodeListWithSiblings() {
        $expected = $this->document('<html><section><em><strong><a href="http://example.org/">this is a test</a></strong></em></section><section><em><strong><p>test!</p></strong></em></section></html>');
        $doc = $this->document('<html><section><a href="http://example.org/">this is a test</a></section><section><p>test!</p></section></html>');
        $doc->find('section')->wrapInner('<em><strong></strong><i></i></em><span></span>');

        $this->assertEqualXMLStructure($expected->children()->first(), $doc->children()->first(), true);
    }

    public function testWrapInnerNodeListDoesntExist() {
        $expected = $this->document('<html><section><a href="http://example.org/">this is a test</a></section><section><p>test!</p></section></html>');
        $doc = $this->document('<html><section><a href="http://example.org/">this is a test</a></section><section><p>test!</p></section></html>');
        $doc->find('article')->wrapInner('<em><strong></strong></em>');

        $this->assertEqualXMLStructure($expected->children()->first(), $doc->children()->first(), true);
    }
}