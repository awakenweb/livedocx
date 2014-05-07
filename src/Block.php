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

use Awakenweb\Livedocx\Exceptions\BlockException;

/**
 * @author Mathieu SAVELLI <mathieu.savelli@awakenweb.fr>
 */
class Block
{

    use Soap\HasSoapClient;

    /**
     *
     * @var string
     */
    protected $block_name;

    /**
     *
     * @var array
     */
    protected $bindings = [ ];

    /**
     * set the name of the block
     *
     * @param string $block_name
     *
     * @throws BlockException
     */
    public function setName($block_name)
    {
        if ( ! is_string($block_name) || $block_name === '' ) {
            throw new BlockException('Block name must be a non empty string');
        }
        $this->block_name = $block_name;
    }

    /**
     * Bind a value to a block key
     *
     * @param string               $key
     * @param string|integer|float $value
     *
     * @return Block
     *
     * @throws BlockException
     */
    public function bind($key , $value)
    {
        if ( is_null($key) || ! is_string($key) || $key === '' ) {
            throw new BlockException('Block binding key must be a non empty string');
        }

        if ( is_null($value) || ( ! is_string($value) && ! is_numeric($value)) || $value === '' ) {
            throw new BlockException('Block binding value must be a non empty string or number');
        }

        $this->bindings[ $key ] = $value;

        return $this;
    }

    /**
     * Return the complete bindings set for this block
     *
     * @return array
     */
    public function retrieveValues()
    {
        return $this->bindings;
    }

    /**
     *
     * @return string
     */
    public function getName()
    {
        return $this->block_name;
    }

    /**
     * Return the names of all blocks included in the active template
     *
     * @return array
     */
    public function getAllBlockNames()
    {
        $ret = [ ];
        try {
            $result = $this->getSoapClient()->GetBlockNames();
        } catch ( \Awakenweb\Livedocx\Exceptions\SoapException $ex ) {
            throw new BlockException('Error while getting the list of all blocks in the active template' , $ex);
        }
        if ( isset($result->GetBlockNamesResult->string) ) {
            if ( is_array($result->GetBlockNamesResult->string) ) {
                $ret = $result->GetBlockNamesResult->string;
            } else {
                $ret[] = $result->GetBlockNamesResult->string;
            }
        }

        return $ret;
    }

    /**
     * Return the list of all fields contained in this block inside the active template
     *
     * @return type
     */
    public function getFieldNames()
    {
        $ret = [ ];
        try {
            $result = $this->getSoapClient()->GetBlockFieldNames([
                'blockName' => $this->getName() ,
            ]);
        } catch ( \Awakenweb\Livedocx\Exceptions\SoapException $ex ) {
            throw new BlockException('Error while getting the list of all fields in this block' , $ex);
        }

        if ( isset($result->GetBlockFieldNamesResult->string) ) {
            if ( is_array($result->GetBlockFieldNamesResult->string) ) {
                $ret = $result->GetBlockFieldNamesResult->string;
            } else {
                $ret[] = $result->GetBlockFieldNamesResult->string;
            }
        }

        return $ret;
    }

}
