<?php

namespace DOMWrap\Tests\Manipulation;

class WrapInnerTest extends \PHPUnit\Framework\TestCase
{
    use \DOMWrap\Tests\Harness\TestTrait;

    public function testWrapInnerNode() {
        $expected = '<html><body><article><em><strong><a href="http://example.org/">this is a test</a></strong></em></article></body></html>';
        $doc = $this->document('<html><article><a href="http://example.org/">this is a test</a></article></html>');
        $doc->find('article')->first()->wrapInner('<em><strong></strong></em>');

        $this->assertXmlStringEqualsXmlString($expected, $doc->html());
    }

    public function testWrapInnerNodeSingle() {
        $expected = '<html><body><article><em><a href="http://example.org/">this is a test</a></em></article></body></html>';
        $doc = $this->document('<html><article><a href="http://example.org/">this is a test</a></article></html>');
        $doc->find('article')->first()->wrapInner('<em></em>');

        $this->assertXmlStringEqualsXmlString($expected, $doc->html());
    }

    public function testWrapInnerNodeTriple() {
        $expected = '<html><body><article><em><strong><pre><a href="http://example.org/">this is a test</a></pre></strong></em></article></body></html>';
        $doc = $this->document('<html><article><a href="http://example.org/">this is a test</a></article></html>');
        $doc->find('article')->first()->wrapInner('<em><strong><pre></pre></strong></em>');

        $this->assertXmlStringEqualsXmlString($expected, $doc->html());
    }

    public function testWrapInnerNodeShortHand() {
        $expected = '<html><body><article><em><strong><a href="http://example.org/">this is a test</a></strong></em></article></body></html>';
        $doc = $this->document('<html><article><a href="http://example.org/">this is a test</a></article></html>');
        $doc->find('article')->first()->wrapInner('<em><strong>');

        $this->assertXmlStringEqualsXmlString($expected, $doc->html());
    }

    public function testWrapInnerNodeWithSiblings() {
        $expected = '<html><body><article><em><strong><a href="http://example.org/">this is a test</a></strong></em></article></body></html>';
        $doc = $this->document('<html><article><a href="http://example.org/">this is a test</a></article></html>');
        $doc->find('article')->first()->wrapInner('<em><strong></strong><i></i></em><span></span>');

        $this->assertXmlStringEqualsXmlString($expected, $doc->html());
    }

    public function testWrapInnerNodeList() {
        $expected = '<html><body><section><em><strong><a href="http://example.org/">this is a test</a></strong></em></section><section><em><strong><p>test!</p></strong></em></section></body></html>';
        $doc = $this->document('<html><section><a href="http://example.org/">this is a test</a></section><section><p>test!</p></section></html>');
        $doc->find('section')->wrapInner('<em><strong></strong></em>');

        $this->assertXmlStringEqualsXmlString($expected, $doc->html());
    }

    public function testWrapInnerNodeListShortHand() {
        $expected = '<html><body><section><em><strong><a href="http://example.org/">this is a test</a></strong></em></section><section><em><strong><p>test!</p></strong></em></section></body></html>';
        $doc = $this->document('<html><section><a href="http://example.org/">this is a test</a></section><section><p>test!</p></section></html>');
        $doc->find('section')->wrapInner('<em><strong>');

        $this->assertXmlStringEqualsXmlString($expected, $doc->html());
    }

    public function testWrapInnerNodeListWithSiblings() {
        $expected = '<html><body><section><em><strong><a href="http://example.org/">this is a test</a></strong></em></section><section><em><strong><p>test!</p></strong></em></section></body></html>';
        $doc = $this->document('<html><section><a href="http://example.org/">this is a test</a></section><section><p>test!</p></section></html>');
        $doc->find('section')->wrapInner('<em><strong></strong><i></i></em><span></span>');

        $this->assertXmlStringEqualsXmlString($expected, $doc->html());
    }

    public function testWrapInnerNodeListDoesntExist() {
        $expected = '<html><body><section><a href="http://example.org/">this is a test</a></section><section><p>test!</p></section></body></html>';
        $doc = $this->document('<html><section><a href="http://example.org/">this is a test</a></section><section><p>test!</p></section></html>');
        $doc->find('article')->wrapInner('<em><strong></strong></em>');

        $this->assertXmlStringEqualsXmlString($expected, $doc->html());
    }
}