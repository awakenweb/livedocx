<?php

namespace Awakenweb\Livedocx\tests\units;

require_once 'vendor/autoload.php';

use atoum;
use Awakenweb\Livedocx\Container as LDXContainer;

class Container extends atoum
{

    /**
     *
     * @dataProvider wrongParametersAssign
     */
    public function test_assign_throw_exception_when_wrong_parameters($fieldname, $value)
    {
        $container = new LDXContainer();

        $this->exception(function() use ($container, $fieldname, $value) {
                    $container->assign($fieldname, $value);
                })
                ->isInstanceOf('Awakenweb\Livedocx\Exceptions\ContainerException')
                ->hasMessage('Incorrect parameters for assignment');
    }

    public function test_assign_store_blocks()
    {
        $this->mockGenerator->orphanize('__construct');
        $this->mockGenerator->shuntParentClassCalls();
        $mockBlock = new \mock\Awakenweb\Livedocx\Block();
        $container = new LDXContainer();

        $container->assign($mockBlock);

        $this->array($container->getBlocks())
                ->contains($mockBlock);
    }

    public function test_assign_store_images()
    {
        $this->mockGenerator->orphanize('__construct');
        $this->mockGenerator->shuntParentClassCalls();
        $mockImage = new \mock\Awakenweb\Livedocx\Image();
        $container = new LDXContainer();

        $mockImage->getMockController()->getName = 'test';
        $container->assign('testField', $mockImage);

        $this->array($container->getImages())
                ->hasKey('image:testField')
                ->contains('test');
    }

    public function test_assign_store_arrays()
    {
        $container = new LDXContainer();

        $container->assign([
            'test'  => 'testValue',
            'test2' => 'testValue2'
        ]);

        $this->array($container->getFields())
                ->hasKeys(['test', 'test2'])
                ->containsValues(['testValue', 'testValue2']);
    }

    public function test_assign_store_singleValue()
    {
        $container = new LDXContainer();

        $container->assign('test', 'testValue');

        $this->array($container->getFields())
                ->hasKeys(['test'])
                ->containsValues(['testValue']);
    }

    /* = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = */
    /* = - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - = */
    /* = -                               DATA PROVIDERS                              - = */
    /* = - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - = */
    /* = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = */

    public function wrongParametersAssign()
    {
        return [
            ['random string', null],
            [null, null],
            [new \stdClass(), null],
            [array(), null],
        ];
    }

}
