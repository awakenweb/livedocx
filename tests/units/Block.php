<?php

namespace Awakenweb\Livedocx\tests\units;

require_once '/vendor/autoload.php';

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
        $this->exception(function () use ($blockname , $block) {
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

        $this->exception(function () use ($block , $key) {
                    $block->bind($key , 'random value');
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

        $this->exception(function () use ($block , $value) {
                    $block->bind('randomKey' , $value);
                })
                ->isInstanceOf('Awakenweb\Livedocx\Exceptions\BlockException')
                ->hasMessage('Block binding value must be a non empty string or number');
    }

    public function test_block_getName_return_string()
    {
        $mock  = $this->scaffoldMock();
        $block = new LDXBlock($mock);

        $block->setName('randomBlockName');

        $this->string($block->getName())
                ->isEqualTo('randomBlockName');
    }

    public function test_block_retrieveValues_return_array()
    {
        $mock  = $this->scaffoldMock();
        $block = new LDXBlock($mock);
        $block->bind('test' , 'my test value')
                ->bind('thisIsATest' , 'another test value');

        $this->array($block->retrieveValues())
                ->hasKeys(['test' , 'thisIsATest' ])
                ->containsValues(['my test value' , 'another test value' ]);
    }

    public function test_getAllBlockNames_throw_exception_when_soap_error_occurs()
    {
        $mock = $this->scaffoldMock();

        $mock->getMockController()->GetBlockNames = function() {
            throw new SoapException('random exception');
        };

        $block = new LDXBlock($mock);

        $this->exception(function () use ($block) {
                    $block->getAllBlockNames();
                })
                ->isInstanceOf('Awakenweb\Livedocx\Exceptions\BlockException')
                ->hasMessage('Error while getting the list of all blocks in the active template')
                ->hasNestedException();
    }

    public function test_getAllBlockNames_return_empty_array_when_no_blocks()
    {
        $mock = $this->scaffoldMock();

        $mock->getMockController()->GetBlockNames = function() {
            return new stdClass();
        };

        $block = new LDXBlock($mock);

        $this->array($block->getAllBlockNames())
                ->isEmpty();
    }

    public function test_getAllBlockNames_return_array_when_an_array()
    {
        $mock = $this->scaffoldMock();

        $mock->getMockController()->GetBlockNames = function() {
            $ret                              = new stdClass();
            $ret->GetBlockNamesResult         = new stdClass();
            $ret->GetBlockNamesResult->string = ['value1' , 'value2' ];
            return $ret;
        };

        $block = new LDXBlock($mock);

        $this->array($block->getAllBlockNames())
                ->containsValues(['value1' , 'value2' ]);
    }

    public function test_getAllBlockNames_return_array_when_a_string()
    {
        $mock = $this->scaffoldMock();

        $mock->getMockController()->GetBlockNames = function() {
            $ret                              = new stdClass();
            $ret->GetBlockNamesResult         = new stdClass();
            $ret->GetBlockNamesResult->string = 'value1';
            return $ret;
        };

        $block = new LDXBlock($mock);

        $this->array($block->getAllBlockNames())
                ->containsValues(['value1' ]);
    }

    /* = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = */
    /* = - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - = */
    /* = -                               DATA PROVIDERS                              - = */
    /* = - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - = */
    /* = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = */

    public function fakeKeysForBlock()
    {
        return [
            ['' ] ,
            [ null ] ,
            [new stdClass() ] ,
            [1234567890 ] ,
            [123.123456 ] ,
            [array() ]
        ];
    }

    public function fakeValuesForBlock()
    {
        return [
            ['' ] ,
            [ null ] ,
            [new stdClass() ] ,
            [array() ]
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
