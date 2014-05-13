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

use Awakenweb\Livedocx\Exceptions\LivedocxException;
use Awakenweb\Livedocx\Exceptions\SoapException;
use Awakenweb\Livedocx\Templates\Local as Local;
use Awakenweb\Livedocx\Templates\Remote as Remote;

/**
 * Main class of the project. This class actually acts as a factory for other class
 * and connects them together
 */
class Livedocx
{

    /**
     *
     * @var Container
     */
    protected $container;

    use Soap\HasSoapClient;

    /**
     * Create a new instance of Livedocx by providing an instance of Soap\Client and
     * a values Container
     *
     * @param \Awakenweb\Livedocx\Soap\Client $client
     * @param \Awakenweb\Livedocx\Container $container
     */
    public function __construct(Soap\Client $client, Container $container)
    {
        $this->soapClient = $client;
        $this->container  = $container;
    }

    /**
     * Factory method for Document
     *
     * @return Document
     */
    public function createDocument()
    {
        return new Document($this->getSoapClient());
    }

    /**
     * Factory method for Image
     *
     * @return Image
     */
    public function createImage()
    {
        return new Image($this->getSoapClient());
    }

    /**
     * Factory method for Block
     *
     * @return Block
     */
    public function createBlock()
    {
        return new Block($this->getSoapClient());
    }

    /**
     * Factory method for Remote templates
     *
     * @return Remote
     */
    public function createRemoteTemplate()
    {
        return new Remote($this->getSoapClient());
    }

    /**
     * Factory method for Local templates
     *
     * @return Local
     */
    public function createLocalTemplate()
    {
        return new Local($this->getSoapClient());
    }

    /**
     *
     * @param mixed $key
     *
     * @param null|string $value
     *
     * @return \Awakenweb\Livedocx\Livedocx
     */
    public function assign($key, $value = null)
    {
        $this->container->assign($key, $value);

        return $this;
    }

    /**
     * Prepare the merging of the fields and return a document
     *
     * @param Container $container
     *
     * @return Document
     */
    public function prepare()
    {

        $blocks = $this->container->getBlocks();
        $fields = array_merge($this->container->getFields(), $this->container->getImages());

        $this->declareListOfBlocks($blocks)
                ->declareListOfValues($fields);

        return $this->createDocument();
    }

    /**
     * Send the list of all block values to Livedocx service to prepare the merging
     *
     * @param array $blocks
     *
     * @return Livedocx
     *
     * @throws LivedocxException
     */
    protected function declareListOfBlocks($blocks)
    {
        foreach ($blocks as $block) {
            try {
                $this->getSoapClient()->SetBlockFieldValues(array(
                    'blockName'        => $block->getName(),
                    'blockFieldValues' => $this->getSoapClient()->convertArray($block->retrieveValues())
                ));
            } catch (SoapException $ex) {
                $s = '';
                if (!is_null($block->getName())) {
                    $s.= " (block: {$block->getName()})";
                }
                throw new LivedocxException("Error while sending blocks informations to Livedocx service" . $s, $ex);
            }
        }
        return $this;
    }

    /**
     *  Send the list of all field values to Livedocx service to prepare the merging
     *
     * @param array $fields
     *
     * @return \Awakenweb\Livedocx\Livedocx
     *
     * @throws LivedocxException
     */
    protected function declareListOfValues($fields)
    {
        try {
            $this->getSoapClient()->SetFieldValues(array(
                'fieldValues' => $this->getSoapClient()->convertArray($fields)
            ));
        } catch (SoapException $e) {
            throw new LivedocxException('Error while sending the fields/values binding to Livedocx service', $e);
        }

        return $this;
    }

}
