<?php

namespace Awakenweb\Livedocx\tests\units;

require_once 'vendor/autoload.php';

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
            $r->GetDocumentFormatsResult->string = ['Q' , 'b' , 'C' ];

            return $r;
        };
        $document = new LDXDocument($mock);
        $this->array($document->getAvailableFormats())
                ->containsValues(['q' , 'b' , 'c' ]);
    }

    /**
     *
     */
    public function test_getAvailableFormats_throw_exception_when_soap_error_occurs()
    {
        $mock = $this->scaffoldMock();

        $mock->getMockController()->GetDocumentFormats = function() {
            throw new SoapException('random exception');
        };

        $document = new LDXDocument($mock);
        $this->exception(function() use ($document) {
                    $document->getAvailableFormats();
                })
                ->isInstanceOf('Awakenweb\Livedocx\Exceptions\Document\StatusException')
                ->hasMessage('Error while getting the list of available document formats')
                ->hasNestedException();
    }

    /**
     *
     */
    public function test_setPassword_throw_exception_when_soap_error_occurs()
    {
        $mock = $this->scaffoldMock();

        $mock->getMockController()->SetDocumentPassword = function() {
            throw new SoapException('random exception');
        };

        $document = new LDXDocument($mock);
        $this->exception(function() use ($document) {
                    $document->setPassword('random password');
                })
                ->isInstanceOf('Awakenweb\Livedocx\Exceptions\Document\PasswordException')
                ->hasMessage('Error while setting a password for the document')
                ->hasNestedException();
    }

    /**
     *
     */
    public function test_setPassword_is_chainable()
    {
        $mock = $this->scaffoldMock();

        $mock->getMockController()->SetDocumentPassword = true;

        $document = new LDXDocument($mock);
        $this->object($document->setPassword('toto'))
                ->isInstanceOf('Awakenweb\Livedocx\Document');
    }

    /**
     *
     */
    public function test_create_throw_exception_when_soap_error_occcurs()
    {
        $mock = $this->scaffoldMock();

        $mock->getMockController()->CreateDocument = function() {
            throw new SoapException('random exception');
        };

        $document = new LDXDocument($mock);
        $this->exception(function() use ($document) {
                    $document->create();
                })
                ->isInstanceOf('Awakenweb\Livedocx\Exceptions\Document\CreateException')
                ->hasMessage('Error while mergin fields to the template on Livedocx service')
                ->hasNestedException();
    }

    /**
     *
     */
    public function test_create_is_chainable()
    {
        $mock     = $this->scaffoldMock();
        $document = new LDXDocument($mock);

        $mock->getMockController()->CreateDocument = true;
        $this->object($document->create())
                ->isInstanceOf('Awakenweb\Livedocx\Document')
                ->isIdenticalTo($document);
    }

    /**
     *
     */
    public function test_retrieve_throw_exception_when_format_is_not_provided()
    {
        $mock     = $this->scaffoldMock();
        $document = new LDXDocument($mock);
        $this->exception(function() use ($document) {
                    $document->retrieve();
                })
                ->isInstanceOf('Awakenweb\Livedocx\Exceptions\Document\InvalidException')
                ->hasMessage('You must provide a format to retrieve the document');
    }

    /**
     *
     */
    public function test_retrieve_throw_exception_when_soap_error_occcurs()
    {
        $mock     = $this->scaffoldMock();
        $document = new LDXDocument($mock);

        $mock->getMockController()->RetrieveDocument = function() {
            throw new SoapException('random exception');
        };
        $this->exception(function() use ($document) {
                    $document->retrieve('test');
                })
                ->isInstanceOf('Awakenweb\Livedocx\Exceptions\Document\RetrieveException')
                ->hasMessage('Error while retrieving the final document from Livedocx service')
                ->hasNestedException();
    }

    /**
     *
     */
    public function test_retrieve_use_encapsulated_format_when_none_is_provided()
    {
        $mock     = $this->scaffoldMock();
        $document = new LDXDocument($mock);

        $mock->getMockController()->RetrieveDocument = function() use($document) {
            $ret                         = new stdClass();
            $ret->RetrieveDocumentResult = base64_encode($document->getFormat());
            return $ret;
        };

        $document->setFormat('doc');
        $this->string($document->retrieve())
                ->isEqualTo('doc');
    }

    /**
     *
     */
    public function test_retrieve_return_string()
    {
        $mock     = $this->scaffoldMock();
        $document = new LDXDocument($mock);

        $mock->getMockController()->RetrieveDocument = function() {
            $ret                         = new stdClass();
            $ret->RetrieveDocumentResult = base64_encode('random string');
            return $ret;
        };
        $this->string($document->retrieve('doc'))
                ->isEqualTo('random string');
    }

    /**
     *
     */
    public function test_getAccessOptions_throw_exception_when_soap_error_occurs()
    {
        $mock     = $this->scaffoldMock();
        $document = new LDXDocument($mock);

        $mock->getMockController()->GetDocumentAccessOptions = function() {
            throw new SoapException('random content');
        };

        $this->exception(function() use ($document) {
                    $document->getAccessOptions();
                })
                ->isInstanceOf('Awakenweb\Livedocx\Exceptions\Document\PermissionsException')
                ->hasMessage('Error while getting the list of available permissions for the document')
                ->hasNestedException();
    }

    /**
     *
     */
    public function test_getAccessOptions_return_empty_array_when_no_result()
    {
        $mock     = $this->scaffoldMock();
        $document = new LDXDocument($mock);

        $mock->getMockController()->GetDocumentAccessOptions = function() {
            $ret                                 = new stdClass();
            $ret->GetDocumentAccessOptionsResult = new stdClass();
            return $ret;
        };
        $this->array($document->getAccessOptions())
                ->isEmpty();
    }

    /**
     *
     */
    public function test_getAccessOptions_return_array()
    {
        $mock     = $this->scaffoldMock();
        $document = new LDXDocument($mock);

        $mock->getMockController()->GetDocumentAccessOptions = function() {
            $ret                                         = new stdClass();
            $ret->GetDocumentAccessOptionsResult         = new stdClass();
            $ret->GetDocumentAccessOptionsResult->string = ['random content' , 'random value' , 'random string' ];
            return $ret;
        };
        $this->array($document->getAccessOptions())
                ->containsValues(['random content' , 'random value' , 'random string' ]);
    }

    /**
     *
     */
    public function test_setPermissions_throw_exception_when_invalid_parameters()
    {
        $mock     = $this->scaffoldMock();
        $document = new LDXDocument($mock);

        $this->exception(function() use ($document) {
                    $document->setPermissions(null , 'hello');
                })
                ->isInstanceOf('Awakenweb\Livedocx\Exceptions\Document\InvalidException')
                ->hasMessage('Permissions and password must be respectively an array and a string');

        $this->exception(function() use ($document) {
                    $document->setPermissions(array() , null);
                })
                ->isInstanceOf('Awakenweb\Livedocx\Exceptions\Document\InvalidException')
                ->hasMessage('Permissions and password must be respectively an array and a string');
    }

    /**
     *
     */
    public function test_setPermissions_throw_exception_when_soap_error_occurs()
    {
        $mock     = $this->scaffoldMock();
        $document = new LDXDocument($mock);

        $mock->getMockController()->SetDocumentAccessPermissions = function() {
            throw new SoapException('random content');
        };

        $this->exception(function() use ($document) {
                    $document->setPermissions([ ] , 'test string');
                })
                ->isInstanceOf('Awakenweb\Livedocx\Exceptions\Document\PermissionsException')
                ->hasMessage('Error while setting the list of permissions and master password for the document')
                ->hasNestedException();
    }

    /**
     *
     */
    public function test_setPermissions_is_chainable()
    {
        $mock     = $this->scaffoldMock();
        $document = new LDXDocument($mock);

        $mock->getMockController()->SetDocumentAccessPermissions = true;

        $this->object($document->setPermissions([ ] , 'random string'))
                ->isInstanceOf('Awakenweb\Livedocx\Document')
                ->isIdenticalTo($document);
    }

    /**
     *
     */
    public function test_getMetaFiles_with_no_parameters_return_empty_array_when_no_result()
    {
        $mock     = $this->scaffoldMock();
        $document = new LDXDocument($mock);

        $mock->getMockController()->GetAllMetafiles = function() {
            $ret                        = new stdClass();
            $ret->GetAllMetafilesResult = new \stdClass();
            return $ret;
        };

        $this->array($document->getMetaFiles())
                ->isEmpty();
    }

    /**
     *
     */
    public function test_getMetaFiles_with_no_parameters_return_array_when_soap_returns_array()
    {
        $mock     = $this->scaffoldMock();
        $document = new LDXDocument($mock);

        $mock->getMockController()->GetAllMetafiles = function() {
            $ret                                = new stdClass();
            $ret->GetAllMetafilesResult         = new \stdClass();
            $ret->GetAllMetafilesResult->string = [
                base64_encode('random string') ,
                base64_encode('another string')
            ];
            return $ret;
        };

        $this->array($document->getMetaFiles())
                ->containsValues([ 'random string' , 'another string' ]);
    }

    /**
     *
     */
    public function test_getMetaFiles_with_no_parameters_return_array_when_soap_returns_string()
    {
        $mock     = $this->scaffoldMock();
        $document = new LDXDocument($mock);

        $mock->getMockController()->GetAllMetafiles = function() {
            $ret                                = new stdClass();
            $ret->GetAllMetafilesResult         = new \stdClass();
            $ret->GetAllMetafilesResult->string = base64_encode('random string');
            return $ret;
        };

        $this->array($document->getMetaFiles())
                ->contains('random string');
    }

    /**
     *
     */
    public function test_getMetaFiles_with_no_parameters_throw_exception_when_soap_error_occurs()
    {
        $mock     = $this->scaffoldMock();
        $document = new LDXDocument($mock);

        $mock->getMockController()->GetAllMetafiles = function() {
            throw new SoapException('random content');
        };

        $this->exception(function() use ($document) {
                    $document->getMetaFiles();
                })
                ->isInstanceOf('Awakenweb\Livedocx\Exceptions\Document\MetafilesException')
                ->hasMessage('Error while retrieving the document from Livedocx service')
                ->hasNestedException();
    }

    /**
     *
     */
    public function test_getMetaFiles_with_parameters_throw_exception_when_invalid_parameters()
    {
        $mock     = $this->scaffoldMock();
        $document = new LDXDocument($mock);

        $this->exception(function() use ($document) {
                    $document->getMetaFiles(10 , 0);
                })
                ->isInstanceOf('Awakenweb\Livedocx\Exceptions\Document\InvalidException')
                ->hasMessage('Start page for metafiles must be inferior to end page');
    }

    /**
     *
     */
    public function test_getMetaFiles_with_parameters_return_empty_array_when_no_result()
    {
        $mock     = $this->scaffoldMock();
        $document = new LDXDocument($mock);

        $mock->getMockController()->GetMetafiles = function() {
            $ret                     = new stdClass();
            $ret->GetMetafilesResult = new \stdClass();
            return $ret;
        };

        $this->array($document->getMetaFiles(2 , 3))
                ->isEmpty();
    }

    /**
     *
     */
    public function test_getMetaFiles_with_parameters_return_array_when_soap_returns_array()
    {
        $mock     = $this->scaffoldMock();
        $document = new LDXDocument($mock);

        $mock->getMockController()->GetMetafiles = function() {
            $ret                             = new stdClass();
            $ret->GetMetafilesResult         = new \stdClass();
            $ret->GetMetafilesResult->string = [
                base64_encode('random string') ,
                base64_encode('another string')
            ];
            return $ret;
        };

        $this->array($document->getMetaFiles(1 , 2))
                ->containsValues([ 'random string' , 'another string' ]);
    }

    /**
     *
     */
    public function test_getMetaFiles_with_parameters_return_array_when_soap_returns_string()
    {
        $mock     = $this->scaffoldMock();
        $document = new LDXDocument($mock);

        $mock->getMockController()->GetMetafiles = function() {
            $ret                             = new stdClass();
            $ret->GetMetafilesResult         = new \stdClass();
            $ret->GetMetafilesResult->string = base64_encode('random string');
            return $ret;
        };

        $this->array($document->getMetaFiles(1 , 2))
                ->contains('random string');
    }

    /**
     *
     */
    public function test_getMetaFiles_with_parameters_throw_exception_when_soap_error_occurs()
    {
        $mock     = $this->scaffoldMock();
        $document = new LDXDocument($mock);

        $mock->getMockController()->GetMetafiles = function() {
            throw new SoapException('random content');
        };

        $this->exception(function() use ($document) {
                    $document->getMetaFiles(1 , 2);
                })
                ->isInstanceOf('Awakenweb\Livedocx\Exceptions\Document\MetafilesException')
                ->hasMessage('Error while retrieving the document from Livedocx service')
                ->hasNestedException();
    }

    /**
     *
     */
    public function test_getAsBitmaps_with_no_parameters_return_empty_array_when_no_result()
    {
        $mock     = $this->scaffoldMock();
        $document = new LDXDocument($mock);

        $mock->getMockController()->GetAllBitmaps = function() {
            $ret                      = new stdClass();
            $ret->GetAllBitmapsResult = new \stdClass();
            return $ret;
        };

        $this->array($document->getAsBitmaps(1 , 'test'))
                ->isEmpty();
    }

    /**
     *
     */
    public function test_getAsBitmaps_with_no_parameters_return_array_when_soap_returns_array()
    {
        $mock     = $this->scaffoldMock();
        $document = new LDXDocument($mock);

        $mock->getMockController()->GetAllBitmaps = function() {
            $ret                              = new stdClass();
            $ret->GetAllBitmapsResult         = new stdClass();
            $ret->GetAllBitmapsResult->string = [
                base64_encode('random string') ,
                base64_encode('another string')
            ];
            return $ret;
        };

        $this->array($document->getAsBitmaps(1 , 'test'))
                ->containsValues([ 'random string' , 'another string' ]);
    }

    /**
     *
     */
    public function test_getAsBitmaps_with_no_parameters_return_array_when_soap_returns_string()
    {
        $mock     = $this->scaffoldMock();
        $document = new LDXDocument($mock);

        $mock->getMockController()->GetAllBitmaps = function() {
            $ret                              = new stdClass();
            $ret->GetAllBitmapsResult         = new stdClass();
            $ret->GetAllBitmapsResult->string = base64_encode('random string');
            return $ret;
        };

        $this->array($document->getAsBitmaps(1 , 'test'))
                ->contains('random string');
    }

    /**
     *
     */
    public function test_getAsBitmaps_with_no_parameters_throw_exception_when_soap_error_occurs()
    {
        $mock     = $this->scaffoldMock();
        $document = new LDXDocument($mock);

        $mock->getMockController()->GetAllBitmaps = function() {
            throw new SoapException('random content');
        };

        $this->exception(function() use ($document) {
                    $document->getAsBitmaps(1 , 'test');
                })
                ->isInstanceOf('Awakenweb\Livedocx\Exceptions\Document\BitmapsException')
                ->hasMessage('Error while retrieving the final document as bitmaps from Livedocx service')
                ->hasNestedException();
    }

    /**
     *
     */
    public function test_getAsBitmaps_with_parameters_throw_exception_when_invalid_parameters()
    {
        $mock     = $this->scaffoldMock();
        $document = new LDXDocument($mock);

        $this->exception(function() use ($document) {
                    $document->getAsBitmaps(1 , 'test' , 10 , 0);
                })
                ->isInstanceOf('Awakenweb\Livedocx\Exceptions\Document\InvalidException')
                ->hasMessage('Start page for bitmaps must be inferior to end page');

        $this->exception(function() use ($document) {
                    $document->getAsBitmaps(null , 10 , 1 , 10);
                })
                ->isInstanceOf('Awakenweb\Livedocx\Exceptions\Document\InvalidException')
                ->hasMessage('zoomFactor and format must be respectively integer and string');
    }

    /**
     *
     */
    public function test_getAsBitmaps_with_parameters_return_empty_array_when_no_result()
    {
        $mock     = $this->scaffoldMock();
        $document = new LDXDocument($mock);

        $mock->getMockController()->GetBitmaps = function() {
            $ret                   = new stdClass();
            $ret->GetBitmapsResult = new stdClass();
            return $ret;
        };

        $this->array($document->getAsBitmaps(1 , 'test' , 2 , 3))
                ->isEmpty();
    }

    /**
     *
     */
    public function test_getAsBitmaps_with_parameters_return_array_when_soap_returns_array()
    {
        $mock     = $this->scaffoldMock();
        $document = new LDXDocument($mock);

        $mock->getMockController()->GetBitmaps = function() {
            $ret                           = new stdClass();
            $ret->GetBitmapsResult         = new stdClass();
            $ret->GetBitmapsResult->string = [
                base64_encode('random string') ,
                base64_encode('another string')
            ];
            return $ret;
        };

        $this->array($document->getAsBitmaps(1 , 'test' , 1 , 2))
                ->containsValues([ 'random string' , 'another string' ]);
    }

    /**
     *
     */
    public function test_getAsBitmaps_with_parameters_return_array_when_soap_returns_string()
    {
        $mock     = $this->scaffoldMock();
        $document = new LDXDocument($mock);

        $mock->getMockController()->GetBitmaps = function() {
            $ret                           = new stdClass();
            $ret->GetBitmapsResult         = new stdClass();
            $ret->GetBitmapsResult->string = base64_encode('random string');
            return $ret;
        };

        $this->array($document->getAsBitmaps(1 , 'test' , 1 , 2))
                ->contains('random string');
    }

    /**
     *
     */
    public function test_getAsBitmaps_with_parameters_throw_exception_when_soap_error_occurs()
    {
        $mock     = $this->scaffoldMock();
        $document = new LDXDocument($mock);

        $mock->getMockController()->GetBitmaps = function() {
            throw new SoapException('random content');
        };

        $this->exception(function() use ($document) {
                    $document->getAsBitmaps(1 , 'test' , 1 , 2);
                })
                ->isInstanceOf('Awakenweb\Livedocx\Exceptions\Document\BitmapsException')
                ->hasMessage('Error while retrieving the final document as paginated bitmaps from Livedocx service')
                ->hasNestedException();
    }

    /* = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = */
    /* = - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - = */
    /* = -                               DATA PROVIDERS                              - = */
    /* = - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - = */
    /* = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = */

    protected
            function scaffoldMock()
    {
        $this->mockGenerator->orphanize('__construct');
        $this->mockGenerator->shuntParentClassCalls();
        $mock = new \mock\Awakenweb\Livedocx\Soap\Client();


        return $mock;
    }

}
