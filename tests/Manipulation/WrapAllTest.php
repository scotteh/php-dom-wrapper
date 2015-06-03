<?php

namespace DOMWrap\Tests\Manipulation;

class WrapAllTest extends \PHPUnit_Framework_TestCase
{
    use \DOMWrap\Tests\Harness\TestTrait;

    public function testWrapAllNode() {
        $expected = $this->document('<html><em><strong><article><a href="http://example.org/">this is a test</a></article></strong></em></html>');
        $doc = $this->document('<html><article><a href="http://example.org/">this is a test</a></article></html>');
        $doc->find('article')->first()->wrapAll('<em><strong></strong></em>');

        $this->assertEqualXMLStructure($expected->children()->first(), $doc->children()->first(), true);
    }

    public function testWrapAllNodeList() {
        $expected = $this->document('<html><em><strong><section><a href="http://example.org/">this is a test</a></section><section><p>test!</p></section></strong></em><div><article></article></div></html>');
        $doc = $this->document('<html><section><a href="http://example.org/">this is a test</a></section><div><article><section><p>test!</p></section></article></div></html>');
        $doc->find('section')->wrapAll('<em><strong></strong></em>');

        $this->assertEqualXMLStructure($expected->children()->first(), $doc->children()->first(), true);
    }

    public function testWrapAllNodeListInvalid() {
        $expected = $this->document('<html><section><a href="http://example.org/">this is a test</a></section><div><article><section><p>test!</p></section></article></div></html>');
        $doc = $this->document('<html><section><a href="http://example.org/">this is a test</a></section><div><article><section><p>test!</p></section></article></div></html>');
        $doc->find('section')->wrapAll('');

        $this->assertEqualXMLStructure($expected->children()->first(), $doc->children()->first(), true);
    }

    public function testWrapAllNodeListNestedShortHand() {
        $expected = $this->document('<html><em><strong><section><p>test!</p></section><section><a href="http://example.org/">this is a test</a></section></strong></em></html>');
        $doc = $this->document('<html><section><p>test!</p><section><a href="http://example.org/">this is a test</a></section></section></html>');
        $doc->find('section')->wrapAll('<em><strong>');

        $this->assertEqualXMLStructure($expected->children()->first(), $doc->children()->first(), true);
    }

    public function testWrapAllNodeListWithSiblings() {
        $expected = $this->document('<html><em><strong><section><a href="http://example.org/">this is a test</a></section><section><p>test!</p></section></strong></em></html>');
        $doc = $this->document('<html><section><a href="http://example.org/">this is a test</a></section><section><p>test!</p></section></html>');
        $doc->find('section')->wrapAll('<em><strong></strong><i></i></em><span></span>');

        $this->assertEqualXMLStructure($expected->children()->first(), $doc->children()->first(), true);
    }

    public function testWrapAllNodeListDoesntExist() {
        $expected = $this->document('<html><section><a href="http://example.org/">this is a test</a></section><section><p>test!</p></section></html>');
        $doc = $this->document('<html><section><a href="http://example.org/">this is a test</a></section><section><p>test!</p></section></html>');
        $doc->find('article')->wrapAll('<em><strong></strong></em>');

        $this->assertEqualXMLStructure($expected->children()->first(), $doc->children()->first(), true);
    }

    public function testWrapAllNodeListFromNodeList() {
        $expected = $this->document('<html><em><section><a href="http://example.org/">this is a test</a></section><section><p>test!</p></section></em></html>');
        $doc = $this->document('<html><section><a href="http://example.org/">this is a test</a></section><section><p>test!</p></section></html>');
        $nodeList = new \DOMWrap\NodeList($doc);
        $nodeList[] = new \DOMWrap\Element('em');
        $nodeList[] = new \DOMWrap\Element('strong');
        $doc->find('section')->wrapAll($nodeList);

        $this->assertEqualXMLStructure($expected->children()->first(), $doc->children()->first(), true);
    }

}