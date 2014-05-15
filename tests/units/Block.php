<?php

namespace Awakenweb\Livedocx\tests\units;

require_once 'vendor/autoload.php';

use atoum;
use Awakenweb\Livedocx\Block as LDXBlock;
use Awakenweb\Livedocx\Exceptions\SoapException;
use stdClass;

class Block extends atoum
{

    /**
     * @dataProvider fakeKeysForBlock
     */
    public function test_new_block_throws_exceptions_with_invalid_parameters($blockname)
    {
        $mock  = $this->scaffoldMock();
        $block = new LDXBlock($mock);
        $this->exception(function () use ($blockname, $block) {
                    $block->setName($blockname);
                })
                ->isInstanceOf('Awakenweb\Livedocx\Exceptions\BlockException')
                ->hasMessage('Block name must be a non empty string');
    }

    /**
     * @dataProvider fakeKeysForBlock
     */
    public function test_block_binding_throws_exceptions_when_invalid_keys($key)
    {
        $mock  = $this->scaffoldMock();
        $block = new LDXBlock($mock);

        $this->exception(function () use ($block, $key) {
                    $block->bind($key, 'random value');
                })
                ->isInstanceOf('Awakenweb\Livedocx\Exceptions\BlockException')
                ->hasMessage('Block binding key must be a non empty string');
    }

    /**
     * @dataProvider fakeValuesForBlock
     */
    public function test_block_binding_throws_exceptions_when_invalid_values($value)
    {
        $mock  = $this->scaffoldMock();
        $block = new LDXBlock($mock);

        $this->exception(function () use ($block, $value) {
                    $block->bind('randomKey', $value);
                })
                ->isInstanceOf('Awakenweb\Livedocx\Exceptions\BlockException')
                ->hasMessage('Block binding value must be a non empty string or number');
    }

    /**
     *
     */
    public function test_block_binding_is_called_recursively_if_parameter_is_array()
    {
        $mock  = $this->scaffoldMock();
        $block = new LDXBlock($mock);

        $this->when(function() use ($block) {
                    $block->bind([
                        'testKey'  => 'testValue',
                        'testKey2' => 'testValue2'
                    ]);
                })
                ->array($block->retrieveValues())
                ->containsValues([ 'testValue', 'testValue2'])
                ->hasKeys([ 'testKey', 'testKey2']);
    }

    /**
     *
     */
    public function test_block_getName_return_string()
    {
        $mock  = $this->scaffoldMock();
        $block = new LDXBlock($mock);

        $block->setName('randomBlockName');

        $this->string($block->getName())
                ->isEqualTo('randomBlockName');
    }

    /**
     *
     */
    public function test_block_retrieveValues_return_array()
    {
        $mock  = $this->scaffoldMock();
        $block = new LDXBlock($mock);
        $block->bind('test', 'my test value')
                ->bind('thisIsATest', 'another test value');

        $this->array($block->retrieveValues())
                ->hasKeys(['test', 'thisIsATest'])
                ->containsValues(['my test value', 'another test value']);
    }

    /**
     *
     */
    public function test_getAllBlockNames_throw_exception_when_soap_error_occurs()
    {
        $mock  = $this->scaffoldMock();
        $block = new LDXBlock($mock);

        $mock->getMockController()->GetBlockNames = function() {
            throw new SoapException('random exception');
        };
        $this->exception(function () use ($block) {
                    $block->getAllBlockNames();
                })
                ->isInstanceOf('Awakenweb\Livedocx\Exceptions\BlockException')
                ->hasMessage('Error while getting the list of all blocks in the active template')
                ->hasNestedException();
    }

    public function test_getAllBlockNames_return_empty_array_when_no_blocks()
    {
        $mock  = $this->scaffoldMock();
        $block = new LDXBlock($mock);

        $mock->getMockController()->GetBlockNames = function() {
            return new stdClass();
        };
        $this->array($block->getAllBlockNames())
                ->isEmpty();
    }

    public function test_getAllBlockNames_return_array_when_an_array()
    {
        $mock  = $this->scaffoldMock();
        $block = new LDXBlock($mock);

        $mock->getMockController()->GetBlockNames = function() {
            $ret                              = new stdClass();
            $ret->GetBlockNamesResult         = new stdClass();
            $ret->GetBlockNamesResult->string = ['value1', 'value2'];
            return $ret;
        };
        $this->array($block->getAllBlockNames())
                ->containsValues(['value1', 'value2']);
    }

    public function test_getAllBlockNames_return_array_when_a_string()
    {
        $mock  = $this->scaffoldMock();
        $block = new LDXBlock($mock);

        $mock->getMockController()->GetBlockNames = function() {
            $ret                              = new stdClass();
            $ret->GetBlockNamesResult         = new stdClass();
            $ret->GetBlockNamesResult->string = 'value1';
            return $ret;
        };
        $this->array($block->getAllBlockNames())
                ->containsValues(['value1']);
    }

    /**
     *
     */
    public function test_getFieldNames_throw_exception_when_soap_error_occurs()
    {
        $mock  = $this->scaffoldMock();
        $block = new LDXBlock($mock);

        $mock->getMockController()->GetBlockFieldNames = function() {
            throw new SoapException('random exception');
        };
        $this->exception(function () use ($block) {
                    $block->getFieldNames();
                })
                ->isInstanceOf('Awakenweb\Livedocx\Exceptions\BlockException')
                ->hasMessage('Error while getting the list of all fields in this block')
                ->hasNestedException();
    }

    /**
     *
     */
    public function test_getFieldNames_return_empty_array_when_no_fields()
    {
        $mock  = $this->scaffoldMock();
        $block = new LDXBlock($mock);

        $mock->getMockController()->GetBlockFieldNames = function() {
            return new stdClass();
        };
        $this->array($block->getFieldNames())
                ->isEmpty();
    }

    /**
     *
     */
    public function test_getFieldNames_return_array_when_an_array()
    {
        $mock  = $this->scaffoldMock();
        $block = new LDXBlock($mock);

        $mock->getMockController()->GetBlockFieldNames = function() {
            $ret                                   = new stdClass();
            $ret->GetBlockFieldNamesResult         = new stdClass();
            $ret->GetBlockFieldNamesResult->string = ['value1', 'value2'];
            return $ret;
        };
        $this->array($block->getFieldNames())
                ->containsValues(['value1', 'value2']);
    }

    /**
     *
     */
    public function test_getFieldNames_return_array_when_a_string()
    {
        $mock  = $this->scaffoldMock();
        $block = new LDXBlock($mock);

        $mock->getMockController()->GetBlockFieldNames = function() {
            $ret                                   = new stdClass();
            $ret->GetBlockFieldNamesResult         = new stdClass();
            $ret->GetBlockFieldNamesResult->string = 'value1';
            return $ret;
        };
        $this->array($block->getFieldNames())
                ->containsValues(['value1']);
    }

    /* = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = */
    /* = - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - = */
    /* = -                               DATA PROVIDERS                              - = */
    /* = - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - = */
    /* = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = */

    public function fakeKeysForBlock()
    {
        return [
            [''],
            [ null],
            [new stdClass()],
            [1234567890],
            [123.123456]
        ];
    }

    public function fakeValuesForBlock()
    {
        return [
            [''],
            [ null],
            [new stdClass()],
            [array()]
        ];
    }

    protected function scaffoldMock()
    {
        $this->mockGenerator->orphanize('__construct');
        $this->mockGenerator->shuntParentClassCalls();
        $mock = new \mock\Awakenweb\Livedocx\Soap\Client();

        return $mock;
    }

}
