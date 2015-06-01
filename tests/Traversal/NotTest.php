<?php

namespace DOMWrap\Tests\Manipulation;

use DOMWrap\Element;

class NotTest extends \PHPUnit_Framework_TestCase
{
    use \DOMWrap\Tests\Harness\TestTrait;

    public function testNot() {
        $doc = $this->document('<html><div class="example"><em>test</em><p>paragraph</p><div>block element</div><a href="http://example.org/">this is a test</a></div></html>');
        $nodes = $doc->find('div');

        $this->assertInstanceOf('\\DOMWrap\\NodeList', $nodes);
        $this->assertEquals(2, $nodes->count());

        $this->assertSame($nodes->not('.example')->count(), 1);
        $this->assertSame($nodes->not('.example')->attr('class'), null);

        $this->assertSame($nodes->not(':not(.example)')->count(), 1);
        $this->assertSame($nodes->not(':not(.example)')->attr('class'), 'example');
    }
}