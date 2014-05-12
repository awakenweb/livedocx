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

use Awakenweb\Livedocx\Exceptions\ServiceException;

class Service
{

    /**
     *
     * @var array
     */
    protected $fields = [];

    /**
     *
     * @var array
     */
    protected $blocks = [];

    /**
     *
     * @var array
     */
    protected $images = [];

    /**
     * Register a set of values to be merged within the template.
     * You can either pass a Block, an array of values or a single value
     *
     * @param string|array|Block $fieldname
     * @param string|Image       $value
     *
     * @return Service
     *
     * @throws ServiceException
     */
    public function assign($fieldname, $value = null)
    {
        switch ($fieldname) {
            case $fieldname instanceOf Block:
                $this->blocks[] = $fieldname;
                break;
            case is_array($fieldname): // bulk fields assignment
                foreach ($fieldname as $field => $val) {
                    $this->fields[$field] = $val;
                }
                break;
            case is_string($fieldname) && $value instanceOf Image:
                $this->images['image:' . $fieldname] = $value;
                break;
            case is_string($fieldname && !is_null($value)):
                $this->fields[$fieldname]          = $value;
            default:
                throw new ServiceException('Incorrect parameters for assignment');
        }

        return $this;
    }

    /**
     *
     * @return array
     */
    public function getFields()
    {
        return $this->fields;
    }

    /**
     *
     * @return array
     */
    public function getBlocks()
    {
        return $this->blocks;
    }

    /**
     *
     * @return array
     */
    public function getImages()
    {
        return $this->images;
    }

}
