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
class Document
{

    protected $data;
    protected $name;

    /**
     * Create a new instance of document containing data and a type
     *
     * @param string $name
     * @param string $data
     */
    public function __construct($name , $data)
    {
        if ( ! is_string($name) || $name === '' ) {
            throw new Exceptions\DocumentException('The name of the document must be a non empty string');
        }
        if ( ! is_string($data) || $data === '' ) {
            throw new Exceptions\DocumentException('The data of the document must be a non empty string');
        }
        $this->name = $name;
        $this->data = $data;
    }

    /**
     * Save the document to the destination
     *
     * @param string $destination_dir
     */
    public function save($destination_dir)
    {
        if ( is_null($destination_dir) || ! is_string($destination_dir) || $destination_dir === '' ) {
            throw new Exceptions\DocumentException('The destination directory of the document must be a non empty string');
        }
        if ( false === file_put_contents($destination_dir . $this->name , $this->data) ) {
            throw new Exceptions\DocumentException('An error has occured while saving the document');
        }
    }

}
