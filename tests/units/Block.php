<?php

namespace Awakenweb\Livedocx\tests\units;

require_once '/vendor/autoload.php';

use Awakenweb\Livedocx\Block as LDXBlock;
use atoum;

class Block extends atoum
{

    /**
     * @dataProvider fakeKeysForBlock
     */
    public function test_new_block_throws_exceptions_with_invalid_parameters($blockname)
    {
        $this->exception(function () use ($blockname) {
                    new LDXBlock($blockname);
                })
                ->isInstanceOf('Awakenweb\Livedocx\Exceptions\BlockException')
                ->hasMessage('Block name must be a non empty string');
    }

    /**
     * @dataProvider fakeKeysForBlock
     */
    public function test_block_binding_throws_exceptions_when_invalid_keys($key)
    {
        $block = new LDXBlock('randomBlockName');

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
        $block = new LDXBlock('randomBlockName');

        $this->exception(function () use ($block , $value) {
                    $block->bind('randomKey' , $value);
                })
                ->isInstanceOf('Awakenweb\Livedocx\Exceptions\BlockException')
                ->hasMessage('Block binding value must be a non empty string or number');
    }

    public function test_block_getName_returns_array()
    {
        $block = new LDXBlock('randomBlockName');

        $this->string($block->getName())
                ->isEqualTo('randomBlockName');
    }

    public function test_block_retrieveValues_returns_array()
    {
        $block = new LDXBlock('randomBlockName');
        $block->bind('test' , 'my test value')
                ->bind('thisIsATest' , 'another test value');

        $this->array($block->retrieveValues())
                ->hasKeys(['test' , 'thisIsATest' ])
                ->containsValues(['my test value' , 'another test value' ]);
    }

    /* --- DATA PROVIDERS --- */

    public function fakeKeysForBlock()
    {
        return [
            ['' ] ,
            [ null ] ,
            [new \StdClass() ] ,
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
            [new \StdClass() ] ,
            [array() ]
        ];
    }

}
