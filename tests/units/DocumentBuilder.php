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
