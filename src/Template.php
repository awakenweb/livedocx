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
 * Description of Template
 *
 * @author Mathieu SAVELLI <mathieu.savelli@awakenweb.fr>
 */
class Template implements Soap\HasSoapClient
{

    use Soap\HasSoapClient;

    /**
     *
     * @var string
     */
    protected $templateName;

    /**
     *
     * @var boolean
     */
    protected $remote = false;

    public function setName($template_name)
    {

    }

    /**
     * Set the template as a remote template, stored on the Livedocx servers
     *
     * @return \Awakenweb\Livedocx\Template
     */
    public function setRemote()
    {
        $this->remote = true;

        return $this;
    }

    /**
     * Set the template as a local template, stored locally on your own server
     *
     * @return \Awakenweb\Livedocx\Template
     */
    public function setLocal()
    {
        $this->local = false;

        return $this;
    }

    /**
     * Check if the template is remote or local
     *
     * @return type
     */
    public function isRemote()
    {
        return $this->remote;
    }

    /**
     * Check if a remote template exists on the Livedocx servers
     *
     * @return boolean
     */
    public function remoteExists()
    {
        $result = $this->soapClient->templateExists(['filename' => $this->templateName ]);

        return boolval($result->TemplateExistsResult);
    }

    /**
     *
     */
    public function upload()
    {

    }

    /**
     * Define a subtemplate to ignore when generating the final document
     *
     * @param type $subtemplate
     *
     * @return \Awakenweb\Livedocx\Template
     */
    public function ignoreSubTemplate($subtemplate)
    {
        return $this;
    }

    /**
     * Define a list of subtemplates to ignore when generating the final document
     *
     * @param array $subtemplates_list
     *
     * @return \Awakenweb\Livedocx\Template
     */
    public function ignoreSubTemplates(array $subtemplates_list)
    {
        foreach ($subtemplates_list as $subtemplate) {
            $this->ignoreSubTemplate($subtemplate);
        }

        return $this;
    }

}
