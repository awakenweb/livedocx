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
 * Livedocx webservice API for PDF generation
 *
 * @author Mathieu SAVELLI <mathieu.savelli@awakenweb.fr>
 */
class Livedocx
{

    use Soap\HasSoapClient;

    /**
     *
     * @var string
     */
    protected $connected = false;

    /**
     *
     * @var string
     */
    protected $username;

    /**
     *
     * @var string
     */
    protected $password;

    /**
     *
     * @var array
     */
    protected $fields = [ ];

    /**
     *
     * @var array
     */
    protected $blocks = [ ];

    /**
     * Try to logout from the server before destruction of the object
     */
    public function __destruct()
    {
        $this->logOut();
    }

    /**
     * Set credentials for login
     *
     * @param type $username
     * @param type $password
     *
     * @return \Awakenweb\Livedocx\Livedocx
     */
    public function setCredentials($username , $password)
    {
        $this->username = $username;
        $this->password = $password;

        return $this;
    }

    /**
     * Connects to the livedocx service
     *
     * @param string $username
     * @param string $password
     *
     * @return \Awakenweb\Livedocx\Livedocx
     *
     * @throws Exceptions\LivedocxException
     */
    public function logIn($username = null , $password = null)
    {
        try {
            $this->getSoapClient()->logIn([
                'username' => $username ? : $this->username ,
                'password' => $password ? : $this->passowrd
            ]);
            $this->connected = true;

            return $this;
        } catch ( \SoapFault $e ) {
            throw new Exceptions\LivedocxException('Login or password is invalid' , $e);
        }
    }

    /**
     * End the connection to the Livedocx server
     *
     * @throws Exceptions\LivedocxException
     */
    public function logOut()
    {
        try {
            $this->getSoapClient()->logOut();
            $this->connected = false;
        } catch ( \SoapFault $e ) {
            throw new Exceptions\LivedocxException('Unable to log out of Livedocx' , $e);
        }
    }

    /**
     *
     * @param string $fieldname
     * @param string|integer|float $value
     */
    public function assign($fieldname , $value)
    {
        $this->fields[ $fieldname ] = $value;
    }

    public function addBlock(Block $block)
    {

    }

    /**
     * Set an associative or multi-associative array of keys and values pairs
     *
     * @param array $values
     *
     * @return Livedocx
     */
    protected function setFieldValues($values)
    {
        $this->logIn();

        foreach ( $values as $value ) {
            if ( is_array($value) ) {
                $method = 'multiAssocArrayToArrayOfArrayOfString';
            } else {
                $method = 'assocArrayToArrayOfArrayOfString';
            }
            break;
        }

        try {
            $client = $this->getSoapClient();
            $client->SetFieldValues(array(
                'fieldValues' => $client->$method($values) ,
            ));
        } catch ( Exceptions\SoapException $e ) {
            throw new Exceptions\LivedocxException('Unable to bind values to fields' , $e);
        }

        return $this;
    }

}
