<?php

namespace DOMWrap\Tests\Manipulation;

class EqTest extends \PHPUnit_Framework_TestCase
{
    use \DOMWrap\Tests\Harness\TestTrait;

    public function testEq() {
        $doc = $this->document('<html><div class="example"><em>test</em><p>paragraph</p><div>block element</div><a href="http://example.org/">this is a test</a></div></html>');
        $nodes = $doc->find('div, em, p');

        $this->assertInstanceOf('\\DOMWrap\\NodeList', $nodes);
        $this->assertEquals(4, $nodes->count());

        $this->assertInstanceOf('\DOMNode', $nodes->eq(0));
        $this->assertSame('div', $nodes->eq(0)->nodeName);
        $this->assertSame('example', $nodes->eq(0)->attr('class'));

        $this->assertInstanceOf('\DOMNode', $nodes->eq(1));
        $this->assertSame('em', $nodes->eq(1)->nodeName);

        $this->assertInstanceOf('\DOMNode', $nodes->eq(2));
        $this->assertSame('p', $nodes->eq(2)->nodeName);

        $this->assertInstanceOf('\DOMNode', $nodes->eq(3));
        $this->assertSame('div', $nodes->eq(3)->nodeName);

        $this->assertSame(null, $nodes->eq(4));

        $this->assertInstanceOf('\DOMNode', $nodes->eq(-1));
        $this->assertSame('div', $nodes->eq(-1)->nodeName);

        $this->assertInstanceOf('\DOMNode', $nodes->eq(-2));
        $this->assertSame('p', $nodes->eq(-2)->nodeName);

        $this->assertInstanceOf('\DOMNode', $nodes->eq(-3));
        $this->assertSame('em', $nodes->eq(-3)->nodeName);

        $this->assertInstanceOf('\DOMNode', $nodes->eq(-4));
        $this->assertSame('div', $nodes->eq(-4)->nodeName);
        $this->assertSame('example', $nodes->eq(-4)->attr('class'));

        $this->assertSame(null, $nodes->eq(-5));
    }
}