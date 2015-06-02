<?php

namespace DOMWrap\Tests\Manipulation;

use DOMWrap\Element;

class ClosestTest extends \PHPUnit_Framework_TestCase
{
    use \DOMWrap\Tests\Harness\TestTrait;

    public function testClosest() {
        $doc = $this->document('<html><div><div class="example"><em>test</em><p>paragraph</p><div><p>block element<span><a href="http://example.org/">this is a test</a></span></p></div></div></div></html>');
        $node = $doc->find('a')->first()->closest('p');

        $this->assertInstanceOf('\\DOMWrap\\Element', $node);

        $this->assertSame('p', $node->nodeName);
    }

    public function testClosestNodeList() {
        $doc = $this->document('<html><div><div class="example"><em>test</em><p>paragraph</p><div><p>block element<span><a href="http://example.org/">this is a test</a></span></p></div></div></div></html>');
        $nodes = $doc->find('a')->closest('p');

        $this->assertInstanceOf('\\DOMWrap\\NodeList', $nodes);
        $this->assertEquals(1, $nodes->count());

        $this->assertSame('p', $nodes[0]->nodeName);
    }
}