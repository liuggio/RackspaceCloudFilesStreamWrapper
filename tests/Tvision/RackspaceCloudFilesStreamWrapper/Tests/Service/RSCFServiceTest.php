<?php

namespace Tvision\RackspaceCloudFilesStreamWrapper\Tests\Service;

use Tvision\RackspaceCloudFilesStreamWrapper\Model\RackspaceCloudFilesResource;

/**
 * @author liuggio
 */
class RSCFServiceTest extends \PHPUnit_Framework_TestCase
{

    public function getMockService($function = null)
    {
        $service = $this->getMockBuilder('\\Tvision\\RackspaceCloudFilesStreamWrapper\\Service\\RSCFService')
            ->disableOriginalConstructor()
            ->setMethods($function)
            ->getMock();
        return $service;
    }


    public function testApiGetContainer() {
        //we want to asser that the get_container api is called

        $container = $this->getMock('\OpenCloud\ObjectStore\Container',array('Name'), array(),'', false);
        $container->expects($this->any())
            ->method('Name')
            ->will($this->returnValue('container-name'));

        $rackspaceService = $this->getMockBuilder("Tvision\RackspaceCloudFilesStreamWrapper\Service\RackspaceApi")
            ->disableOriginalConstructor()
            ->getMock();
        $rackspaceService->expects($this->once())
            ->method('getContainer')
            ->will($this->returnValue($container));

        $service = $this->getMockService(array('getRackspaceService'));

        $service->expects($this->any())
            ->method('getRackspaceService')
            ->will($this->returnValue($rackspaceService));

        $ret = $service->apiGetContainer('container-name');

        $this->assertEquals($ret, $container);
        $this->assertEquals($ret->Name(), $container->Name());
    }


    public function testApiGetObjectByContainer() {
        //we want to assert that the create_object api is called

        $obj = $this->getMock('\OpenCloud\ObjectStore\Resource\DataObject',array(), array(),'', false);

        $container = $this->getMock('\OpenCloud\ObjectStore\Resource\Container',array('Name','DataObject'), array(),'', false);
        $container->expects($this->any())
            ->method('Name')
            ->will($this->returnValue('container-name'));
        $container->expects($this->any())
            ->method('DataObject')
            ->will($this->returnValue($obj));

        $service = $this->getMockService();

        $ret = $service->apiGetObjectByContainer($container, array('name' => 'test-object', 'content_type' => 'image/gif'));

        $this->assertEquals($ret, $obj);
    }
    

    public function testCreateResourceFromPath() {
        //we want to test that the file is unlinked
        $resourceName = 'js_75a9295_bootstrap-modal_3.js';
        $resourceContainerName = 'liuggio_assetic';
        $path = 'rscf://' . $resourceContainerName . '/' . $resourceName;

        $object = $this->getMock('\OpenCloud\ObjectStore\Resource\DataObject',array(), array(),'', false);
        $container = $this->getMock('\OpenCloud\ObjectStore\Resource\Container',array(), array(),'', false);


        $resource = new RackspaceCloudFilesResource();
        $resource->setResourceName($resourceName);
        $resource->setContainerName($resourceContainerName);
        $resource->setObject($object);
        $resource->setContainer($container);
        $resource->setCurrentPath($path);

        $rackspaceApi = $this->getMockBuilder("Tvision\RackspaceCloudFilesStreamWrapper\Service\RackspaceApi")
            ->disableOriginalConstructor()
            ->getMock();
        $rackspaceApi->expects($this->once())
            ->method('getContainer')
            ->will($this->returnValue($container));

        $service = $this->getMockService(
            array(
                'getResourceClass',
                'apiGetContainer',
                'apiGetObjectByContainer',
                'getRackspaceService',
                'getContainerByResource',
                'getObjectByResource',
                'guessFileType'));

        $service->expects($this->any())
            ->method('getResourceClass')
            ->will($this->returnValue('\\Tvision\\RackspaceCloudFilesStreamWrapper\\Model\\RackspaceCloudFilesResource'));
        $service->expects($this->any())
            ->method('apiGetContainer')
            ->will($this->returnValue($container));
        $service->expects($this->any())
            ->method('getRackspaceService')
            ->will($this->returnValue($rackspaceApi));
        $service->expects($this->any())
            ->method('apiGetObjectByContainer')
            ->will($this->returnValue($object));


        $ret = $service->createResourceFromPath($path);

        //asserting
        $this->assertEquals($ret, $resource);
    }
    
}

 
