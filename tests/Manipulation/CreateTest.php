<?php

namespace DOMWrap\Tests\Manipulation;

class CreateTest extends \PHPUnit\Framework\TestCase
{
    use \DOMWrap\Tests\Harness\TestTrait;

    public function testCreateString() {
        $expected = '<html><body><span><span>xyz</span></span></body></html>';

        $doc = $this->document('<html><body></body></html>');
        $newNode = $doc->create('<span><span>xyz</span></span>');
        $nodes = $doc->find('body');
        $nodes->first()->appendWith($newNode);

        $this->assertXmlStringEqualsXmlString($expected, $doc->html());
    }

    public function testCreateNodeList() {
        $expected = '<html><body><ul><li>a<ul><li>a</li><li>a</li></ul></li><li>a<ul><li>a</li><li>a</li></ul></li></ul></body></html>';

        $doc = $this->document('<html><body><ul><li>a</li><li>a</li></ul></body></html>');
        $newNode = $doc->create($doc->find('ul'));
        $nodes = $doc->find('li');
        $nodes->appendWith($newNode);

        // @NOTE str_replace is a temporary fix for PHP <= 8.0 where whitespace is added into XML.
        $this->assertXmlStringEqualsXmlString($expected, str_replace("\n", '', $doc->html()));
    }

    public function testCreateDOMElement() {
        $expected = '<html><body><ul><li>a<span>xyz</span></li><li>a<span>xyz</span></li></ul></body></html>';

        $doc = $this->document('<html><body><ul><li>a</li><li>a</li></ul></body></html>');
        $input = new \DOMElement('span');
        $input->textContent ='xyz';
        $newNode = $doc->create($input);
        $nodes = $doc->find('li');
        $nodes->appendWith($newNode);

        // @NOTE str_replace is a temporary fix for PHP <= 8.0 where whitespace is added into XML.
        $this->assertXmlStringEqualsXmlString($expected, str_replace("\n", '', $doc->html()));
    }

}