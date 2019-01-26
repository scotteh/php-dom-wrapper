<?php

namespace DOMWrap\Tests\Manipulation;

class CreateTest extends \PHPUnit\Framework\TestCase
{
    use \DOMWrap\Tests\Harness\TestTrait;

    public function testCreateString() {
        $expected = $this->document('<html><body><span><span>xyz</span></span></body></html>');

        $doc = $this->document('<html><body></body></html>');
        $newNode = $doc->create('<span><span>xyz</span></span>');
        $nodes = $doc->find('body');
        $nodes->first()->append($newNode);

        $this->assertEqualXMLStructure($expected->children()->first(), $doc->children()->first(), true);
    }

    public function testCreateNodeList() {
        $expected = $this->document('<html><body><ul><li>a<ul><li>a</li><li>a</li></ul></li><li>a<ul><li>a</li><li>a</li></ul></li></ul></body></html>');

        $doc = $this->document('<html><body><ul><li>a</li><li>a</li></ul></body></html>');
        $newNode = $doc->create($doc->find('ul'));
        $nodes = $doc->find('li');
        $nodes->append($newNode);

        $this->assertEqualXMLStructure($expected->children()->first(), $doc->children()->first(), true);
    }

    public function testCreateDOMElement() {
        $expected = $this->document('<html><body><ul><li>a<span>xyz</span></li><li>a<span>xyz</span></li></ul></body></html>');

        $doc = $this->document('<html><body><ul><li>a</li><li>a</li></ul></body></html>');
        $input = new \DOMElement('span');
        $input->textContent ='xyz';
        $newNode = $doc->create($input);
        $nodes = $doc->find('li');
        $nodes->append($newNode);

        $this->assertEqualXMLStructure($expected->children()->first(), $doc->children()->first(), true);
    }

}