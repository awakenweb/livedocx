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
 * @author Mathieu SAVELLI <mathieu.savelli@awakenweb.fr>
 */
class Block
{

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
     * Create a new named block
     *
     * @param string $block_name
     *
     * @throws Exceptions\BlockException
     */
    public function __construct($block_name)
    {
        if ( ! is_string($block_name) || $block_name === '' ) {
            throw new Exceptions\BlockException('Block name must be a non empty string');
        }
        $this->block_name = $block_name;
    }

    /**
     * Bind a value to a block key
     *
     * @param string $key
     * @param string $value
     *
     * @return \Awakenweb\Livedocx\Block
     *
     * @throws Exceptions\BlockException
     */
    public function bind($key , $value)
    {
        if ( is_null($key) || ! is_string($key) || $key === '' ) {
            throw new Exceptions\BlockException('Block binding key must be a non empty string');
        }

        if ( is_null($value) || ! is_string($value) || $value === '' ) {
            throw new Exceptions\BlockException('Block binding value must a string');
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

}
