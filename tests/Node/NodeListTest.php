<?php

namespace DOMWrap\Tests;

use DOMWrap\NodeList;

class NodeListTest extends \PHPUnit\Framework\TestCase
{
    use \DOMWrap\Tests\Harness\TestTrait;

    public function testNodeListNew() {
        $doc = $this->document('<html></html>');
        $nodeList = $doc->newNodeList([]);

        $this->assertInstanceOf('\\DOMWrap\\NodeList', $nodeList);
    }

    public function testNodeListCollection() {
        $doc = $this->document('<html></html>');
        $nodeList = $doc->newNodeList();

        $this->assertSame($nodeList->collection(), $nodeList);
    }

    public function testNodeListBadMethodCallException() {
        $doc = $this->document('<html><strong>a</strong></html>');
        $nodeList = $doc->newNodeList();

        $e = null;

        try {
            $nodeList->fakeFunctionName();
        } catch (\Exception $e) { }

        $this->assertInstanceOf('\\BadMethodCallException', $e);
    }
}