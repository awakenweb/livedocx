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
use RuntimeException;
use SplFileObject;

/**
 * Description of Local
 *
 * @author Administrateur
 */
class Local extends Template
{

    protected $directory;

    /**
     * Set the name of the template and its path
     *
     * @param string $template_name
     * @param string $path
     */
    public function setName($template_name, $path = null)
    {
        parent::setName($template_name);
        $this->directory = $path;
    }

    /**
     * Set the local template as active to be used when generating the final document and
     * upload it
     *
     * @return Local
     *
     * @throws TemplateException
     */
    public function setAsActive()
    {
        try {
            $this->getSoapClient()->SetLocalTemplate(array(
                'template' => $this->getBase64Contents(),
                'format'   => $this->getFormat(),
            ));
            return $this;
        } catch (SoapException $ex) {
            throw new TemplateException('Error while setting the local template as the active template', $ex);
        }
    }

    /**
     * Upload the local template to Livedocx service and return a new instance of corresponding
     * remote template
     *
     * @return Remote
     *
     * @throws TemplateException
     */
    public function upload()
    {

        if (!is_readable($this->getName(true))) {
            throw new TemplateException('Cannot read local template from disk.');
        }
        try {
            $this->getSoapClient()->UploadTemplate(array(
                'template' => $this->getBase64Contents(),
                'filename' => basename($this->getName()),
            ));

            $remoteTemplate = new Remote($this->getSoapClient());
            $remoteTemplate->setName($this->getName());

            return $remoteTemplate;
        } catch (SoapException $ex) {
            throw new TemplateException('Error while uploading the template', $ex);
        }
    }

    /**
     * Return the name of the template file.
     * If $full parameter is provided, return the full name with path
     *
     * @param boolean $full
     *
     * @return string
     */
    protected function getName($full = false)
    {
        if (!$full) {
            return $this->templateName;
        }
        $filename = $this->directory ? $this->directory . '/' . $this->templateName : $this->templateName;
        return str_replace('//', '/', $filename);
    }

    /**
     * Return the contents of the whole template file
     *
     * @return string
     *
     * @throws TemplateException
     */
    protected function getContents()
    {
        try {
            $fileObj = new SplFileObject($this->getName(true), 'r');
        } catch (RuntimeException $ex) {
            throw new TemplateException('The provided file is not readable', $ex);
        }
        $result = file_get_contents($fileObj->getPathname());
        if ($result === true) {
            return $result;
        }
    }

    /**
     * Return the content of the template as a base64 encoded string
     *
     * @return string
     *
     * @throws TemplateException @see Local::getContents
     */
    protected function getBase64Contents()
    {
        return base64_encode($this->getContents());
    }

    /**
     * Return the format of the file
     *
     * @return type
     *
     * @throws TemplateException
     */
    protected function getFormat()
    {
        try {
            $fileObj = new SplFileObject($this->getName(true), 'r');
        } catch (RuntimeException $ex) {
            throw new TemplateException('The provided file is not readable', $ex);
        }
        return $fileObj->getExtension();
    }

}
