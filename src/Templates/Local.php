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

/**
 * Description of Local
 *
 * @author Administrateur
 */
class Local extends Awakenweb\Livedocx\Template
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
     *
     */
    public function upload()
    {
        try {
            if (!is_readable($filename)) {
                throw new Exception\InvalidArgumentException(
                'Cannot read local template from disk.'
                );
            }
            try {
                $this->getSoapClient()->UploadTemplate(array(
                    'template' => base64_encode(file_get_contents($filename)),
                    'filename' => basename($filename),
                ));
            } catch (\Exception $e) {
                throw new Exception\RuntimeException(
                $e->getMessage()
                );
            }
        } catch (Exceptions\SoapException $ex) {
            throw new Exceptions\TemplateException('Error while getting the list of accepted template formats', $ex);
        }
    }

    /**
     * Return the name of the template file.
     * If $full parameter is provided, return the full name with path
     *
     * @param type $full
     * @return type
     */
    protected function getName($full = false)
    {
        if (!$full) {
            return $this->templateName;
        }
        $filename = $this->directory ? $this->directory . '/' . $this->templateName : $this->templateName;
        return str_replace('//', '/', $filename);
    }

}
