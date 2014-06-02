<?php

namespace Awakenweb\Livedocx\tests\units;

require_once 'vendor/autoload.php';

use atoum;
use Awakenweb\Livedocx\Exceptions\SoapException;
use Awakenweb\Livedocx\Livedocx as LdxLivedocx;

class Livedocx extends atoum
{

    /**
     *
     */
    public function test_createDocument_return_Document()
    {
        $mocks = $this->scaffoldMock();
        $ldx   = new LdxLivedocx($mocks[ 'client' ] , $mocks[ 'container' ]);

        $doc = $ldx->createDocument();

        $this->object($doc)
                ->isInstanceOf('Awakenweb\Livedocx\Document')
                ->object($doc->getSoapClient())
                ->isIdenticalTo($mocks[ 'client' ]);
    }

    /**
     *
     */
    public function test_createImage_return_Image()
    {
        $mocks = $this->scaffoldMock();
        $ldx   = new LdxLivedocx($mocks[ 'client' ] , $mocks[ 'container' ]);

        $img = $ldx->createImage();

        $this->object($img)
                ->isInstanceOf('Awakenweb\Livedocx\Image')
                ->object($img->getSoapClient())
                ->isIdenticalTo($mocks[ 'client' ]);
    }

    /**
     *
     */
    public function test_createLocalTemplate_return_Local()
    {
        $mocks = $this->scaffoldMock();
        $ldx   = new LdxLivedocx($mocks[ 'client' ] , $mocks[ 'container' ]);

        $tpl = $ldx->createLocalTemplate();

        $this->object($tpl)
                ->isInstanceOf('Awakenweb\Livedocx\Templates\Local')
                ->object($tpl->getSoapClient())
                ->isIdenticalTo($mocks[ 'client' ]);
    }

    /**
     *
     */
    public function test_createRemoteTemplate_return_Remote()
    {
        $mocks = $this->scaffoldMock();
        $ldx   = new LdxLivedocx($mocks[ 'client' ] , $mocks[ 'container' ]);

        $tpl = $ldx->createRemoteTemplate();

        $this->object($tpl)
                ->isInstanceOf('Awakenweb\Livedocx\Templates\Remote')
                ->object($tpl->getSoapClient())
                ->isIdenticalTo($mocks[ 'client' ]);
    }

    /**
     *
     */
    public function test_createBlock_return_Block()
    {
        $mocks = $this->scaffoldMock();
        $ldx   = new LdxLivedocx($mocks[ 'client' ] , $mocks[ 'container' ]);

        $block = $ldx->createBlock();

        $this->object($block)
                ->isInstanceOf('Awakenweb\Livedocx\Block')
                ->object($block->getSoapClient())
                ->isIdenticalTo($mocks[ 'client' ]);
    }

    /**
     *
     */
    public function test_assign_call_Container_method()
    {
        $mocks = $this->scaffoldMock();
        $ldx   = new LdxLivedocx($mocks[ 'client' ] , $mocks[ 'container' ]);

        $mocks[ 'container' ]->getMockController()->assign = function() use ($mocks) {
            return $mocks[ 'container' ];
        };


        $this->when($ldx->assign('test' , 'value'))
                ->mock($mocks[ 'container' ])
                ->call('assign')
                ->once();
    }

    /**
     *
     */
    public function test_prepare_call_mocks_methods()
    {
        $mocks     = $this->scaffoldMock();
        $container = $mocks[ 'container' ];
        $client    = $mocks[ 'client' ];
        $ldx       = new LdxLivedocx($client , $container);
        $blc       = new \mock\Awakenweb\Livedocx\Block($client);
        $blc->setName('some Block');

        $container->getMockController()->getBlocks = [$blc ];
        $container->getMockController()->getFields = [ 'test' => 'value' ];
        $container->getMockController()->getImages = [ ['image:test' => 'value' ] , ['image:anothertest' => 'anothervalue' ] ];

        $client->getMockController()->SetBlockFieldValues = true;
        $client->getMockController()->SetFieldValues      = true;
        $this->when($ldx->prepare())
                ->mock($client)
                ->call('SetBlockFieldValues')->once()
                ->call('SetFieldValues')->once()
                ->mock($container)
                ->call('getBlocks')->once()
                ->call('getImages')->once()
                ->call('getFields')->once();
    }

    /**
     *
     */
    public function test_prepare_throw_exception_when_soap_error_occurs_in_values_assignation()
    {
        $mocks     = $this->scaffoldMock();
        $container = $mocks[ 'container' ];
        $client    = $mocks[ 'client' ];
        $ldx       = new LdxLivedocx($client , $container);

        $client->getMockController()->SetFieldValues = function() {
            throw new SoapException('random exception');
        };

        $this->exception(function() use ($ldx) {
                    $ldx->prepare();
                })
                ->isInstanceOf('Awakenweb\Livedocx\Exceptions\DeclarationException')
                ->hasMessage('Error while sending the fields/values binding to Livedocx service')
                ->hasNestedException();
    }

    /**
     *
     */
    public function test_prepare_throw_exception_when_soap_error_occurs_in_block_assignation()
    {
        $mocks     = $this->scaffoldMock();
        $container = $mocks[ 'container' ];
        $client    = $mocks[ 'client' ];
        $ldx       = new LdxLivedocx($client , $container);
        $blc       = new \mock\Awakenweb\Livedocx\Block($client);

        $client->getMockController()->SetBlockFieldValues = function() {
            throw new SoapException('random exception');
        };
        $client->getMockController()->SetFieldValues = true;
        $container->getMockController()->getBlocks   = [$blc ];
        $blc->getMockController()->getName           = 'test';

        $this->exception(function() use ($ldx) {
                    $ldx->prepare();
                })
                ->isInstanceOf('Awakenweb\Livedocx\Exceptions\DeclarationException')
                ->hasMessage('Error while sending blocks informations to Livedocx service (block: test)')
                ->hasNestedException();
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
        return [
            'client'    => new \mock\Awakenweb\Livedocx\Soap\Client() ,
            'container' => new \mock\Awakenweb\Livedocx\Container()
        ];
    }

}
