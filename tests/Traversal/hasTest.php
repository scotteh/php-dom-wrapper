<?php

namespace DOMWrap\Tests\Manipulation;

use DOMWrap\Element;

class HasTest extends \PHPUnit_Framework_TestCase
{
    use \DOMWrap\Tests\Harness\TestTrait;

    public function testHas() {
        $doc = $this->document('<html><div class="example"><em>test</em><p>paragraph</p><div>block element</div><a href="http://example.org/">this is a test</a></div></html>');
        $nodes = $doc->find('div.example');

        $this->assertInstanceOf('\\DOMWrap\\NodeList', $nodes);
        $this->assertEquals($nodes->count(), 1);

        $this->assertSame($nodes->has('a')->count(), 1);
        $this->assertSame($nodes->has('a')->attr('class'), 'example');

        $this->assertSame($nodes->has('strong')->count(), 0);

        $this->assertSame($nodes->has('[href]')->count(), 1);

        $descendantNodes = $nodes->find('em')->first();

        $this->assertSame($nodes->has($descendantNodes)->count(), 1);

        $descendantNodes = $nodes->find('em, p');

        $this->assertSame($nodes->has($descendantNodes)->count(), 1);

        $descendantNodes = $nodes->find('strong');

        $this->assertSame($nodes->has($descendantNodes)->count(), 0);
    }
}