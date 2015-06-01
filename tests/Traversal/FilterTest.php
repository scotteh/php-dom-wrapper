<?php

namespace DOMWrap\Tests\Manipulation;

use DOMWrap\Element;

class FilterTest extends \PHPUnit_Framework_TestCase
{
    use \DOMWrap\Tests\Harness\TestTrait;

    public function testFilter() {
        $doc = $this->document('<html><div class="example"><em>test</em><p>paragraph</p><div>block element</div><a href="http://example.org/">this is a test</a></div></html>');
        $nodes = $doc->find('div');

        $this->assertInstanceOf('\\DOMWrap\\NodeList', $nodes);
        $this->assertEquals(2, $nodes->count());

        $this->assertSame($nodes->filter('.example')->count(), 1);
        $this->assertSame($nodes->filter('.example')->attr('class'), 'example');

        $this->assertSame($nodes->filter(':not(.example)')->count(), 1);
        $this->assertSame($nodes->filter(':not(.example)')->attr('class'), null);
    }
}