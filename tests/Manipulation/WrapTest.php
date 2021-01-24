<?php

namespace DOMWrap\Tests\Manipulation;

class WrapTest extends \PHPUnit\Framework\TestCase
{
    use \DOMWrap\Tests\Harness\TestTrait;

    public function testWrapNode() {
        $expected = '<html><body><em><strong><article><a href="http://example.org/">this is a test</a></article></strong></em></body></html>';
        $doc = $this->document('<html><article><a href="http://example.org/">this is a test</a></article></html>');
        $doc->find('article')->first()->wrap('<em><strong></strong></em>');

        $this->assertXmlStringEqualsXmlString($expected, $doc->html());
    }

    public function testWrapNodeSingle() {
        $expected = '<html><body><em><article><a href="http://example.org/">this is a test</a></article></em></body></html>';
        $doc = $this->document('<html><article><a href="http://example.org/">this is a test</a></article></html>');
        $doc->find('article')->first()->wrap('<em></em>');

        $this->assertXmlStringEqualsXmlString($expected, $doc->html());
    }

    public function testWrapNodeTriple() {
        $expected = '<html><body><em><strong><pre><article><a href="http://example.org/">this is a test</a></article></pre></strong></em></body></html>';
        $doc = $this->document('<html><article><a href="http://example.org/">this is a test</a></article></html>');
        $doc->find('article')->first()->wrap('<em><strong><pre></pre></strong></em>');

        $this->assertXmlStringEqualsXmlString($expected, $doc->html());
    }

    public function testWrapNodeShortHand() {
        $expected = '<html><body><em><strong><article><a href="http://example.org/">this is a test</a></article></strong></em></body></html>';
        $doc = $this->document('<html><article><a href="http://example.org/">this is a test</a></article></html>');
        $doc->find('article')->first()->wrap('<em><strong>');

        $this->assertXmlStringEqualsXmlString($expected, $doc->html());
    }

    public function testWrapNodeWithSiblings() {
        $expected = '<html><body><em><strong><article><a href="http://example.org/">this is a test</a></article></strong></em></body></html>';
        $doc = $this->document('<html><article><a href="http://example.org/">this is a test</a></article></html>');
        $doc->find('article')->first()->wrap('<em><strong></strong><i></i></em><span></span>');

        $this->assertXmlStringEqualsXmlString($expected, $doc->html());
    }

    public function testWrapNodeList() {
        $expected = '<html><body><em><strong><section><a href="http://example.org/">this is a test</a></section></strong></em><em><strong><section><p>test!</p></section></strong></em></body></html>';
        $doc = $this->document('<html><section><a href="http://example.org/">this is a test</a></section><section><p>test!</p></section></html>');
        $doc->find('section')->wrap('<em><strong></strong></em>');

        $this->assertXmlStringEqualsXmlString($expected, $doc->html());
    }

    public function testWrapNodeListShortHand() {
        $expected = '<html><body><em><strong><section><a href="http://example.org/">this is a test</a></section></strong></em><em><strong><section><p>test!</p></section></strong></em></body></html>';
        $doc = $this->document('<html><section><a href="http://example.org/">this is a test</a></section><section><p>test!</p></section></html>');
        $doc->find('section')->wrap('<em><strong>');

        $this->assertXmlStringEqualsXmlString($expected, $doc->html());
    }

    public function testWrapNodeListWithSiblings() {
        $expected = '<html><body><em><strong><section><a href="http://example.org/">this is a test</a></section></strong></em><em><strong><section><p>test!</p></section></strong></em></body></html>';
        $doc = $this->document('<html><section><a href="http://example.org/">this is a test</a></section><section><p>test!</p></section></html>');
        $doc->find('section')->wrap('<em><strong></strong><i></i></em><span></span>');

        $this->assertXmlStringEqualsXmlString($expected, $doc->html());
    }

    public function testWrapNodeListDoesntExist() {
        $expected = '<html><body><section><a href="http://example.org/">this is a test</a></section><section><p>test!</p></section></body></html>';
        $doc = $this->document('<html><section><a href="http://example.org/">this is a test</a></section><section><p>test!</p></section></html>');
        $doc->find('article')->wrap('<em><strong></strong></em>');

        $this->assertXmlStringEqualsXmlString($expected, $doc->html());
    }
}