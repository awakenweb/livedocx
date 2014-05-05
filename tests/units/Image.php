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
        $mock = $this->scaffoldMock();

        $image = new LdxImage($mock);
        $image->setFilename($filename, $dirname);

        $this->string($image->getName())
                ->isEqualTo($expectedName)
                ->string($image->getName(true))
                ->isEqualTo($expectedFullName);
    }

    /**
     *
     */
    public function test_listAll_throws_exceptions()
    {
        $mock = $this->scaffoldMock();

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

    /**
     *
     */
    public function test_listAll_return_empty_array()
    {
        $mock = $this->scaffoldMock();

        $mock->getMockController()->ListImages = function() {
            $ret = new \stdClass();
            return $ret;
        };
        $image = new LdxImage($mock);
        $this->array($image->listAll())
                ->isEmpty();
    }

    /**
     *
     */
    public function test_listAll_return_array()
    {
        $mock = $this->scaffoldMock();

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

    /**
     *
     */
    public function test_getAcceptedFormats_return_array()
    {

        $mock = $this->scaffoldMock();

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

    /**
     *
     */
    public function test_getAcceptedFormats_return_empty_array()
    {

        $mock = $this->scaffoldMock();

        $mock->getMockController()->GetImageImportFormats = function() {
            return new \stdClass();
        };

        $image = new LdxImage($mock);

        $this->array($image->getAcceptedFormats())
                ->isEmpty();
    }

    /**
     *
     */
    public function test_getAcceptedFormats_throw_exception()
    {

        $mock = $this->scaffoldMock();

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

    /**
     *
     */
    public function test_getAvailableReturnFormats_return_array()
    {

        $mock = $this->scaffoldMock();

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

    /**
     *
     */
    public function test_getAvailableReturnFormats_return_empty_array()
    {

        $mock = $this->scaffoldMock();

        $mock->getMockController()->GetImageExportFormats = function() {
            return new \stdClass();
        };

        $image = new LdxImage($mock);

        $this->array($image->getAvailableReturnFormats())
                ->isEmpty();
    }

    /**
     *
     */
    public function test_getAvailableReturnFormats_throw_exception()
    {

        $mock = $this->scaffoldMock();

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

    /**
     *
     */
    public function test_exists_return_boolean()
    {
        $mock = $this->scaffoldMock();

        $mock->getMockController()->ImageExists = function() {
            return mt_rand(0, 1);
        };

        $image = new LdxImage($mock);
        $image->setFilename('test.jpg');

        $this->boolean($image->exists());
    }

    /**
     *
     */
    public function test_exists_throw_exception()
    {
        $mock = $this->scaffoldMock();

        $mock->getMockController()->ImageExists = function() {
            throw new Exceptions\SoapException('random exception');
        };

        $image = new LdxImage($mock);
        $image->setFilename('test.jpg');

        $this->exception(function() use ($image) {
                    $image->exists();
                })
                ->isInstanceOf('Awakenweb\Livedocx\Exceptions\ImageException')
                ->hasNestedException();
    }

    /**
     *
     */
    public function test_delete_return_itself()
    {
        $mock = $this->scaffoldMock();

        $mock->getMockController()->DeleteImage = true;

        $image = new LdxImage($mock);
        $image->setFilename('random_name.jpg');

        $this->object($image->delete())
                ->isIdenticalTo($image);
    }

    /**
     *
     */
    public function test_delete_throw_exception()
    {
        $mock = $this->scaffoldMock();

        $mock->getMockController()->DeleteImage = function() {
            throw new Exceptions\SoapException('random exception');
        };

        $image = new LdxImage($mock);
        $image->setFilename('random_name.jpg');

        $this->exception(function() use ($image) {
                    $image->delete();
                })
                ->isInstanceOf('Awakenweb\Livedocx\Exceptions\ImageException')
                ->hasNestedException();
    }

    /**
     *
     */
    public function test_download_return_string()
    {
        $mock = $this->scaffoldMock();

        $mock->getMockController()->DownloadImage = function() {
            $r                      = new \stdClass();
            $r->DownloadImageResult = base64_encode('random string');
            return $r;
        };

        $image = new LdxImage($mock);
        $image->setFilename('random_name.jpg');

        $this->string($image->download())
                ->isEqualTo('random string');
    }

    /**
     *
     */
    public function test_download_throw_exception()
    {
        $mock = $this->scaffoldMock();

        $mock->getMockController()->DownloadImage = function() {
            throw new Exceptions\SoapException('random exception');
        };

        $image = new LdxImage($mock);
        $image->setFilename('random_name.jpg');

        $this->exception(function() use ($image) {
                    $image->download();
                })
                ->isInstanceOf('Awakenweb\Livedocx\Exceptions\ImageException')
                ->hasNestedException();
    }

    /**
     *
     */
    public function test_upload_return_itself()
    {
        $mock = $this->scaffoldMock();

        file_put_contents(__DIR__ . '/test.dat', 'some random content');

        $mock->getMockController()->UploadImage = true;

        $image = new LdxImage($mock);
        $image->setFilename('test.dat', __DIR__);

        $this->object($image->upload())
                ->isIdenticalTo($image);

        unlink(__DIR__ . '/test.dat');
    }

    /**
     *
     */
    public function test_upload_throw_exception_when_no_file()
    {
        $mock = $this->scaffoldMock();

        $mock->getMockController()->UploadImage = function() {
            throw new Exceptions\SoapException('random exception');
        };

        $image = new LdxImage($mock);
        $image->setFilename('test.dat', __DIR__);

        $this->exception(function() use ($image) {
                    $image->upload();
                })
                ->isInstanceOf('Awakenweb\Livedocx\Exceptions\ImageException');
    }

    /**
     *
     */
    public function test_upload_throw_exception_when_soap_error()
    {
        $mock = $this->scaffoldMock();

        $mock->getMockController()->UploadImage = function() {
            throw new Exceptions\SoapException('random exception');
        };

        file_put_contents(__DIR__ . '/test.dat', 'some random content');

        $image = new LdxImage($mock);
        $image->setFilename('test.dat', __DIR__);

        $this->exception(function() use ($image) {
                    $image->upload();
                })
                ->isInstanceOf('Awakenweb\Livedocx\Exceptions\ImageException')
                ->hasNestedException();

        unlink(__DIR__ . '/test.dat');
    }

    /* = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = */
    /* = - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - = */
    /* = -                               DATA PROVIDERS                              - = */
    /* = - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - = */
    /* = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = */

    /**
     *
     * @return array
     */
    public function filenamesProvider()
    {
        return [
            ['testfile.jpg', null, 'testfile.jpg', 'testfile.jpg'],
            ['thisIsATest.png', '/test/', 'thisIsATest.png', '/test/thisIsATest.png']
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
