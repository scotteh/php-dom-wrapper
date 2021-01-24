<?php

namespace DOMWrap\Tests\Manipulation;

class UnwrapTest extends \PHPUnit\Framework\TestCase
{
    use \DOMWrap\Tests\Harness\TestTrait;

    public function testUnwrapNode() {
        $expected = '<html><body><a href="http://example.org/">this is a test</a></body></html>';
        $doc = $this->document('<html><article><a href="http://example.org/">this is a test</a></article></html>');
        $doc->find('article > a')->first()->unwrap();

        $this->assertXmlStringEqualsXmlString($expected, $doc->html());
    }

    public function testUnwrapNodeList() {
        $expected = '<html><body><a href="http://example.org/">this is a test</a><p>test!</p></body></html>';
        $doc = $this->document('<html><section><a href="http://example.org/">this is a test</a></section><section><p>test!</p></section></html>');
        $doc->find('a, p')->unwrap();

        $this->assertXmlStringEqualsXmlString($expected, $doc->html());
    }

    public function testUnwrapNodeListNested() {
        $expected = '<html><body><section><a href="http://example.org/">this is a test</a></section><section><p>test!</p></section></body></html>';
        $doc = $this->document('<html><em><section><a href="http://example.org/">this is a test</a></section><div><article><section><p>test!</p></section></article></div></em></html>');
        $doc->find('article, section')->unwrap();

        $this->assertXmlStringEqualsXmlString($expected, $doc->html());
    }

    public function testUnwrapNodeListDoesntExist() {
        $expected = '<html><body><section><a href="http://example.org/">this is a test</a></section><section><p>test!</p></section></body></html>';
        $doc = $this->document('<html><section><a href="http://example.org/">this is a test</a></section><section><p>test!</p></section></html>');
        $doc->find('article')->unwrap();

        $this->assertXmlStringEqualsXmlString($expected, $doc->html());
    }

}