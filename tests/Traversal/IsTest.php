<?php

namespace DOMWrap\Tests\Manipulation;

use DOMWrap\Element;

class IsTest extends \PHPUnit_Framework_TestCase
{
    use \DOMWrap\Tests\Harness\TestTrait;

    public function testIs() {
        $doc = $this->document('<html><div class="example"><em>test</em><p>paragraph</p><div>block element</div><a href="http://example.org/">this is a test</a></div></html>');
        $nodes = $doc->find('div');

        $this->assertInstanceOf('\\DOMWrap\\NodeList', $nodes);
        $this->assertEquals(2, $nodes->count());

        $this->assertSame($nodes->is('.example'), true);
        $this->assertSame($nodes->is('.test'), false);
        $this->assertSame($nodes->is($nodes[0]), true);
        $this->assertSame($nodes->is($nodes[1]), true);
        $this->assertSame($nodes->is(new Element('div')), false);
        $this->assertSame($nodes->is(function($node) {
            return false;
        }), false);
        $this->assertSame($nodes->is(function($node) {
            return true;
        }), true);
        $this->assertSame($nodes->is(function($node) use ($nodes) {
            return $nodes->exists($node);
        }), true);
        $this->assertSame($nodes->is(function($node) use ($nodes) {
            return !$nodes->exists($node);
        }), false);
    }
}