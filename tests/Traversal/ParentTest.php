<?php

namespace DOMWrap\Tests\Manipulation;

use DOMWrap\Element;

class parentTest extends \PHPUnit_Framework_TestCase
{
    use \DOMWrap\Tests\Harness\TestTrait;

    public function testParent() {
        $doc = $this->document('<html><div><div class="example"><em>test</em><p>paragraph</p><div><p>block element<span><a href="http://example.org/">this is a test</a></span></p></div></div></div></html>');
        $node = $doc->find('div')->first()->parent();

        $this->assertInstanceOf('\\DOMWrap\\Element', $node);

        $this->assertSame('body', $node->nodeName);
    }

    public function testParentSelector() {
        $doc = $this->document('<html><div><div class="example"><em>test</em><p>paragraph</p><div><p>block element<span><a href="http://example.org/">this is a test</a></span></p></div></div></div></html>');
        $node = $doc->find('div')->last()->parent('div');

        $this->assertInstanceOf('\\DOMWrap\\Element', $node);

        $this->assertSame('div', $node->nodeName);
    }

    public function testParentNodeList() {
        $doc = $this->document('<html><div><div class="example"><em>test</em><p>paragraph</p><div><p>block element<span><a href="http://example.org/">this is a test</a></span></p></div></div></div></html>');
        $nodes = $doc->find('div')->parent();

        $this->assertInstanceOf('\\DOMWrap\\NodeList', $nodes);
        $this->assertEquals(3, $nodes->count());

        $this->assertSame('body', $nodes[0]->nodeName);

        $this->assertSame('div', $nodes[1]->nodeName);

        $this->assertSame('div', $nodes[2]->nodeName);
    }

    public function testParentNodeListSelector() {
        $doc = $this->document('<html><div><div class="example"><em>test</em><p>paragraph</p><div><p>block element<span><a href="http://example.org/">this is a test</a></span></p></div></div></div></html>');
        $nodes = $doc->find('div')->parent('div');

        $this->assertInstanceOf('\\DOMWrap\\NodeList', $nodes);
        $this->assertEquals(2, $nodes->count());

        $this->assertSame('div', $nodes[0]->nodeName);

        $this->assertSame('div', $nodes[1]->nodeName);
    }
}