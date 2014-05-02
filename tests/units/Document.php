<?php

namespace Awakenweb\Livedocx\tests\units;

require_once '/vendor/autoload.php';

use Awakenweb\Livedocx\Document as LDXDocument;
use atoum;

class Document extends atoum
{

    /**
     * @dataProvider fakeValuesForDocument
     */
    public function test_new_document_throws_exceptions_with_invalid_parameters($value)
    {

        $this->exception(function () use ($value) {
                    $doc = new LDXDocument($value , 'random string');
                })
                ->isInstanceOf('Awakenweb\Livedocx\Exceptions\DocumentException')
                ->hasMessage('The name of the document must be a non empty string');

        $this->exception(function () use ($value) {
                    $doc = new LDXDocument('randomName.txt' , $value);
                })
                ->isInstanceOf('Awakenweb\Livedocx\Exceptions\DocumentException')
                ->hasMessage('The data of the document must be a non empty string');
    }

    /**
     * @dataProvider fakeValuesForDocument
     */
    public function test_save_document_throws_exceptions_with_invalid_parameters($destination)
    {
        $doc = new LDXDocument('test.txt' , 'This is a test purpose only string');

        $this->exception(function () use ($doc , $destination) {
                    $doc->save($destination);
                })
                ->isInstanceOf('Awakenweb\Livedocx\Exceptions\DocumentException')
                ->hasMessage('The destination directory of the document must be a non empty string');
    }

    /* --- DATA PROVIDERS --- */

    public function fakeValuesForDocument()
    {
        return [
            ['' ] ,
            [null ] ,
            [new \StdClass() ] ,
            [1234567890 ]
        ];
    }

}
