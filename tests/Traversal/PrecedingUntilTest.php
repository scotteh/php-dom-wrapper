<?php

namespace DOMWrap\Tests\Manipulation;

class PrecedingUntilTest extends \PHPUnit_Framework_TestCase
{
    use \DOMWrap\Tests\Harness\TestTrait;

    public function testPrecedingUntil() {
        $doc = $this->document('<html><article><em>test</em><p>paragraph</p><div>block element</div><a href="http://example.org/">this is a test</a></article></html>');
        $nodes = $doc->find('a[href]')->precedingUntil();

        $this->assertInstanceOf('\\DOMWrap\\NodeList', $nodes);
        $this->assertEquals(3, $nodes->count());

        $this->assertInstanceOf('\\DOMWrap\\Element', $nodes[0]);
        $this->assertEquals('div', $nodes[0]->tagName);

        $this->assertInstanceOf('\\DOMWrap\\Element', $nodes[1]);
        $this->assertEquals('p', $nodes[1]->tagName);

        $this->assertInstanceOf('\\DOMWrap\\Element', $nodes[2]);
        $this->assertEquals('em', $nodes[2]->tagName);
    }

    public function testPrecedingUntilInputNode() {
        $doc = $this->document('<html><article><em>test</em><p>paragraph</p><div>block element</div><a href="http://example.org/">this is a test</a></article></html>');
        $nodes = $doc->find('a[href]')->precedingUntil($doc->find('p')->first());

        $this->assertInstanceOf('\\DOMWrap\\NodeList', $nodes);
        $this->assertEquals(2, $nodes->count());

        $this->assertInstanceOf('\\DOMWrap\\Element', $nodes[0]);
        $this->assertEquals('div', $nodes[0]->tagName);

        $this->assertInstanceOf('\\DOMWrap\\Element', $nodes[1]);
        $this->assertEquals('p', $nodes[1]->tagName);
    }

    public function testPrecedingUntilInputNodeList() {
        $doc = $this->document('<html><article><em>test</em><p>paragraph</p><div>block element</div><a href="http://example.org/">this is a test</a></article></html>');
        $nodes = $doc->find('a[href]')->precedingUntil($doc->find('p'));

        $this->assertInstanceOf('\\DOMWrap\\NodeList', $nodes);
        $this->assertEquals(2, $nodes->count());

        $this->assertInstanceOf('\\DOMWrap\\Element', $nodes[0]);
        $this->assertEquals('div', $nodes[0]->tagName);

        $this->assertInstanceOf('\\DOMWrap\\Element', $nodes[1]);
        $this->assertEquals('p', $nodes[1]->tagName);
    }

    public function testPrecedingUntilInputSelector() {
        $doc = $this->document('<html><article><em>test</em><p>paragraph</p><div>block element</div><a href="http://example.org/">this is a test</a></article></html>');
        $nodes = $doc->find('a[href]')->precedingUntil('p');

        $this->assertInstanceOf('\\DOMWrap\\NodeList', $nodes);
        $this->assertEquals(2, $nodes->count());

        $this->assertInstanceOf('\\DOMWrap\\Element', $nodes[0]);
        $this->assertEquals('div', $nodes[0]->tagName);

        $this->assertInstanceOf('\\DOMWrap\\Element', $nodes[1]);
        $this->assertEquals('p', $nodes[1]->tagName);
    }

    public function testPrecedingUntilSelectorNode() {
        $doc = $this->document('<html><article><em>test</em><div>1</div><p>paragraph</p><div>block element</div><strong>test</strong><a href="http://example.org/">this is a test</a></article></html>');
        $nodes = $doc->find('a[href]')->precedingUntil(null, $doc->find('div')->first());

        $this->assertInstanceOf('\\DOMWrap\\NodeList', $nodes);
        $this->assertEquals(1, $nodes->count());

        $this->assertInstanceOf('\\DOMWrap\\Element', $nodes[0]);
        $this->assertEquals('div', $nodes[0]->tagName);
    }

    public function testPrecedingUntilSelectorNodeList() {
        $doc = $this->document('<html><article><em>test</em><div>1</div><p>paragraph</p><div>block element</div><strong>test</strong><a href="http://example.org/">this is a test</a></article></html>');
        $nodes = $doc->find('a[href]')->precedingUntil(null, $doc->find('div'));

        $this->assertInstanceOf('\\DOMWrap\\NodeList', $nodes);
        $this->assertEquals(2, $nodes->count());

        $this->assertInstanceOf('\\DOMWrap\\Element', $nodes[0]);
        $this->assertEquals('div', $nodes[0]->tagName);
        $this->assertEquals('block element', $nodes[0]->text());

        $this->assertInstanceOf('\\DOMWrap\\Element', $nodes[1]);
        $this->assertEquals('div', $nodes[1]->tagName);
        $this->assertEquals('1', $nodes[1]->text());
    }

    public function testPrecedingUntilSelectorSelector() {
        $doc = $this->document('<html><article><em>test</em><div>1</div><p>paragraph</p><div>block element</div><strong>test</strong><a href="http://example.org/">this is a test</a></article></html>');
        $nodes = $doc->find('a[href]')->precedingUntil(null, 'div');

        $this->assertInstanceOf('\\DOMWrap\\NodeList', $nodes);
        $this->assertEquals(2, $nodes->count());

        $this->assertInstanceOf('\\DOMWrap\\Element', $nodes[0]);
        $this->assertEquals('div', $nodes[0]->tagName);
        $this->assertEquals('block element', $nodes[0]->text());

        $this->assertInstanceOf('\\DOMWrap\\Element', $nodes[1]);
        $this->assertEquals('div', $nodes[1]->tagName);
        $this->assertEquals('1', $nodes[1]->text());
    }

    public function testPrecedingUntilBothSelector() {
        $doc = $this->document('<html><article><em>test</em><div>1</div><p>paragraph</p><div>2</div><div>3</div><div>4</div><strong>test</strong><a href="http://example.org/">this is a test</a></article></html>');
        $nodes = $doc->find('a[href]')->precedingUntil('p', 'div');

        $this->assertInstanceOf('\\DOMWrap\\NodeList', $nodes);
        $this->assertEquals(3, $nodes->count());

        $this->assertInstanceOf('\\DOMWrap\\Element', $nodes[0]);
        $this->assertEquals('div', $nodes[0]->tagName);
        $this->assertEquals('4', $nodes[0]->text());

        $this->assertInstanceOf('\\DOMWrap\\Element', $nodes[1]);
        $this->assertEquals('div', $nodes[1]->tagName);
        $this->assertEquals('3', $nodes[1]->text());

        $this->assertInstanceOf('\\DOMWrap\\Element', $nodes[2]);
        $this->assertEquals('div', $nodes[2]->tagName);
        $this->assertEquals('2', $nodes[2]->text());
    }

    public function testPrecedingUntilBothNode1() {
        $doc = $this->document('<html><article><em>test</em><div>1</div><p>paragraph</p><div>2</div><div>3</div><div>4</div><strong>test</strong><a href="http://example.org/">this is a test</a></article></html>');
        $nodes = $doc->find('a[href]')->precedingUntil($doc->find('p')->first(), $doc->find('div')->last());

        $this->assertInstanceOf('\\DOMWrap\\NodeList', $nodes);
        $this->assertEquals(1, $nodes->count());

        $this->assertInstanceOf('\\DOMWrap\\Element', $nodes[0]);
        $this->assertEquals('div', $nodes[0]->tagName);
        $this->assertEquals('4', $nodes[0]->text());
    }

    public function testPrecedingUntilBothNode2() {
        $doc = $this->document('<html><article><em>test</em><div>1</div><p>paragraph</p><div>2</div><div>3</div><div>4</div><strong>test</strong><a href="http://example.org/">this is a test</a></article></html>');
        $nodes = $doc->find('a[href]')->precedingUntil($doc->find('p')->first(), $doc->find('div')->first());

        $this->assertInstanceOf('\\DOMWrap\\NodeList', $nodes);
        $this->assertEquals(0, $nodes->count());
    }

    public function testPrecedingUntilBothNodeList() {
        $doc = $this->document('<html><article><em>test</em><div>1</div><p>paragraph</p><div>2</div><div>3</div><div>4</div><strong>test</strong><a href="http://example.org/">this is a test</a></article></html>');
        $nodes = $doc->find('a[href]')->precedingUntil($doc->find('p'), $doc->find('div'));

        $this->assertInstanceOf('\\DOMWrap\\NodeList', $nodes);
        $this->assertEquals(3, $nodes->count());

        $this->assertInstanceOf('\\DOMWrap\\Element', $nodes[0]);
        $this->assertEquals('div', $nodes[0]->tagName);
        $this->assertEquals('4', $nodes[0]->text());

        $this->assertInstanceOf('\\DOMWrap\\Element', $nodes[1]);
        $this->assertEquals('div', $nodes[1]->tagName);
        $this->assertEquals('3', $nodes[1]->text());

        $this->assertInstanceOf('\\DOMWrap\\Element', $nodes[2]);
        $this->assertEquals('div', $nodes[2]->tagName);
        $this->assertEquals('2', $nodes[2]->text());
    }
}