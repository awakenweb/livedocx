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

namespace Awakenweb\Livedocx\Templates;

use Awakenweb\Livedocx\Exceptions\FileExistException;
use Awakenweb\Livedocx\Exceptions\SoapException;
use Awakenweb\Livedocx\Exceptions\Templates\ActiveException;
use Awakenweb\Livedocx\Exceptions\Templates\DeleteException;
use Awakenweb\Livedocx\Exceptions\Templates\DownloadException;
use Awakenweb\Livedocx\Exceptions\Templates\InvalidException;
use Awakenweb\Livedocx\Exceptions\Templates\NonActiveException;
use Awakenweb\Livedocx\Exceptions\Templates\StatusException;
use Awakenweb\Livedocx\Exceptions\Templates\TemplateException;
use Awakenweb\Livedocx\Template;

/**
 * Description of Remote
 *
 * @author Administrateur
 */
class Remote extends Template
{

    /**
     * Set the remote template as active to be used when generating the final document
     *
     * @return Remote
     *
     * @throws ActiveException
     * @throws InvalidException
     */
    public function setAsActive()
    {
        if ( ! $this->exists() ) {
            throw new FileExistException('Remote template does not exist');
        }

        try {
            $this->getSoapClient()->SetRemoteTemplate(['filename' => $this->getName() ]);
            $this->isActive = true;

            return $this;
        } catch ( SoapException $ex ) {
            throw new ActiveException('Error while setting the remote template as the active template' , $ex);
        }
    }

    public function getName()
    {
        return $this->templateName;
    }

    /**
     * Check if a remote template exists on the Livedocx service
     *
     * @return boolean
     *
     * @throw StatusException
     */
    public function exists()
    {
        try {
            $result = $this->soapClient->templateExists(['filename' => $this->getName() ]);

            return ( bool ) $result->TemplateExistsResult;
        } catch ( SoapException $ex ) {
            throw new StatusException('Error while verifying the existence of a remote template' , $ex);
        }
    }

    /**
     * Return the list of all fields in the active template
     *
     * @return array
     *
     * @throws StatusException
     * @throws NonActiveException
     */
    public function getFieldNames()
    {
        if ( ! $this->isActive ) {
            throw new NonActiveException('You can only get the field names of the active template');
        }

        $ret = array();

        try {
            $result = $this->getSoapClient()->GetFieldNames();
        } catch ( SoapException $ex ) {
            throw new StatusException('Error while getting the list of all fields in the active template' , $ex);
        }

        if ( isset($result->GetFieldNamesResult->string) ) {
            if ( is_array($result->GetFieldNamesResult->string) ) {
                $ret = $result->GetFieldNamesResult->string;
            } else {
                $ret[] = $result->GetFieldNamesResult->string;
            }
        }

        return $ret;
    }

    /**
     * Download a remote template from the Livedocx service
     *
     * @throws TemplateException
     * @throws FileExistException
     */
    public function download()
    {
        if ( ! $this->exists() ) {
            throw new FileExistException('Remote template does not exist');
        }

        try {
            $result = $this->getSoapClient()->DownloadTemplate(array(
                'filename' => basename($this->getName()) ));

            return base64_decode($result->DownloadTemplateResult);
        } catch ( SoapException $ex ) {
            throw new DownloadException('Error while downloading the remote template from Livedocx service' , $ex);
        }
    }

    /**
     * Delete a remote template from the Livedocx service.
     *
     * @return Remote
     *
     * @throws TemplateException
     */
    public function delete()
    {
        try {
            $this->getSoapClient()->DeleteTemplate(array(
                'filename' => basename($this->getName())
            ));

            return $this;
        } catch ( SoapException $ex ) {
            throw new DeleteException('Error while deleting the remote template from Livedocx service' , $ex);
        }
    }

}
