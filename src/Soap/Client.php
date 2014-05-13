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

namespace Awakenweb\Livedocx\Soap;

use Awakenweb\Livedocx\Exceptions\SoapException,
    SoapClient,
    SoapFault,
    DateTime,
    StdClass;

/**
 * Abstraction class for ext-soap SoapClient class
 *
 * @author Mathieu SAVELLI <mathieu.savelli@awakenweb.fr>
 */
class Client
{

    /**
     * @param SoapClient $client
     */
    protected $client;

    /**
     * Create a new abstraction instance of the SoapClient
     *
     * @param SoapClient $client
     */
    public function __construct(SoapClient $client)
    {
        $this->client = $client;
    }

    /**
     * Proxy method for the SoapClient class
     *
     * @param string $methodname
     * @param array  $args
     *
     * @return mixed
     *
     */
    public function __call($methodname, $args)
    {

        try {
            return call_user_func_array([ $this->client, $methodname], $args);
        } catch (SoapFault $ex) {
            throw new SoapException('Error while querying the SOAP server', $ex);
        }
    }

    /**
     * Convert a standard PHP array to a Livedocx compatible array
     *
     * @param array $array
     *
     * @return array
     */
    public function convertArray($array)
    {
        if ($this->isArrayMulti($array)) {
            return $this->multiAssocArrayToArrayOfArrayOfString($array);
        }
        return $this->assocArrayToArrayOfArrayOfString($array);
    }

    /**
     *
     * @param array $array
     *
     * @return boolean
     */
    protected function isArrayMulti($array)
    {
        foreach ($array as $value) {
            if (is_array($value))
                return true;
        }
        return false;
    }

    /**
     * Convert an associative PHP array to an array of array of strings
     *
     * @param array $assoc
     *
     * @return array
     */
    protected function assocArrayToArrayOfArrayOfString($assoc)
    {
        $arrayKeys   = array_keys($assoc);
        $arrayValues = array_values($assoc);

        return array($arrayKeys, $arrayValues);
    }

    /**
     * Convert a multidimensional PHP array to an array of array of arrays of
     * strings
     *
     * @param array $multi
     *
     * @return array
     */
    protected function multiAssocArrayToArrayOfArrayOfString($multi)
    {
        $arrayKeys   = array_keys($multi[0]);
        $arrayValues = array();

        foreach ($multi as $v) {
            $arrayValues[] = array_values($v);
        }

        $_arrayKeys    = array();
        $_arrayKeys[0] = $arrayKeys;

        return array_merge($_arrayKeys, $arrayValues);
    }

    /**
     * Convert LiveDocx service return value from list methods to consistent
     * PHP array.
     *
     * @param stdClass $list
     *
     * @return array
     */
    public function backendListArrayToMultiAssocArray(stdClass $list)
    {
        $ret = array();

        if (isset($list->ArrayOfString)) {
            foreach ($list->ArrayOfString as $a) {
                if (is_array($a)) {      // 1 template only
                    $o         = new StdClass();
                    $o->string = $a;
                } else {                 // 2 or more templates
                    $o = $a;
                }
                unset($a);

                if (isset($o->string)) {
                    $date1 = DateTime::createFromFormat(DateTime::RFC1123, $o->string[3]);
                    $date2 = DateTime::createFromFormat(DateTime::RFC1123, $o->string[1]);
                    $ret[] = array(
                        'filename'   => $o->string[0],
                        'fileSize'   => (integer) $o->string[2],
                        'createTime' => (integer) $date1->getTimestamp(),
                        'modifyTime' => (integer) $date2->getTimestamp(),
                    );
                    unset($date1, $date2);
                }
            }
        }

        return $ret;
    }

}
