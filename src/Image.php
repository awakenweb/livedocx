<?php

/*
 * The MIT License
 *
 * Copyright 2014 Mathieu SAVELLI <mathieu.savelli@awakenweb.fr>.
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 */

namespace Awakenweb\Livedocx;

use Awakenweb\Livedocx\Exceptions\FileExistException;
use Awakenweb\Livedocx\Exceptions\Image\DeleteException;
use Awakenweb\Livedocx\Exceptions\Image\DownloadException;
use Awakenweb\Livedocx\Exceptions\Image\ImageException;
use Awakenweb\Livedocx\Exceptions\Image\StatusException;
use Awakenweb\Livedocx\Exceptions\Image\UploadException;
use Awakenweb\Livedocx\Exceptions\SoapException;

/**
 * Description of Image
 *
 * @author Mathieu SAVELLI <mathieu.savelli@awakenweb.fr>
 */
class Image
{

    use Soap\HasSoapClient;

    /**
     *
     * @var string
     */
    protected $filename;

    /**
     *
     * @var string
     */
    protected $directory;

    /**
     * Define the file and the working directory for a localfile
     *
     * @param string      $filename
     * @param string|null $directory
     */
    public function setFilename($filename , $directory = null)
    {
        $this->filename  = $filename;
        $this->directory = $directory;
    }

    /**
     * Return the name of the image file.
     * If $full parameter is provided, return the full name with path
     *
     * @param boolean $full
     *
     * @return string
     */
    public function getName($full = false)
    {
        if ( ! $full ) {
            return $this->filename;
        }
        $filename = $this->directory ? $this->directory . '/' . $this->filename : $this->filename;

        return str_replace('//' , '/' , $filename);
    }

    /**
     * Return a list of all images present on the Livedocx service
     *
     * @return array
     *
     * @throws StatusException
     */
    public function listAll()
    {
        try {
            $return = [ ];
            $result = $this->getSoapClient()->ListImages();
            if ( isset($result->ListImagesResult) ) {
                $return = $this->getSoapClient()->backendListArrayToMultiAssocArray($result->ListImagesResult);
            }

            return $return;
        } catch ( SoapException $ex ) {
            throw new StatusException('Error while obtaining the list of all images' , $ex);
        }
    }

    /**
     * Get the list of accepted images format for upload
     *
     * @return array
     *
     * @throws StatusException
     */
    public function getAcceptedFormats()
    {
        try {
            $ret    = array();
            $result = $this->getSoapClient()->GetImageImportFormats();
            if ( isset($result->GetImageImportFormatsResult->string) ) {
                $ret = $result->GetImageImportFormatsResult->string;
                $ret = array_map('strtolower' , $ret);
            }

            return $ret;
        } catch ( SoapException $ex ) {
            throw new StatusException('Error while obtaining the list of accepted image formats' , $ex);
        }
    }

    /**
     * Get the list of accepted formats for image generation
     *
     * @return array
     *
     * @throws StatusException
     */
    public function getAvailableReturnFormats()
    {
        try {
            $ret    = array();
            $result = $this->getSoapClient()->GetImageExportFormats();
            if ( isset($result->GetImageExportFormatsResult->string) ) {
                $ret = $result->GetImageExportFormatsResult->string;
                $ret = array_map('strtolower' , $ret);
            }

            return $ret;
        } catch ( SoapException $ex ) {
            throw new StatusException('Error while obtaining the list of all available image formats' , $ex);
        }
    }

    /**
     * Upload the active image on the livedocx server
     *
     * @return Image
     *
     * @throws UploadException
     * @throws FileExistException
     */
    public function upload()
    {
        $filename = $this->getName(true);

        if ( ! is_readable($filename) ) {
            throw new FileExistException('Image file from disk is not readable');
        }

        try {
            $this->getSoapClient()->UploadImage(array(
                'image'    => base64_encode(file_get_contents($filename)) ,
                'filename' => basename($filename) ,
            ));

            return $this;
        } catch ( SoapException $e ) {
            throw new UploadException('Error while uploading the image' , $e);
        }
    }

    /**
     * Download an image file from Livedocx
     *
     * @return string
     *
     * @throws DownloadException
     */
    public function download()
    {
        try {
            $result = $this->getSoapClient()->DownloadImage(array(
                'filename' => basename($this->filename) ,
            ));

            return base64_decode($result->DownloadImageResult);
        } catch ( SoapException $e ) {
            throw new DownloadException('Error while downloading the image' , $e);
        }
    }

    /**
     * Check if an image exists on the Livedocx service
     *
     * @return boolean
     *
     * @throws StatusException
     */
    public function exists()
    {
        try {
            $result = $this->getSoapClient()->ImageExists(array(
                'filename' => basename($this->filename) ,
            ));

            return ( boolean ) $result->ImageExistsResult;
        } catch ( SoapException $e ) {
            throw new StatusException('Error while verifying existence of the image' , $e);
        }
    }

    /**
     * Delete the file from the Livedocx service
     *
     * @return Image
     *
     * @throws DeleteException
     */
    public function delete()
    {
        try {
            $this->getSoapClient()->DeleteImage([
                'filename' => basename($this->getName(true)) ,
            ]);

            return $this;
        } catch ( SoapException $e ) {
            throw new DeleteException('Error while deleting the image' , $e);
        }
    }

}
