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

use Awakenweb\Livedocx\Exceptions\Block\InvalidException;
use Awakenweb\Livedocx\Exceptions\Block\NameException;
use Awakenweb\Livedocx\Exceptions\Block\StatusException;
use Awakenweb\Livedocx\Exceptions\SoapException;

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
     * @throws NameException
     *
     * @api
     */
    public function setName($block_name)
    {
        if ( ! is_string($block_name) || $block_name === '' ) {
            throw new NameException('Block name must be a non empty string');
        }
        $this->block_name = $block_name;
    }

    /**
     * Bind a set of values to a block fieldname
     *
     * @param array          $values
     *
     * @return Block
     *
     * @throws InvalidException
     *
     * @api
     */
    public function bind($values)
    {
        if ( ! is_array($values) ) {
            throw new InvalidException('Values assigned to a block must be an array');
        }

        // only one value
        if ( ! $this->isArrayMulti($values) ) {
            $this->bindings[] = $values;
            return $this;
        }

        // multiple values
        foreach ( $values as $line ) {
            $this->bind($line);
        }

        return $this;
    }

    /**
     * Return the complete bindings set for this block
     *
     * @return array
     *
     * @internal
     */
    public function retrieveValues()
    {
        return $this->bindings;
    }

    /**
     *
     * @return string
     *
     * @api
     */
    public function getName()
    {
        if ( is_null($this->block_name) ) {
            throw new NameException('The name of the block has not been set');
        }
        return $this->block_name;
    }

    /**
     * Return the names of all blocks included in the active template
     *
     * @return array
     *
     * @throws StatusException
     *
     * @api
     */
    public function getAllBlockNames()
    {
        $ret = [ ];
        try {
            $result = $this->getSoapClient()->GetBlockNames();
        } catch ( SoapException $ex ) {
            throw new StatusException('Error while getting the list of all blocks in the active template' , $ex);
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
     *  Return the list of all fields contained in this block inside the active template
     *
     * @return array
     *
     * @throws StatusException
     * @throws NameException @see Block::getName
     *
     * @api
     */
    public function getFieldNames()
    {
        $ret = [ ];
        try {
            $result = $this->getSoapClient()->GetBlockFieldNames([
                'blockName' => $this->getName() ,
            ]);
        } catch ( SoapException $ex ) {
            throw new StatusException('Error while getting the list of all fields in this block' , $ex);
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

    /**
     *
     * @param array $array
     *
     * @return boolean
     *
     * @internal
     */
    protected function isArrayMulti($array)
    {
        foreach ( $array as $value ) {
            if ( is_array($value) ) {
                return true;
            }
        }
        return false;
    }

}
