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

use Awakenweb\Livedocx\Exceptions\SoapException;
use Awakenweb\Livedocx\Exceptions\TemplateException;
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
     * @throws TemplateException
     */
    public function setAsActive()
    {
        try {
            $this->getSoapClient()->SetRemoteTemplate(['filename' => $this->getName()]);
            return $this;
        } catch (SoapException $ex) {
            throw new TemplateException('Error while setting the remote template as the active template', $ex);
        }
    }

    protected function getName()
    {
        return $this->templateName;
    }

    /**
     * Check if a remote template exists on the Livedocx service
     *
     * @return boolean
     */
    public function exists()
    {
        try {
            $result = $this->soapClient->templateExists(['filename' => $this->getName()]);
            return boolval($result->TemplateExistsResult);
        } catch (SoapException $ex) {
            throw new TemplateException('Error while verifying the existence of a remote template', $ex);
        }
    }

    /**
     * Download a remote template from the Livedocx service
     *
     * @throws TemplateException
     */
    public function download()
    {
        try {
            $result = $this->getSoapClient()->DownloadTemplate(array(
                'filename' => basename($this->getName())));
            return base64_decode($result->DownloadTemplateResult);
        } catch (SoapException $ex) {
            throw new TemplateException('Error while downloading the remote template from Livedocx service', $ex);
        }
    }

    /**
     * Delete a remote template from the Livedocx service
     *
     * @return \Awakenweb\Livedocx\Templates\Remote
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
        } catch (SoapException $ex) {
            throw new TemplateException('Error while deleting the remote template from Livedocx service', $ex);
        }
    }

}
