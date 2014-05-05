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

    public function setFilename($filename);

    public function listAll()
    {
        $return = [ ];

        $result = $this->getSoapClient()->ListImages();
        if ( isset($result->ListImagesResult) ) {
            $return = $this->getSoapClient()->backendListArrayToMultiAssocArray($result->ListImagesResult);
        }

        return $return;
    }

    public function getAcceptedFormats()
    {

        return array();
    }

    public function getAvailableReturnFormats()
    {

    }

    public function upload()
    {

    }

    /**
     *
     * @return type
     *
     * @throws Exceptions\ImageException
     */
    public function download()
    {

        try {
            $result = $this->getSoapClient()->DownloadImage(array(
                'filename' => basename($filename) ,
            ));
        } catch ( \Exception $e ) {
            throw new Exceptions\ImageException(
            'Error while downloading the image' , $e
            );
        }

        return base64_decode($result->DownloadImageResult);
    }

    /**
     * Check if an image exists on the Livedocx service
     *
     * @return boolean
     */
    public function exists()
    {
        $result = $this->getSoapClient()->ImageExists(array(
            'filename' => basename($filename) ,
        ));

        return ( boolean ) $result->ImageExistsResult;
    }

    /**
     * Delete the file from the Livedocx service
     *
     * @return \Awakenweb\Livedocx\Image
     */
    public function delete()
    {
        $this->getSoapClient()->DeleteImage(array(
            'filename' => basename($this->filename) ,
        ));

        return $this;
    }

}
