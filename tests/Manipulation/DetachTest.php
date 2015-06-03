<?php

namespace DOMWrap\Tests\Manipulation;

class DetachTest extends \PHPUnit_Framework_TestCase
{
    use \DOMWrap\Tests\Harness\TestTrait;

    public function testDetachNode() {
        $expected = $this->document('<html><article></article></html>');
        $doc = $this->document('<html><article><a href="http://example.org/">this is a test</a></article></html>');
        $nodes = $doc->find('article > a')->first()->detach();

        $this->assertEqualXMLStructure($expected->children()->first(), $doc->children()->first(), true);
        $this->assertInstanceOf('\\DOMWrap\\NodeList', $nodes);
        $this->assertEquals(1, $nodes->count());

        $this->assertInstanceOf('\\DOMWrap\\Element', $nodes[0]);
        $this->assertEquals('a', $nodes[0]->tagName);
        $this->assertEquals('http://example.org/', $nodes[0]->attr('href'));
        $this->assertEquals('this is a test', $nodes[0]->text());
    }

    public function testDetachNodeSelect() {
        $expected = $this->document('<html><body></body></html>');
        $doc = $this->document('<html><body><article class="a"><a href="http://example.org/">this is a test</a></article></body></html>');
        $nodes = $doc->find('article')->first()->detach('.a');

        $this->assertEqualXMLStructure($expected->children()->first(), $doc->children()->first(), true);
        $this->assertInstanceOf('\\DOMWrap\\NodeList', $nodes);
        $this->assertEquals(1, $nodes->count());

        $this->assertInstanceOf('\\DOMWrap\\Element', $nodes[0]);
        $this->assertEquals('article', $nodes[0]->tagName);
        $this->assertEquals('a', $nodes[0]->attr('class'));
    }

    public function testDetachNodeList() {
        $expected = $this->document('<html><section></section><section></section></html>');
        $doc = $this->document('<html><section><a href="http://example.org/">this is a test</a></section><section><p>test!</p></section></html>');
        $nodes = $doc->find('a, p')->detach();

        $this->assertEqualXMLStructure($expected->children()->first(), $doc->children()->first(), true);
        $this->assertInstanceOf('\\DOMWrap\\NodeList', $nodes);
        $this->assertEquals(2, $nodes->count());

        $this->assertInstanceOf('\\DOMWrap\\Element', $nodes[0]);
        $this->assertEquals('a', $nodes[0]->tagName);
        $this->assertEquals('http://example.org/', $nodes[0]->attr('href'));
        $this->assertEquals('this is a test', $nodes[0]->text());

        $this->assertInstanceOf('\\DOMWrap\\Element', $nodes[1]);
        $this->assertEquals('p', $nodes[1]->tagName);
        $this->assertEquals('test!', $nodes[1]->text());
    }

    public function testDetachNodeListNested() {
        $expected = $this->document('<html><div></div></html>');
        $doc = $this->document('<html><section><a href="http://example.org/">this is a test</a><em>example!</em></section><div><article><section><p>test!</p></section></article></div></html>');
        $nodes = $doc->find('article, section')->detach();

        $this->assertEqualXMLStructure($expected->children()->first(), $doc->children()->first(), true);
        $this->assertInstanceOf('\\DOMWrap\\NodeList', $nodes);
        $this->assertEquals(3, $nodes->count());

        $this->assertInstanceOf('\\DOMWrap\\Element', $nodes[0]);
        $this->assertEquals('section', $nodes[0]->tagName);
        $this->assertEquals(2, $nodes[0]->children()->count());

        $this->assertInstanceOf('\\DOMWrap\\Element', $nodes[1]);
        $this->assertEquals('article', $nodes[1]->tagName);
        $this->assertEquals(0, $nodes[1]->children()->count()); // Child <section> already detached

        $this->assertInstanceOf('\\DOMWrap\\Element', $nodes[2]);
        $this->assertEquals('section', $nodes[2]->tagName);
        $this->assertEquals(1, $nodes[2]->children()->count());
    }

    public function testDetachNodeListDoesntExist() {
        $expected = $this->document('<html><section><a href="http://example.org/">this is a test</a></section><section><p>test!</p></section></html>');
        $doc = $this->document('<html><section><a href="http://example.org/">this is a test</a></section><section><p>test!</p></section></html>');
        $nodes = $doc->find('article')->detach();

        $this->assertEqualXMLStructure($expected->children()->first(), $doc->children()->first(), true);
        $this->assertInstanceOf('\\DOMWrap\\NodeList', $nodes);
        $this->assertEquals(0, $nodes->count());
    }

}