<?php

namespace DOMWrap\Tests\Manipulation;

class WrapAllTest extends \PHPUnit\Framework\TestCase
{
    use \DOMWrap\Tests\Harness\TestTrait;

    public function testWrapAllNode() {
        $expected = '<html><body><em><strong><article><a href="http://example.org/">this is a test</a></article></strong></em></body></html>';
        $doc = $this->document('<html><article><a href="http://example.org/">this is a test</a></article></html>');
        $doc->find('article')->first()->wrapAll('<em><strong></strong></em>');

        $this->assertXmlStringEqualsXmlString($expected, $doc->html());
    }

    public function testWrapAllNodeList() {
        $expected = '<html><body><em><strong><section><a href="http://example.org/">this is a test</a></section><section><p>test!</p></section></strong></em><div><article></article></div></body></html>';
        $doc = $this->document('<html><section><a href="http://example.org/">this is a test</a></section><div><article><section><p>test!</p></section></article></div></html>');
        $doc->find('section')->wrapAll('<em><strong></strong></em>');

        $this->assertXmlStringEqualsXmlString($expected, $doc->html());
    }

    public function testWrapAllNodeListInvalid() {
        $expected = '<html><body><section><a href="http://example.org/">this is a test</a></section><div><article><section><p>test!</p></section></article></div></body></html>';
        $doc = $this->document('<html><section><a href="http://example.org/">this is a test</a></section><div><article><section><p>test!</p></section></article></div></html>');
        $doc->find('section')->wrapAll('');

        $this->assertXmlStringEqualsXmlString($expected, $doc->html());
    }

    public function testWrapAllNodeListNestedShortHand() {
        $expected = '<html><body><em><strong><section><p>test!</p></section><section><a href="http://example.org/">this is a test</a></section></strong></em></body></html>';
        $doc = $this->document('<html><section><p>test!</p><section><a href="http://example.org/">this is a test</a></section></section></html>');
        $doc->find('section')->wrapAll('<em><strong>');

        $this->assertXmlStringEqualsXmlString($expected, $doc->html());
    }

    public function testWrapAllNodeListWithSiblings() {
        $expected = '<html><body><em><strong><section><a href="http://example.org/">this is a test</a></section><section><p>test!</p></section></strong></em></body></html>';
        $doc = $this->document('<html><section><a href="http://example.org/">this is a test</a></section><section><p>test!</p></section></html>');
        $doc->find('section')->wrapAll('<em><strong></strong><i></i></em><span></span>');

        $this->assertXmlStringEqualsXmlString($expected, $doc->html());
    }

    public function testWrapAllNodeListDoesntExist() {
        $expected = '<html><body><section><a href="http://example.org/">this is a test</a></section><section><p>test!</p></section></body></html>';
        $doc = $this->document('<html><section><a href="http://example.org/">this is a test</a></section><section><p>test!</p></section></html>');
        $doc->find('article')->wrapAll('<em><strong></strong></em>');

        $this->assertXmlStringEqualsXmlString($expected, $doc->html());
    }

    public function testWrapAllNodeListFromNodeList() {
        $expected = '<html><body><em><section><a href="http://example.org/">this is a test</a></section><section><p>test!</p></section></em></body></html>';
        $doc = $this->document('<html><section><a href="http://example.org/">this is a test</a></section><section><p>test!</p></section></html>');
        $nodeList = new \DOMWrap\NodeList($doc);
        $nodeList[] = new \DOMWrap\Element('em');
        $nodeList[] = new \DOMWrap\Element('strong');
        $doc->find('section')->wrapAll($nodeList);

        $this->assertXmlStringEqualsXmlString($expected, $doc->html());
    }

}