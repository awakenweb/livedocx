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
    public function setName($template_name , $path = null)
    {
        parent::setName($template_name);
        $this->directory = $path;
    }

    /**
     * Set the local template as active and upload it to be used when generating the final
     * document
     *
     * @return Local
     *
     * @throws TemplateException
     */
    public function setAsActive()
    {
        try {
            $templatecontent = $this->getBase64Contents();
            $format          = $this->getFormat();
        } catch ( FileExistException $ex ) {
            throw new TemplateException('Template file does not exist or is not readable' , $ex);
        }

        try {
            $this->getSoapClient()->SetLocalTemplate([
                'template' => $templatecontent ,
                'format'   => $format
            ]);
            $this->isActive = true;

            return $this;
        } catch ( SoapException $ex ) {
            throw new TemplateException('Error while setting the local template as the active template' , $ex);
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
        try {
            $templatecontent = $this->getBase64Contents();
            $filename        = basename($this->getName());
        } catch ( FileExistException $ex ) {
            throw new TemplateException('Template file does not exist or is not readable' , $ex);
        }

        try {
            $this->getSoapClient()->UploadTemplate([
                'template' => $templatecontent ,
                'filename' => $filename ,
            ]);

            $remoteTemplate = new Remote($this->getSoapClient());
            $remoteTemplate->setName($this->getName());

            return $remoteTemplate;
        } catch ( SoapException $ex ) {
            throw new TemplateException('Error while uploading the template' , $ex);
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
    public function getName($full = false)
    {
        if (! $full) {
            return $this->templateName;
        }
        $filename = $this->directory ? $this->directory . '/' . $this->templateName : $this->templateName;

        return str_replace('//' , '/' , $filename);
    }

    /**
     * Return the contents of the whole template file
     *
     * @return string
     *
     * @throws TemplateException
     */
    public function getContents()
    {
        try {
            $fileObj = new SplFileObject($this->getName(true) , 'r');
        } catch ( RuntimeException $ex ) {
            throw new FileExistException('The provided file is not readable' , $ex);
        }

        return file_get_contents($fileObj->getPathname());
    }

    /**
     * Return the format of the file
     *
     * @return string
     *
     * @throws TemplateException
     */
    public function getFormat()
    {
        try {
            $fileObj = new SplFileObject($this->getName(true) , 'r');
        } catch ( RuntimeException $ex ) {
            throw new FileExistException('The provided file is not readable' , $ex);
        }

        return $fileObj->getExtension();
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

}
