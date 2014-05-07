<?php

namespace Awakenweb\Livedocx\tests\units;

require_once '/vendor/autoload.php';

use atoum;
use Awakenweb\Livedocx\Document as LDXDocument;
use Awakenweb\Livedocx\Exceptions\SoapException;
use stdClass;

class Document extends atoum
{

    /**
     *
     */
    public function test_getAvailableFormats_return_array()
    {

        $mock = $this->scaffoldMock();

        $mock->getMockController()->GetDocumentFormats = function() {
            $r                                   = new stdClass();
            $r->GetDocumentFormatsResult         = new stdClass();
            $r->GetDocumentFormatsResult->string = ['Q', 'b', 'C'];

            return $r;
        };
        $document = new LDXDocument($mock);
        $this->array($document->getAvailableFormats())
                ->containsValues(['q', 'b', 'c']);
    }

    /**
     *
     */
    public function test_getAvailableFormats_throw_exceptions_when_soap_error_occurs()
    {
        $mock = $this->scaffoldMock();

        $mock->getMockController()->GetDocumentFormats = function() {
            throw new SoapException('random exception');
        };

        $document = new LDXDocument($mock);
        $this->exception(function() use ($document) {
                    $document->getAvailableFormats();
                })
                ->isInstanceOf('Awakenweb\Livedocx\Exceptions\DocumentException')
                ->hasMessage('Error while getting the list of available document formats')
                ->hasNestedException();
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
        $mock = new \mock\Awakenweb\Livedocx\Soap\Client();

        return $mock;
    }

}
