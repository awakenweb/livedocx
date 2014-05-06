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

use Awakenweb\Livedocx\Exceptions\SoapException;
use Awakenweb\Livedocx\Exceptions\TemplateException;

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

    /**
     * Set the filename of the template
     *
     * @param string $template_name
     */
    public function setName($template_name)
    {
        $this->templateName = $template_name;
    }

    /**
     * Return a list of all the accepted template formats you can use to generate your document
     *
     * @return array
     *
     * @throws TemplateException
     */
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
        } catch (SoapException $ex) {
            throw new TemplateException('Error while getting the list of accepted template formats', $ex);
        }
    }

    /**
     * Return a list of all available return formats you can ask for when generating the
     * document
     *
     * @return array
     *
     * @throws TemplateException
     */
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
        } catch (SoapException $ex) {
            throw new TemplateException('Error while getting the list of available document formats', $ex);
        }
    }

    /**
     * Get the list of all available fonts on the Livedocx service
     *
     * @return type
     *
     * @throws TemplateException
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
        } catch (SoapException $ex) {
            throw new TemplateException('Error while getting the list of available fonts', $ex);
        }
    }

    /**
     * Tell the Livedocx service to ignore included subtemplates when generating the final
     * document
     *
     * @param boolean $state
     *
     * @return Template
     *
     * @throws TemplateException
     */
    public function ignoreSubTemplates($state = true)
    {
        if (!is_bool($state)) {
            throw new TemplateException('ignoreSubTemplates expects its parameter to be a boolean');
        }
        try {
            $this->getSoapClient()->SetIgnoreSubTemplates(array(
                'ignoreSubTemplates' => $state,
            ));
        } catch (SoapException $ex) {
            throw new TemplateException("Error while telling the server to ignore subtemplates", $ex);
        }
    }

    /**
     * Define a list of subtemplates to ignore when generating the final document
     *
     * @param array $subtemplates_list
     *
     * @return Template
     *
     * @throws TemplateException
     */
    public function ignoreListOfSubTemplates(array $subtemplates_list)
    {
        if (!is_array($subtemplates_list)) {
            throw new TemplateException('filenames must be an array.');
        }
        $filenames = array_values($subtemplates_list);
        try {
            $this->getSoapClient()->SetSubTemplateIgnoreList(array(
                'filenames' => $filenames,
            ));
        } catch (SoapException $ex) {
            throw new TemplateException("Error while telling the server to ignore a list of subtemplates", $ex);
        }
    }

    protected abstract function getName();

    public abstract function setAsActive();
}
