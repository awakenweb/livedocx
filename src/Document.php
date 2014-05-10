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

use Awakenweb\Livedocx\Exceptions\DocumentException;
use Awakenweb\Livedocx\Exceptions\SoapException;

/**
 * @author Mathieu SAVELLI <mathieu.savelli@awakenweb.fr>
 */
class Document
{

    use Soap\HasSoapClient;

    /**
     *
     * @var type
     */
    protected $data;

    /**
     *
     * @var type
     */
    protected $format;

    /**
     *
     * @param string $format
     */
    public function setFormat($format)
    {
        $this->format = $format;
    }

    /**
     *
     * @return string
     */
    public function getFormat()
    {
        return $this->format;
    }

    /**
     * Return a list of all available return formats you can ask for when generating the
     * document
     *
     * @return array
     *
     * @throws DocumentException
     */
    public function getAvailableFormats()
    {
        try {
            $ret    = array();
            $result = $this->getSoapClient()->GetDocumentFormats();
            if ( isset($result->GetDocumentFormatsResult->string) ) {
                $ret = $result->GetDocumentFormatsResult->string;
                $ret = array_map('strtolower' , $ret);
            }
            return $ret;
        } catch ( SoapException $ex ) {
            throw new DocumentException('Error while getting the list of available document formats' , $ex);
        }
    }

    /**
     * Set a password for the generated document
     *
     * @param string $password
     *
     * @return Document
     *
     * @throws DocumentException
     */
    public function setPassword($password)
    {
        try {
            $this->getSoapClient()->SetDocumentPassword(array(
                'password' => $password ,
            ));
            return $this;
        } catch ( SoapException $ex ) {
            throw new DocumentException('Error while setting a password for the document' , $ex);
        }
    }

    /**
     * Set a master password and a list of features accessible without this password
     *
     * @param array $permissions
     * @param string $password
     *
     * @throws DocumentException
     */
    public function setPermissions($permissions , $password)
    {
        if ( ! is_array($permissions) || ! is_string($password) || $password === '' ) {
            throw new DocumentException('Permissions and password must be respectively an array and a string');
        }
        try {
            $this->getSoapClient()->SetDocumentAccessPermissions(array(
                'permissions' => $permissions ,
                'password'    => $password ,
            ));
            return $this;
        } catch ( SoapException $ex ) {
            throw new DocumentException('Error while setting the list of permissions and master password for the document' , $ex);
        }
    }

    /**
     * Return a list of permissions you can use in setPermissions
     *
     * @return array
     *
     * @throws DocumentException
     */
    public function getAccessOptions()
    {
        try {
            $ret    = array();
            $result = $this->getSoapClient()->GetDocumentAccessOptions();
            if ( isset($result->GetDocumentAccessOptionsResult->string) ) {
                $ret = $result->GetDocumentAccessOptionsResult->string;
            }
            return $ret;
        } catch ( SoapException $ex ) {
            throw new DocumentException('Error while getting the list of available permissions for the document' , $ex);
        }
    }

    /**
     * Merge the fields to the template on Livedocx service
     *
     * @return Document
     *
     * @throws DocumentException
     */
    public function create()
    {
        try {
            $this->getSoapClient()->CreateDocument();
            return $this;
        } catch ( SoapException $ex ) {
            throw new DocumentException("Error while mergin fields to the template on Livedocx service" , $ex);
        }
    }

    /**
     * Retrieve the final document from Livedocx service.
     * If you didn't provide a format for retrieval using the setFormat method, you can do it here.
     *
     * @param string|null $format
     *
     * @return string
     *
     * @throws DocumentException
     */
    public function retrieve($format = null)
    {
        if ( is_null($format) ) {
            $format = $this->format;
        }
        if ( is_null($format) ) {
            throw new DocumentException('You must provide a format to retrieve the document');
        }
        $format = strtolower($format);

        try {
            $result     = $this->getSoapClient()->RetrieveDocument(array(
                'format' => $format ,
            ));
            $this->data = base64_decode($result->RetrieveDocumentResult);
            return $this->data;
        } catch ( SoapException $ex ) {
            throw new DocumentException('Error while retrieving the final document from Livedocx service' , $ex);
        }
    }

    /**
     * Get the binary metafiles of the document.
     * You can obtain paginated results by providing $from and $to parameters, or all binary
     * metafiles if no parameters are provided
     *
     * @param int $from
     * @param int $to
     *
     * @return array
     *
     * @throws DocumentException @see Document::getAllMetaFiles @see Document::getPaginatedMetafiles
     */
    public function getMetaFiles($from = null , $to = null)
    {
        if ( is_null($from) ) {
            $ret = $this->getAllMetafiles();
        } else {
            $ret = $this->getPaginatedMetaFiles($from , $to);
        }
        return $ret;
    }

    /**
     * Get paginated results for
     *
     * @param type $from
     * @param type $to
     *
     * @return type
     *
     * @throws DocumentException
     */
    protected function getPaginatedMetaFiles($from , $to)
    {
        if ( $from > $to ) {
            throw new DocumentException('Start page for metafiles must be inferior to end page');
        }

        $ret = array();
        try {
            $result = $this->getSoapClient()->GetMetafiles(array(
                'fromPage' => ( integer ) $from ,
                'toPage'   => ( integer ) $to ,
            ));
        } catch ( SoapException $ex ) {
            throw new DocumentException('Error while retrieving the document from Livedocx service' , $ex);
        }
        if ( isset($result->GetMetafilesResult->string) ) {
            $pageCounter = ( integer ) $from;
            if ( is_array($result->GetMetafilesResult->string) ) {
                foreach ( $result->GetMetafilesResult->string as $string ) {
                    $ret[ $pageCounter ] = base64_decode($string);
                    $pageCounter ++;
                }
            } else {
                $ret[ $pageCounter ] = base64_decode($result->GetMetafilesResult->string);
            }
        }
        return $ret;
    }

    /**
     *
     * @return type
     * @throws DocumentException
     */
    protected function getAllMetafiles()
    {
        $ret = array();
        try {
            $result = $this->getSoapClient()->GetAllMetafiles();
        } catch ( SoapException $ex ) {
            throw new DocumentException('Error while retrieving the document from Livedocx service' , $ex);
        }
        if ( isset($result->GetAllMetafilesResult->string) ) {
            $pageCounter = 1;
            if ( is_array($result->GetAllMetafilesResult->string) ) {
                foreach ( $result->GetAllMetafilesResult->string as $string ) {
                    $ret[ $pageCounter ] = base64_decode($string);
                    $pageCounter ++;
                }
            } else {
                $ret[ $pageCounter ] = base64_decode($result->GetAllMetafilesResult->string);
            }
        }
        return $ret;
    }

    /**
     * Get the the final document as bitmap files.
     * You can obtain paginated results by providing $from and $to parameters, or all pages as
     * bitmaps if only $zoomFactor and $format parameters are provided.
     *
     * @param int $zoomFactor
     * @param string $format
     * @param int $from
     * @param int $to
     *
     * @return type
     *
     * @throws DocumentException @see Document::getAllBitmaps @see Document::getPaginatedBitmaps
     */
    public function getAsBitmaps($zoomFactor , $format , $from = null , $to = null)
    {
        if ( ! is_int($zoomFactor) || ! is_string($format) ) {
            throw new DocumentException('zoomFactor and format must be respectively integer and string');
        }
        if ( is_null($from) ) {
            $ret = $this->getAllBitmaps($zoomFactor , $format);
        } else {
            $ret = $this->getPaginatedBitmaps($zoomFactor , $format , $from , $to);
        }
        return $ret;
    }

    /**
     * Retrieve the final document as a list of paginated bitmap files
     *
     * @param int $zoomFactor
     * @param string $format
     *
     * @return array
     *
     * @throws DocumentException
     */
    protected function getPaginatedBitmaps($zoomFactor , $format , $from , $to)
    {
        if ( $from > $to ) {
            throw new DocumentException('Start page for bitmaps must be inferior to end page');
        }

        $ret = array();
        try {
            $result = $this->getSoapClient()->GetBitmaps(array(
                'fromPage'   => ( integer ) $from ,
                'toPage'     => ( integer ) $to ,
                'zoomFactor' => ( integer ) $zoomFactor ,
                'format'     => ( string ) $format ,
            ));
        } catch ( SoapException $ex ) {
            throw new DocumentException('Error while retrieving the final document as paginated bitmaps from Livedocx service' , $ex);
        }
        if ( isset($result->GetBitmapsResult->string) ) {
            $pageCounter = ( integer ) $from;
            if ( is_array($result->GetBitmapsResult->string) ) {
                foreach ( $result->GetBitmapsResult->string as $string ) {
                    $ret[ $pageCounter ] = base64_decode($string);
                    $pageCounter ++;
                }
            } else {
                $ret[ $pageCounter ] = base64_decode($result->GetBitmapsResult->string);
            }
        }

        return $ret;
    }

    /**
     * Retrieve the final document as a list of bitmap files
     *
     * @param int $zoomFactor
     * @param string $format
     *
     * @return array
     *
     * @throws DocumentException
     */
    protected function getAllBitmaps($zoomFactor , $format)
    {
        $ret = array();
        try {
            $result = $this->getSoapClient()->GetAllBitmaps(array(
                'zoomFactor' => ( integer ) $zoomFactor ,
                'format'     => ( string ) $format ,
            ));
        } catch ( SoapException $ex ) {
            throw new DocumentException('Error while retrieving the final document as bitmaps from Livedocx service' , $ex);
        }

        if ( isset($result->GetAllBitmapsResult->string) ) {
            $pageCounter = 1;
            if ( is_array($result->GetAllBitmapsResult->string) ) {
                foreach ( $result->GetAllBitmapsResult->string as $string ) {
                    $ret[ $pageCounter ] = base64_decode($string);
                    $pageCounter ++;
                }
            } else {
                $ret[ $pageCounter ] = base64_decode($result->GetAllBitmapsResult->string);
            }
        }

        return $ret;
    }

    /**
     * Share a document on Livedocx service to make it available to everyone on the Internet.
     * Return the name of the document
     *
     * @return string
     *
     * @throws DocumentException
     */
    /* public function share()
      {
      $ret = null;
      try {
      $result = $this->getSoapClient()->ShareDocument();
      } catch ( SoapException $ex ) {
      throw new DocumentException('Error while sharing a document on Livedocx service' , $ex);
      }
      if ( isset($result->ShareDocumentResult) ) {
      $ret = ( string ) $result->ShareDocumentResult;
      }
      return $ret;
      }/* */

    /**
     *
     * @return
     *
     * @throws DocumentException
     */
    /* public function listAllShared()
      {
      $ret = array();
      try {
      $result = $this->getSoapClient()->ListSharedDocuments();
      } catch ( SoapException $ex ) {
      throw new DocumentException('Error while obtaining the list of all shared documents stored on Livedocx service' , $ex);
      }
      if ( isset($result->ListSharedDocumentsResult) ) {
      $ret = $this->backendListArrayToMultiAssocArray(
      $result->ListSharedDocumentsResult
      );
      }
      return $ret;
      }/* */

    /**
     * Delete a shared document from the Livedocx service service
     *
     * @throws DocumentException
     */
    /* public function deleteShared()
      {
      try {
      $this->getSoapClient()->DeleteSharedDocument(array(
      'filename' => basename($this->name) ,
      ));
      } catch ( SoapException $ex ) {
      throw new DocumentException('Error while deleting a shared document from Livedocx service' , $ex);
      }
      }/* */

    /**
     * Download a shared document from the Livedocx service service
     *
     * @return string
     *
     * @throws DocumentException
     */
    /* public function downloadShared()
      {
      try {
      $result = $this->getSoapClient()->DownloadSharedDocument(array(
      'filename' => basename($this->name) ,
      ));
      } catch ( SoapException $ex ) {
      throw new DocumentException('Error while deleting a shared document from Livedocx service' , $ex);
      }

      return base64_decode($result->DownloadSharedDocumentResult);
      }/* */

    /**
     * Verify the presence of a shared document on Livedocx service
     *
     * @return boolean
     *
     * @throws DocumentException @see Document::listAllShared
     */
    /* public function isShared()
      {
      $ret             = false;
      $sharedDocuments = $this->listAllShared();
      foreach ( $sharedDocuments as $shareDocument ) {
      if ( isset($shareDocument[ 'filename' ]) && (basename($this->name) === $shareDocument[ 'filename' ])
      ) {
      $ret = true;
      break;
      }
      }
      return $ret;
      }/* */
}
