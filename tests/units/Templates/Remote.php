<?php

namespace Awakenweb\Livedocx\tests\units\Templates;

require_once 'vendor/autoload.php';

use atoum;
use Awakenweb\Livedocx\Exceptions\SoapException;
use Awakenweb\Livedocx\Templates\Remote as LdxRemote;
use stdClass;

/**
 * tests for common code between Remote and Remote templates are contained
 * in this test suite
 */
class Remote extends atoum
{

    public function test_setters_and_getters()
    {
        $remote = new LdxRemote($this->scaffoldMock());
        $remote->setName('test.dat');
        $this->string($remote->getName())
                ->isEqualTo('test.dat');
    }

    /* = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = */
    /* = - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - = */
    /* = -                           REMOTE TEMPLATE CODE                            - = */
    /* = - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - = */
    /* = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = */

    /**
     *
     * @dataProvider booleanProvider
     */
    public function test_exists_return_boolean($value)
    {
        $mock = $this->scaffoldMock();

        $mock->getMockController()->TemplateExists = function() use ($value) {
            $ret                       = new stdClass();
            $ret->TemplateExistsResult = $value;
            return $ret;
        };

        $remote = new LdxRemote($mock);
        $remote->setName('test.tpl');

        $this->boolean($remote->exists())
                ->isEqualTo($value);
    }

    /**
     *
     */
    public function test_exists_throw_exception_when_soap_error_occurs()
    {
        $mock = $this->scaffoldMock();

        $mock->getMockController()->TemplateExists = function() {
            throw new SoapException('random exception');
        };

        $remote = new LdxRemote($mock);
        $remote->setName('test.tpl');

        $this->exception(function() use ($remote) {
                    $remote->exists();
                })
                ->isInstanceOf('Awakenweb\Livedocx\Exceptions\Templates\StatusException')
                ->hasMessage('Error while verifying the existence of a remote template')
                ->hasNestedException();
    }

    /**
     *
     */
    public function test_setAsActive_throw_exception_when_remote_template_does_not_exist()
    {
        $mock = $this->scaffoldMock();

        $mock->getMockController()->TemplateExists = function() {
            $ret                       = new stdClass();
            $ret->TemplateExistsResult = false;
            return $ret;
        };

        $remote = new LdxRemote($mock);
        $remote->setName('test.tpl');

        $this->exception(function() use ($remote) {
                    $remote->setAsActive();
                })
                ->isInstanceOf('Awakenweb\Livedocx\Exceptions\FileExistException')
                ->hasMessage('Remote template does not exist');
    }

    /**
     *
     */
    public function test_setAsActive_throw_exception_when_soap_error_occurs()
    {
        $mock = $this->scaffoldMock();

        $mock->getMockController()->TemplateExists = function() {
            $ret                       = new stdClass();
            $ret->TemplateExistsResult = true;
            return $ret;
        };

        $mock->getMockController()->SetRemoteTemplate = function() {
            throw new SoapException('random exception');
        };

        $remote = new LdxRemote($mock);
        $remote->setName('test.tpl');

        $this->exception(function() use ($remote) {
                    $remote->setAsActive();
                })
                ->isInstanceOf('Awakenweb\Livedocx\Exceptions\Templates\ActiveException')
                ->hasMessage('Error while setting the remote template as the active template')
                ->hasNestedException();
    }

    /**
     *
     */
    public function test_setAsActive_is_chainable()
    {

        $mock = $this->scaffoldMock();

        $mock->getMockController()->TemplateExists = function() {
            $ret                       = new stdClass();
            $ret->TemplateExistsResult = true;
            return $ret;
        };

        $mock->getMockController()->SetRemoteTemplate = true;

        $remote = new LdxRemote($mock);
        $remote->setName('test.tpl');

        $this->object($remote->setAsActive())
                ->isIdenticalTo($remote);
    }

    /**
     *
     */
    public function test_download_throw_exception_when_remote_file_does_not_exist()
    {
        $mock = $this->scaffoldMock();

        $mock->getMockController()->TemplateExists = function() {
            $ret                       = new stdClass();
            $ret->TemplateExistsResult = false;
            return $ret;
        };

        $remote = new LdxRemote($mock);
        $remote->setName('test.tpl');

        $this->exception(function() use ($remote) {
                    $remote->download();
                })
                ->isInstanceOf('Awakenweb\Livedocx\Exceptions\FileExistException')
                ->hasMessage('Remote template does not exist');
    }

    /**
     *
     */
    public function test_download_throw_exception_when_soap_error_occurs()
    {
        $mock = $this->scaffoldMock();

        $mock->getMockController()->TemplateExists = function() {
            $ret                       = new stdClass();
            $ret->TemplateExistsResult = true;
            return $ret;
        };

        $mock->getMockController()->DownloadTemplate = function() {
            throw new SoapException('random exception');
        };

        $remote = new LdxRemote($mock);
        $remote->setName('test.tpl');

        $this->exception(function() use ($remote) {
                    $remote->download();
                })
                ->isInstanceOf('Awakenweb\Livedocx\Exceptions\Templates\DownloadException')
                ->hasMessage('Error while downloading the remote template from Livedocx service')
                ->hasNestedException();
    }

    public function test_download_retrieve_content()
    {
        $mock = $this->scaffoldMock();

        $mock->getMockController()->TemplateExists = function() {
            $ret                       = new stdClass();
            $ret->TemplateExistsResult = true;
            return $ret;
        };

        $randomValue = uniqid();

        $mock->getMockController()->DownloadTemplate = function() use ($randomValue) {
            $ret                         = new \stdClass();
            $ret->DownloadTemplateResult = base64_encode($randomValue);
            return $ret;
        };

        $remote = new LdxRemote($mock);
        $remote->setName('test.tpl');

        $this->string($remote->download())
                ->isEqualTo($randomValue);
    }

    public function test_delete_throws_exception_when_soap_error_occurs()
    {
        $mock = $this->scaffoldMock();

        $mock->getMockController()->DeleteTemplate = function() {
            throw new SoapException('random exception');
        };

        $remote = new LdxRemote($mock);
        $remote->setName('test.tpl');

        $this->exception(function() use ($remote) {
                    $remote->delete();
                })
                ->isInstanceOf('Awakenweb\Livedocx\Exceptions\Templates\DeleteException')
                ->hasMessage('Error while deleting the remote template from Livedocx service')
                ->hasNestedException();
    }

    /**
     *
     */
    public function test_delete_is_chainable()
    {
        $mock = $this->scaffoldMock();

        $mock->getMockController()->DeleteTemplate = true;

        $remote = new LdxRemote($mock);
        $remote->setName('test.tpl');

        $this->object($remote->delete())
                ->isIdenticalTo($remote);
    }

    /**
     *
     */
    public function test_getFieldNames_throw_exception_when_template_is_not_active()
    {
        $mock = $this->scaffoldMock();

        $remote = new LdxRemote($mock);

        $this->exception(function() use ($remote) {
                    $remote->getFieldNames();
                })
                ->isInstanceOf('Awakenweb\Livedocx\Exceptions\Templates\NonActiveException')
                ->hasMessage('You can only get the field names of the active template');
    }

    public function test_getFieldNames_throw_exception_when_soap_error_occurs()
    {
        $mock = $this->scaffoldMock();

        $mock->getMockController()->SetRemoteTemplate = true;
        $mock->getMockController()->TemplateExists    = function() {
            $ret                       = new stdClass();
            $ret->TemplateExistsResult = true;
            return $ret;
        };
        $mock->getMockController()->GetFieldNames = function() {
            throw new SoapException('random exception');
        };

        $remote = new LdxRemote($mock);

        $remote->setName('test-remote-1.dat');

        $this->exception(function() use ($remote) {
                    $remote->setAsActive();
                    $remote->getFieldNames(['random value' , 'test' ]);
                })
                ->isInstanceOf('Awakenweb\Livedocx\Exceptions\Templates\StatusException')
                ->hasMessage('Error while getting the list of all fields in the active template')
                ->hasNestedException();
    }

    public function test_getFieldNames_return_empty_array()
    {
        $mock = $this->scaffoldMock();

        $mock->getMockController()->SetRemoteTemplate = true;
        $mock->getMockController()->TemplateExists    = function() {
            $ret                       = new stdClass();
            $ret->TemplateExistsResult = true;
            return $ret;
        };
        $mock->getMockController()->GetFieldNames = function() {
            return new \stdClass();
        };

        $remote = new LdxRemote($mock);


        $remote->setName('test-remote-2.dat');
        $remote->setAsActive();

        $this->array($remote->getFieldNames())
                ->isEmpty();
    }

    public function test_getFieldNames_return_array_with_an_array()
    {
        $mock = $this->scaffoldMock();

        $mock->getMockController()->SetRemoteTemplate = true;
        $mock->getMockController()->TemplateExists    = function() {
            $ret                       = new stdClass();
            $ret->TemplateExistsResult = true;
            return $ret;
        };
        $mock->getMockController()->GetFieldNames = function() {
            $ret                              = new \stdClass();
            $ret->GetFieldNamesResult         = new \stdClass();
            $ret->GetFieldNamesResult->string = [ 'value' , 'value2' , 'value3' ];
            return $ret;
        };

        $remote = new LdxRemote($mock);


        $remote->setName('test-remote-3.dat');
        $remote->setAsActive();

        $this->array($remote->getFieldNames())
                ->containsValues([ 'value' , 'value2' , 'value3' ]);
    }

    public function test_getFieldNames_return_array_with_a_string()
    {
        $mock = $this->scaffoldMock();

        $mock->getMockController()->SetRemoteTemplate = true;
        $mock->getMockController()->TemplateExists    = function() {
            $ret                       = new stdClass();
            $ret->TemplateExistsResult = true;
            return $ret;
        };
        $mock->getMockController()->GetFieldNames = function() {
            $ret                              = new \stdClass();
            $ret->GetFieldNamesResult         = new \stdClass();
            $ret->GetFieldNamesResult->string = 'value';
            return $ret;
        };

        $remote = new LdxRemote($mock);

        $remote->setName('test-remote-4.dat');
        $remote->setAsActive();

        $this->array($remote->getFieldNames())
                ->containsValues([ 'value' ]);
    }

    /* = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = */
    /* = - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - = */
    /* = -                              DATA PROVIDERS                               - = */
    /* = - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - = */
    /* = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = */

    public function booleanProvider()
    {
        return [
            [ false ] , [ true ] , [ 1 ] , [ 0 ]
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
