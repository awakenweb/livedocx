<?php

namespace Awakenweb\Livedocx\tests\units\Templates;

require_once '/vendor/autoload.php';

use atoum;
use Awakenweb\Livedocx\Exceptions\SoapException;
use Awakenweb\Livedocx\Templates\Local as LdxLocal;

/**
 * tests for common code between Remote and Local templates are contained
 * in this test suite
 */
class Local extends atoum
{

    /**
     * @dataProvider filenamesProvider
     */
    public function test_setters_and_getters($filename, $dirname, $expectedName, $expectedFullName)
    {
        $mock  = $this->scaffoldMock();
        $local = new LdxLocal($mock);
        $local->setName($filename, $dirname);
        $this->string($local->getName())
                ->isEqualTo($expectedName)
                ->string($local->getName(true))
                ->isEqualTo($expectedFullName);
    }

    /* = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = */
    /* = - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - = */
    /* = -                                COMMON CODE                                - = */
    /* = - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - = */
    /* = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = */

    /**
     *
     */
    public function test_listAll_throw_exceptions_when_soap_error_occurs()
    {
        $mock = $this->scaffoldMock();

        $mock->getMockController()->ListTemplates = function() {
            throw new SoapException('random exception');
        };
        $local = new LdxLocal($mock);
        $this->exception(function() use ($local) {
                    $local->listAll();
                })
                ->isInstanceOf('Awakenweb\Livedocx\Exceptions\TemplateException')
                ->hasNestedException();
    }

    /**
     *
     */
    public function test_listAll_return_empty_array()
    {
        $mock = $this->scaffoldMock();

        $mock->getMockController()->ListTemplates = function() {
            $ret = new \stdClass();
            return $ret;
        };
        $local = new LdxLocal($mock);
        $this->array($local->listAll())
                ->isEmpty();
    }

    /**
     *
     */
    public function test_listAll_return_array()
    {
        $mock = $this->scaffoldMock();

        $mock->getMockController()->ListTemplates = function() {
            $ret                      = new \stdClass();
            $ret->ListTemplatesResult = new \stdClass();
            return $ret;
        };
        $mock->getMockController()->backendListArrayToMultiAssocArray = function() {
            return [
                ['filename' => 'test.doc', 'filesize' => 123456],
                ['filename' => 'test2.doc', 'filesize' => 654321],
                ['filename' => 'test3.doc', 'filesize' => 615243]
            ];
        };

        $local = new LdxLocal($mock);

        $this->array($local->listAll())
                ->isNotEmpty();
    }

    /**
     *
     */
    public function test_getAcceptedTemplateFormats_return_array()
    {

        $mock = $this->scaffoldMock();

        $mock->getMockController()->GetTemplateFormats = function() {
            $r                                   = new \stdClass();
            $r->GetTemplateFormatsResult         = new \stdClass();
            $r->GetTemplateFormatsResult->string = ['Q', 'b', 'C'];

            return $r;
        };
        $image = new LdxLocal($mock);
        $this->array($image->getAcceptedTemplateFormats())
                ->isNotEmpty();
    }

    /**
     *
     */
    public function test_getAcceptedTemplateFormats_throw_exceptions_when_soap_error_occurs()
    {
        $mock = $this->scaffoldMock();

        $mock->getMockController()->GetTemplateFormats = function() {
            throw new SoapException('random exception');
        };
        $local = new LdxLocal($mock);
        $this->exception(function() use ($local) {
                    $local->getAcceptedTemplateFormats();
                })
                ->isInstanceOf('Awakenweb\Livedocx\Exceptions\TemplateException')
                ->hasNestedException();
    }

    /**
     *
     */
    public function test_getAvailableDocumentFormats_return_array()
    {

        $mock = $this->scaffoldMock();

        $mock->getMockController()->GetDocumentFormats = function() {
            $r                                   = new \stdClass();
            $r->GetDocumentFormatsResult         = new \stdClass();
            $r->GetDocumentFormatsResult->string = ['Q', 'b', 'C'];

            return $r;
        };
        $image = new LdxLocal($mock);
        $this->array($image->getAvailableDocumentFormats())
                ->isNotEmpty();
    }

    /**
     *
     */
    public function test_getAvailableDocumentFormats_throw_exceptions_when_soap_error_occurs()
    {
        $mock = $this->scaffoldMock();

        $mock->getMockController()->GetDocumentFormats = function() {
            throw new SoapException('random exception');
        };
        $local = new LdxLocal($mock);
        $this->exception(function() use ($local) {
                    $local->getAvailableDocumentFormats();
                })
                ->isInstanceOf('Awakenweb\Livedocx\Exceptions\TemplateException')
                ->hasNestedException();
    }

    /**
     *
     */
    public function test_getAvailableFonts_return_array()
    {

        $mock = $this->scaffoldMock();

        $mock->getMockController()->GetFontNames = function() {
            $r                             = new \stdClass();
            $r->GetFontNamesResult         = new \stdClass();
            $r->GetFontNamesResult->string = ['Q', 'b', 'C'];

            return $r;
        };
        $image = new LdxLocal($mock);
        $this->array($image->getAvailableFonts())
                ->isNotEmpty();
    }

    /**
     *
     */
    public function test_getAvailableFonts_throw_exceptions_when_soap_error_occurs()
    {
        $mock = $this->scaffoldMock();

        $mock->getMockController()->GetFontNames = function() {
            throw new SoapException('random exception');
        };
        $local = new LdxLocal($mock);
        $this->exception(function() use ($local) {
                    $local->getAvailableFonts();
                })
                ->isInstanceOf('Awakenweb\Livedocx\Exceptions\TemplateException')
                ->hasNestedException();
    }

    /**
     *
     */
    public function test_ignoreSubTemplates_calls_soap_server()
    {
        $mock = $this->scaffoldMock();

        $mock->getMockController()->SetIgnoreSubTemplates = true;

        $local = new LdxLocal($mock);

        $this->when(function() use ($local) {
                    $local->ignoreSubTemplates(true);
                })
                ->mock($mock)
                ->call('SetIgnoreSubTemplates')
                ->once();
    }

    /**
     *
     */
    public function test_ignoreSubTemplates_throw_exceptions_when_soap_error_occurs()
    {
        $mock = $this->scaffoldMock();

        $mock->getMockController()->SetIgnoreSubTemplates = function() {
            throw new SoapException('random exception');
        };
        $local = new LdxLocal($mock);

        $this->exception(function() use ($local) {
                    $local->ignoreSubTemplates();
                })
                ->isInstanceOf('Awakenweb\Livedocx\Exceptions\TemplateException')
                ->hasNestedException();
    }

    /**
     *
     */
    public function test_ignoreSubTemplates_throw_exceptions_with_wrong_parameter()
    {
        $mock = $this->scaffoldMock();

        $mock->getMockController()->SetIgnoreSubTemplates = true;

        $local = new LdxLocal($mock);

        $this->exception(function() use ($local) {
                    $local->ignoreSubTemplates('random string');
                })
                ->isInstanceOf('Awakenweb\Livedocx\Exceptions\TemplateException');
    }

    /**
     *
     */
    public function test_ignoreListOfSubTemplates_calls_soap_server()
    {
        $mock = $this->scaffoldMock();

        $mock->getMockController()->SetSubTemplateIgnoreList = true;

        $local = new LdxLocal($mock);

        $this->when(function() use ($local) {
                    $local->ignoreListOfSubTemplates(['test', 'value', 'random value']);
                })
                ->mock($mock)
                ->call('SetSubTemplateIgnoreList')
                ->once();
    }

    /**
     *
     */
    public function test_ignoreListOfSubTemplates_throw_exceptions_when_soap_error_occurs()
    {
        $mock = $this->scaffoldMock();

        $mock->getMockController()->SetSubTemplateIgnoreList = function() {
            throw new SoapException('random exception');
        };
        $local = new LdxLocal($mock);

        $this->exception(function() use ($local) {
                    $local->ignoreListOfSubTemplates(['random value', 'test']);
                })
                ->isInstanceOf('Awakenweb\Livedocx\Exceptions\TemplateException')
                ->hasNestedException();
    }

    /**
     *
     */
    public function test_ignoreListOfSubTemplates_throw_exceptions_with_wrong_parameter()
    {
        $mock = $this->scaffoldMock();

        $mock->getMockController()->SetSubTemplateIgnoreList = true;

        $local = new LdxLocal($mock);

        $this->exception(function() use ($local) {
                    $local->ignoreListOfSubTemplates(1234567890);
                })
                ->isInstanceOf('Awakenweb\Livedocx\Exceptions\TemplateException');
    }

    /* = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = */
    /* = - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - = */
    /* = -                            LOCAL TEMPLATE CODE                            - = */
    /* = - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - = */
    /* = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = */

    /**
     *
     */
    public function test_getContents_throw_exception_when_file_is_not_readable()
    {
        $mock  = $this->scaffoldMock();
        $local = new LdxLocal($mock);
        if (file_exists(__DIR__ . '/test.dat')) {
            unlink(__DIR__ . '/test.dat');
        }
        $local->setName('test.dat', __DIR__);

        $this->exception(function() use ($local) {
                    $local->getContents();
                })
                ->isInstanceOf('Awakenweb\Livedocx\Exceptions\FileExistException');
    }

    /**
     *
     */
    public function test_getContents_read_file_and_return_string()
    {
        $mock  = $this->scaffoldMock();
        $local = new LdxLocal($mock);

        $mlrs = 'Multi line' . PHP_EOL . 'random string' . PHP_EOL;
        if (file_exists(__DIR__ . '/test.dat')) {
            unlink(__DIR__ . '/test.dat');
        }
        file_put_contents(__DIR__ . '/test.dat', $mlrs);

        $local->setName('test.dat', __DIR__);

        $this->string($local->getContents())
                ->isEqualTo($mlrs);

        unlink(__DIR__ . '/test.dat');
    }

    /**
     *
     */
    public function test_getFormat_throw_exception_when_file_is_not_readable()
    {
        $mock  = $this->scaffoldMock();
        $local = new LdxLocal($mock);
        if (file_exists(__DIR__ . '/test.dat')) {
            unlink(__DIR__ . '/test.dat');
        }
        $local->setName('test.dat', __DIR__);

        $this->exception(function() use ($local) {
                    $local->getFormat();
                })
                ->isInstanceOf('Awakenweb\Livedocx\Exceptions\FileExistException');
    }

    /**
     *
     */
    public function test_getFormat_read_file_and_return_string()
    {
        $mock = $this->scaffoldMock();

        $local = new LdxLocal($mock);

        if (file_exists(__DIR__ . '/test.dat')) {
            unlink(__DIR__ . '/test.dat');
        }
        file_put_contents(__DIR__ . '/test.dat', 'random content');

        $local->setName('test.dat', __DIR__);

        $this->string($local->getFormat())
                ->isEqualTo('dat');

        unlink(__DIR__ . '/test.dat');
    }

    /**
     *
     */
    public function test_setAsActive_throw_exceptions_when_soap_error_occurs()
    {
        $mock = $this->scaffoldMock();

        $mock->getMockController()->SetLocalTemplate = function() {
            throw new SoapException('random exception');
        };
        if (file_exists(__DIR__ . '/test.dat')) {
            unlink(__DIR__ . '/test.dat');
        }
        file_put_contents(__DIR__ . '/test.dat', 'random content');
        $local = new LdxLocal($mock);
        $local->setName('test.dat', __DIR__);

        $this->exception(function() use ($local) {
                    $local->setAsActive();
                })->isInstanceOf('Awakenweb\Livedocx\Exceptions\TemplateException')
                ->hasMessage('Error while setting the local template as the active template');

        unlink(__DIR__ . '/test.dat');
    }

    /**
     *
     */
    public function test_setAsActive_throw_exceptions_when_file_does_not_exist()
    {
        $mock = $this->scaffoldMock();

        $mock->getMockController()->SetLocalTemplate = function() {
            throw new SoapException('random exception');
        };

        $local = new LdxLocal($mock);

        $this->exception(function() use ($local) {
                    $local->setAsActive();
                })->isInstanceOf('Awakenweb\Livedocx\Exceptions\TemplateException')
                ->hasMessage('Template file does not exist or is not readable');
    }

    /**
     *
     */
    public function test_setAsActive_read_file_and_extension()
    {
        $mock   = $this->scaffoldMock();
        $result = array();

        $mock->getMockController()->SetLocalTemplate = function($params) use (&$result) {
            $result = $params;
        };

        $content = 'Multi line' . PHP_EOL . 'random string' . PHP_EOL;
        if (file_exists(__DIR__ . '/test.dat')) {
            unlink(__DIR__ . '/test.dat');
        }
        file_put_contents(__DIR__ . '/test.dat', $content);
        $encoded = base64_encode($content);

        $local = new LdxLocal($mock);
        $local->setName('test.dat', __DIR__);

        $this->when(function()use($local) {
                    $local->setAsActive();
                })
                ->string($result['template'])
                ->isEqualTo($encoded)
                ->string($result['format'])
                ->isEqualTo('dat');


        unlink(__DIR__ . '/test.dat');
    }

    /* = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = */
    /* = - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - = */
    /* = -                              DATA PROVIDERS                               - = */
    /* = - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - = */
    /* = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = */

    /**
     *
     * @return array
     */
    public function filenamesProvider()
    {
        return [
            ['testfile.doc', null, 'testfile.doc', 'testfile.doc'],
            ['thisIsATest.doc', '/test/', 'thisIsATest.doc', '/test/thisIsATest.doc']
        ];
    }

    protected function scaffoldMock()
    {
        $this->mockGenerator->orphanize('__construct');
        $this->mockGenerator->shuntParentClassCalls();
        $mock = new \mock\Awakenweb\Livedocx\Soap\Client();

        return $mock;
    }

}
