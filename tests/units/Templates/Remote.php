<?php

namespace Awakenweb\Livedocx\tests\units\Templates;

require_once '/vendor/autoload.php';

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
        $local = new LdxRemote($this->scaffoldMock());
        $local->setName('test.dat');
        $this->string($local->getName())
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
                ->isInstanceOf('Awakenweb\Livedocx\Exceptions\TemplateException')
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
                ->isInstanceOf('Awakenweb\Livedocx\Exceptions\TemplateException')
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
                ->isInstanceOf('Awakenweb\Livedocx\Exceptions\TemplateException')
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
                ->isInstanceOf('Awakenweb\Livedocx\Exceptions\TemplateException')
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
                ->isInstanceOf('Awakenweb\Livedocx\Exceptions\TemplateException')
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
                ->isInstanceOf('Awakenweb\Livedocx\Exceptions\TemplateException')
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
