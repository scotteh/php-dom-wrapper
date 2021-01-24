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
// @FIXME Newline
        $expected = '<html><body><ul><li>a<ul><li>a</li><li>a</li></ul>'."\n".'</li><li>a<ul><li>a</li><li>a</li></ul>'."\n".'</li></ul></body></html>';

        $doc = $this->document('<html><body><ul><li>a</li><li>a</li></ul></body></html>');
        $newNode = $doc->create($doc->find('ul'));
        $nodes = $doc->find('li');
        $nodes->appendWith($newNode);

        $this->assertXmlStringEqualsXmlString($expected, $doc->html());
    }

    public function testCreateDOMElement() {
// @FIXME Newline
        $expected = '<html><body><ul><li>a<span>xyz</span>'."\n".'</li><li>a<span>xyz</span>'."\n".'</li></ul></body></html>';

        $doc = $this->document('<html><body><ul><li>a</li><li>a</li></ul></body></html>');
        $input = new \DOMElement('span');
        $input->textContent ='xyz';
        $newNode = $doc->create($input);
        $nodes = $doc->find('li');
        $nodes->appendWith($newNode);

        $this->assertXmlStringEqualsXmlString($expected, $doc->html());
    }

}