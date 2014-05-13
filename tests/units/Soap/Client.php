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

namespace Awakenweb\Livedocx\tests\units\Soap;

require_once '/vendor/autoload.php';

use atoum;
use Awakenweb\Livedocx\Soap\Client as LdxClient;
use SoapFault;
use stdClass;

/**
 * Description of Client
 *
 * @author Mathieu SAVELLI <mathieu.savelli@awakenweb.fr>
 */
class Client extends atoum
{

    public function test_proxy()
    {
        $mock = $this->scaffoldMock();

        $mock->getMockController()->testMethod = function() {
            return true;
        };

        $ldxclient = new LdxClient($mock);

        $this->boolean($ldxclient->testMethod())
                ->mock($mock)->call('testMethod')->once();
    }

    public function test_proxy_method_throw_exception()
    {
        $mock = $this->scaffoldMock();

        $mock->getMockController()->testMethod = function() {
            throw new SoapFault('test', 'test exception');
        };

        $ldxclient = new LdxClient($mock);

        $this->exception(function() use($ldxclient) {
                    $ldxclient->testMethod();
                })
                ->isInstanceOf('Awakenweb\Livedocx\Exceptions\SoapException')
                ->hasMessage('Error while querying the SOAP server');
    }

    public function test_convertArray_return_an_array_of_array_of_strings_when_given_flat_array()
    {
        $mock      = $this->scaffoldMock();
        $ldxclient = new LdxClient($mock);

        $result = $ldxclient->convertArray([ 'firstKey' => 'firstValue', 'secondKey' => 'secondValue']);

        $this->array($result)
                ->array($result[0])
                ->containsValues([ 'firstKey', 'secondKey'])
                ->array($result[1])
                ->containsValues([ 'firstValue', 'secondValue']);
    }

    public function test_convertArray_return_an_array_of_array_of_strings_when_given_multi_array()
    {
        $mock      = $this->scaffoldMock();
        $ldxclient = new LdxClient($mock);

        $result = $ldxclient->convertArray([
            ['firstKey' => 'firstValue1', 'secondKey' => 'secondValue1', 'thirdKey' => 'thirdValue1'],
            ['firstKey' => 'firstValue2', 'secondKey' => 'secondValue2', 'thirdKey' => 'thirdValue2'],
            ['firstKey' => 'firstValue3', 'secondKey' => 'secondValue3', 'thirdKey' => 'thirdValue3']
        ]);

        $this->array($result)
                ->array($result[0])
                ->strictlyContainsValues([ 'firstKey', 'secondKey', 'thirdKey'])
                ->array($result[1])
                ->strictlyContainsValues([ 'firstValue1', 'secondValue1', 'thirdValue1'])
                ->array($result[2])
                ->strictlyContainsValues([ 'firstValue2', 'secondValue2', 'thirdValue2'])
                ->array($result[3])
                ->strictlyContainsValues([ 'firstValue3', 'secondValue3', 'thirdValue3']);
    }

    public function test_backendListArrayToMultiAssocArray_return_empty_array_when_empty_parameter()
    {
        $mock   = $this->scaffoldMock();
        $client = new LdxClient($mock);

        $this->array($client->backendListArrayToMultiAssocArray(new stdClass()))
                ->isEmpty();
    }

    public function test_backendListArrayToMultiAssocArray_return_array_when_multiple_String_values()
    {
        $mock   = $this->scaffoldMock();
        $client = new LdxClient($mock);
        $test   = new stdClass();

        $test->ArrayOfString = [];

        for ($i = 0; $i < 10; $i ++) {
            $newclass         = new stdClass();
            $newclass->string = [
                'testValue',
                date(DATE_RFC1123, 1),
                (string) 123456,
                date(DATE_RFC1123, 1),
            ];

            $test->ArrayOfString[] = $newclass;
        }

        $returnValue = $client->backendListArrayToMultiAssocArray($test);
        $this->array($returnValue)
                ->size
                ->isEqualTo(10);

        foreach ($returnValue as $value) {
            $this->array($value)
                    ->hasKeys([ 'filename', 'fileSize', 'createTime', 'modifyTime'])
                    ->containsValues([ 'testValue', 1, 123456]);
        }
    }

    public function test_backendListArrayToMultiAssocArray_return_array_when_only_one_String_value()
    {
        $mock   = $this->scaffoldMock();
        $client = new LdxClient($mock);
        $test   = new stdClass();

        $test->ArrayOfString = [
            [
                'testValue',
                date(DATE_RFC1123, 1),
                (string) 123456,
                date(DATE_RFC1123, 1),
            ]
        ];

        $returnValue = $client->backendListArrayToMultiAssocArray($test);
        $this->array($returnValue)
                ->size
                ->isEqualTo(1);

        $this->array($returnValue[0])
                ->hasKeys([ 'filename', 'fileSize', 'createTime', 'modifyTime'])
                ->containsValues([ 'testValue', 1, 123456]);
    }

    /* = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = */
    /* = - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - = */
    /* = -                               DATA PROVIDERS                              - = */
    /* = - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - = */
    /* = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = */

    protected function scaffoldMock()
    {
        $this->mockGenerator->orphanize('__construct');
        $this->mockGenerator->shuntParentClassCalls();
        $mock = new \mock\SoapClient('');

        return $mock;
    }

}
