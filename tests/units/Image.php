<?php

namespace Awakenweb\Livedocx\tests\units;

require_once '/vendor/autoload.php';

use Awakenweb\Livedocx\Image as LdxImage,
    Awakenweb\Livedocx\Exceptions,
    atoum;

class Image extends atoum
{

    /**
     * @dataProvider filenamesProvider
     */
    public function test_setters_and_getters($filename, $dirname, $expectedName, $expectedFullName)
    {
        $this->mockGenerator->orphanize('__construct');
        $this->mockGenerator->shuntParentClassCalls();
        $mock = new \mock\Awakenweb\Livedocx\Soap\Client();

        $image = new LdxImage($mock);
        $image->setFilename($filename, $dirname);

        $this->string($image->getName())
                ->isEqualTo($expectedName)
                ->string($image->getName(true))
                ->isEqualTo($expectedFullName);
    }

    public function test_listAll_throws_exceptions()
    {
        $this->mockGenerator->orphanize('__construct');
        $this->mockGenerator->shuntParentClassCalls();
        $mock = new \mock\Awakenweb\Livedocx\Soap\Client();

        $mock->getMockController()->ListImages = function() {
            throw new Exceptions\SoapException('random exception');
        };

        $image = new LdxImage($mock);

        $this->exception(function() use ($image) {
                    $image->listAll();
                })
                ->isInstanceOf('Awakenweb\Livedocx\Exceptions\ImageException')
                ->hasNestedException();
    }

    public function test_listAll_return_empty_array()
    {
        $this->mockGenerator->orphanize('__construct');
        $this->mockGenerator->shuntParentClassCalls();
        $mock = new \mock\Awakenweb\Livedocx\Soap\Client();

        $mock->getMockController()->ListImages = function() {
            $ret = new \stdClass();
            return $ret;
        };
        $image = new LdxImage($mock);
        $this->array($image->listAll())
                ->isEmpty();
    }

    public function test_listAll_return_array()
    {
        $this->mockGenerator->orphanize('__construct');
        $this->mockGenerator->shuntParentClassCalls();
        $mock = new \mock\Awakenweb\Livedocx\Soap\Client();

        $mock->getMockController()->ListImages = function() {
            $ret                   = new \stdClass();
            $ret->ListImagesResult = new \stdClass();
            return $ret;
        };
        $mock->getMockController()->backendListArrayToMultiAssocArray = function() {
            return [
                ['filename' => 'test.jpg', 'filesize' => 123456],
                ['filename' => 'test2.jpg', 'filesize' => 654321],
                ['filename' => 'test3.jpg', 'filesize' => 615243]
            ];
        };

        $image = new LdxImage($mock);

        $this->array($image->listAll())
                ->isNotEmpty();
    }

    public function test_getAcceptedFormats_return_array()
    {

        $this->mockGenerator->orphanize('__construct');
        $this->mockGenerator->shuntParentClassCalls();
        $mock = new \mock\Awakenweb\Livedocx\Soap\Client();

        $mock->getMockController()->GetImageImportFormats = function() {
            $r                                      = new \stdClass();
            $r->GetImageImportFormatsResult         = new \stdClass();
            $r->GetImageImportFormatsResult->string = ['Q', 'b', 'C'];

            return $r;
        };
        $image = new LdxImage($mock);
        $this->array($image->getAcceptedFormats())
                ->isNotEmpty();
    }

    public function test_getAcceptedFormats_return_empty_array()
    {

        $this->mockGenerator->orphanize('__construct');
        $this->mockGenerator->shuntParentClassCalls();

        $mock = new \mock\Awakenweb\Livedocx\Soap\Client();

        $mock->getMockController()->GetImageImportFormats = function() {
            return new \stdClass();
        };

        $image = new LdxImage($mock);

        $this->array($image->getAcceptedFormats())
                ->isEmpty();
    }

    public function test_getAcceptedFormats_throw_exception()
    {

        $this->mockGenerator->orphanize('__construct');
        $this->mockGenerator->shuntParentClassCalls();

        $mock = new \mock\Awakenweb\Livedocx\Soap\Client();

        $mock->getMockController()->GetImageImportFormats = function() {
            throw new Exceptions\SoapException('random exception');
        };

        $image = new LdxImage($mock);

        $this->exception(function() use ($image) {
                    $image->getAcceptedFormats();
                })
                ->isInstanceOf('Awakenweb\Livedocx\Exceptions\ImageException')
                ->hasNestedException();
    }

    public function test_getAvailableReturnFormats_return_array()
    {

        $this->mockGenerator->orphanize('__construct');
        $this->mockGenerator->shuntParentClassCalls();
        $mock = new \mock\Awakenweb\Livedocx\Soap\Client();

        $mock->getMockController()->GetImageExportFormats = function() {
            $r                                      = new \stdClass();
            $r->GetImageExportFormatsResult         = new \stdClass();
            $r->GetImageExportFormatsResult->string = ['Q', 'b', 'C'];
            return $r;
        };
        $image = new LdxImage($mock);
        $this->array($image->getAvailableReturnFormats())
                ->isNotEmpty();
    }

    public function test_getAvailableReturnFormats_return_empty_array()
    {

        $this->mockGenerator->orphanize('__construct');
        $this->mockGenerator->shuntParentClassCalls();

        $mock = new \mock\Awakenweb\Livedocx\Soap\Client();

        $mock->getMockController()->GetImageExportFormats = function() {
            return new \stdClass();
        };

        $image = new LdxImage($mock);

        $this->array($image->getAvailableReturnFormats())
                ->isEmpty();
    }

    public function test_getAvailableReturnFormats_throw_exception()
    {

        $this->mockGenerator->orphanize('__construct');
        $this->mockGenerator->shuntParentClassCalls();

        $mock = new \mock\Awakenweb\Livedocx\Soap\Client();

        $mock->getMockController()->GetImageExportFormats = function() {
            throw new Exceptions\SoapException('random exception');
        };

        $image = new LdxImage($mock);

        $this->exception(function() use ($image) {
                    $image->getAvailableReturnFormats();
                })
                ->isInstanceOf('Awakenweb\Livedocx\Exceptions\ImageException')
                ->hasNestedException();
    }

    /* -----------------------------------------------------------------------*\
     *                                                                           *
      \*------------------------------------------------------------------------ */

    public function filenamesProvider()
    {
        return [
            ['testfile.jpg', null, 'testfile.jpg', 'testfile.jpg'],
            [ 'thisIsATest.png', '/test/', 'thisIsATest.png', '/test/thisIsATest.png']
        ];
    }

}
