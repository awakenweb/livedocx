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
abstract class Template
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
        $this->templateName = $template_name;
    }

    public function getAcceptedTemplateFormats()
    {
        try {
            $ret    = array();
            $result = $this->getSoapClient()->GetTemplateFormats();

            if (isset($result->GetTemplateFormatsResult->string)) {
                $ret = $result->GetTemplateFormatsResult->string;
                $ret = array_map('strtolower', $ret);
            }

            return $ret;
        } catch (Exceptions\SoapException $ex) {
            throw new Exceptions\TemplateException('Error while getting the list of accepted template formats', $ex);
        }
    }

    public function getAvailableDocumentFormats()
    {
        try {
            $ret    = array();
            $result = $this->getSoapClient()->GetDocumentFormats();

            if (isset($result->GetDocumentFormatsResult->string)) {
                $ret = $result->GetDocumentFormatsResult->string;
                $ret = array_map('strtolower', $ret);
            }

            return $ret;
        } catch (Exceptions\SoapException $ex) {
            throw new Exceptions\TemplateException('Error while getting the list of available document formats', $ex);
        }
    }

    /**
     * Get the list of all available fonts on the Livedocx service
     *
     * @return type
     *
     * @throws Exceptions\TemplateException
     */
    public function getAvailableFonts()
    {
        try {
            $ret    = array();
            $result = $this->getSoapClient()->GetFontNames();

            if (isset($result->GetFontNamesResult->string)) {
                $ret = $result->GetFontNamesResult->string;
            }

            return $ret;
        } catch (Exceptions\SoapException $ex) {
            throw new Exceptions\TemplateException('Error while getting the list of available fonts', $ex);
        }
    }

    public function ignoreSubTemplate($subtemplate)
    {
        try {
            return $this;
        } catch (Exceptions\SoapException $ex) {
            throw new Exceptions\TemplateException("Error while ignoring a subtemplate : '$subtemplate'", $ex);
        }
    }

    /**
     * Define a list of subtemplates to ignore when generating the final document
     *
     * @param array $subtemplates_list
     *
     * @return \Awakenweb\Livedocx\Template
     *
     * @throws Exceptions\TemplateException @see Template::ignoreSubTemplate
     */
    public function ignoreSubTemplates(array $subtemplates_list)
    {
        foreach ($subtemplates_list as $subtemplate) {
            $this->ignoreSubTemplate($subtemplate);
        }
        return $this;
    }

}
