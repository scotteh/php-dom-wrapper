<?php

namespace DOMWrap\Tests\Manipulation;

class UnwrapTest extends \PHPUnit_Framework_TestCase
{
    use \DOMWrap\Tests\Harness\TestTrait;

    public function testUnwrapNode() {
        $expected = $this->document('<html><a href="http://example.org/">this is a test</a></html>');
        $doc = $this->document('<html><article><a href="http://example.org/">this is a test</a></article></html>');
        $doc->find('article > a')->first()->unwrap();

        $this->assertEqualXMLStructure($expected->children()->first(), $doc->children()->first(), true);
    }

    public function testUnwrapNodeList() {
        $expected = $this->document('<html><a href="http://example.org/">this is a test</a><p>test!</p></html>');
        $doc = $this->document('<html><section><a href="http://example.org/">this is a test</a></section><section><p>test!</p></section></html>');
        $doc->find('a, p')->unwrap();

        $this->assertEqualXMLStructure($expected->children()->first(), $doc->children()->first(), true);
    }

    public function testUnwrapNodeListNested() {
        $expected = $this->document('<html><section><a href="http://example.org/">this is a test</a></section><section><p>test!</p></section></html>');
        $doc = $this->document('<html><em><section><a href="http://example.org/">this is a test</a></section><div><article><section><p>test!</p></section></article></div></em></html>');
        $doc->find('article, section')->unwrap();

        $this->assertEqualXMLStructure($expected->children()->first(), $doc->children()->first(), true);
    }

    public function testUnwrapNodeListDoesntExist() {
        $expected = $this->document('<html><section><a href="http://example.org/">this is a test</a></section><section><p>test!</p></section></html>');
        $doc = $this->document('<html><section><a href="http://example.org/">this is a test</a></section><section><p>test!</p></section></html>');
        $doc->find('article')->unwrap();

        $this->assertEqualXMLStructure($expected->children()->first(), $doc->children()->first(), true);
    }

}