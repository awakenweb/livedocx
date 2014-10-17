<?php

namespace Awakenweb\Livedocx\tests\units;

require_once 'vendor/autoload.php';

use atoum;
use Awakenweb\Livedocx\DocumentBuilder as LDXDocumentBuilder;

class DocumentBuilder extends atoum
{

    public function test_new_document_builder_()
    {
        $mock = $this->scaffoldMock();
        $dbuilder = new LDXDocumentBuilder($mock);

        $this->object($dbuilder)
                ->isInstanceOf('\Awakenweb\Livedocx\DocumentBuilder');
    }

    public function test_save_throws_exception_when_no_document_retrieved()
    {

        $mock = $this->scaffoldMock();
        $dbuilder = new LDXDocumentBuilder($mock);

        $this->exception(function() use ($dbuilder) {
                    $dbuilder->save();
                })
                ->isInstanceOf('\Awakenweb\Livedocx\Exceptions\DocumentBuilder\RetrieveException')
                ->hasMessage('You have to retrieve the document before saving it');
    }

    public function test_get_throws_exception_when_no_document_retrieved()
    {

        $mock = $this->scaffoldMock();
        $dbuilder = new LDXDocumentBuilder($mock);

        $this->exception(function() use ($dbuilder) {
                    $dbuilder->get();
                })
                ->isInstanceOf('\Awakenweb\Livedocx\Exceptions\DocumentBuilder\RetrieveException')
                ->hasMessage('You have to retrieve the document before using it');
    }

    public function test_workflow_calls_mock_methods()
    {
        $mock = $this->scaffoldMock();
        $dbuilder = new LDXDocumentBuilder($mock);

        $this->when(function() use ($dbuilder) {
            $dbuilder->createDocument('myTestDocument')
                    ->fromTemplate('testTemplate.docx')
                    ->with(['testValue1' => 'valueOne', 'testValue2' => 'valueTwo'])
                    ->withImages(['testImage' => __DIR__ . 'test.jpg'])
                    ->retrieve('pdf');
        })
        ->string($dbuilder->get())
                ->isEqualTo();
    }

    /* = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = */
    /* = - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - = */
    /* = -                               DATA PROVIDERS                              - = */
    /* = - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - = */
    /* = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = */

    protected function scaffoldMock()
    {
        $this->mockGenerator->orphanize('__construct');
        $this->mockGenerator->shuntParentClassCalls();
        $mock = new \mock\Awakenweb\Livedocx\Livedocx();

        return $mock;
    }

}
