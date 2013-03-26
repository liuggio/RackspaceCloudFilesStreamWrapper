<?php

namespace Liuggio\RackspaceCloudFilesStreamWrapper\Tests\StreamWrapper;

use \Liuggio\RackspaceCloudFilesStreamWrapper\StreamWrapper\RackspaceCloudFilesStreamWrapper;
use \Liuggio\RackspaceCloudFilesStreamWrapper\RackspaceCloudFilesResource;




class RackspaceCloudFilesStreamWrapperTest extends \PHPUnit_Framework_TestCase
{
    public function testRegisterStreamWrapperClass() {
        //replace built-in function
        $phpunit = $this;
        $protocolName = 'rscf-test';
        $classNameToAssert = 'Liuggio\RackspaceCloudFilesStreamWrapper\StreamWrapper\RackspaceCloudFilesStreamWrapper';
        $new_stream_wrapper_register = function ($protocol, $classname, $flags = null) use ($phpunit, $protocolName, $classNameToAssert) {
            $phpunit->assertEquals($protocol, $protocolName);
            $phpunit->assertEquals($classname, $classNameToAssert);
            return true;
        };
        $old_swf = RackspaceCloudFilesStreamWrapper::getStreamWrapperRegisterFunction();
        RackspaceCloudFilesStreamWrapper::setStreamWrapperRegisterFunction($new_stream_wrapper_register);
        //call
        RackspaceCloudFilesStreamWrapper::registerStreamWrapperClass($protocolName);
        //cleaning
        RackspaceCloudFilesStreamWrapper::setStreamWrapperRegisterFunction($old_swf);
    }


    public function testUnregisterStreamWrapperClass() {
        //replace built-in function
        $phpunit = $this;
        $protocolName = 'rscf-test';

        $new_stream_wrapper_unregister = function ($protocol) use ($phpunit, $protocolName) {
            $phpunit->assertEquals($protocol, $protocolName);
            return true;
        };

        $old_swf = RackspaceCloudFilesStreamWrapper::getStreamWrapperUnregisterFunction();
        $old_protocolName = RackspaceCloudFilesStreamWrapper::$protocolName;
        //inject the function
        RackspaceCloudFilesStreamWrapper::setStreamWrapperUnregisterFunction($new_stream_wrapper_unregister);
        RackspaceCloudFilesStreamWrapper::$protocolName = $protocolName;
        //call
        RackspaceCloudFilesStreamWrapper::unregisterStreamWrapperClass();
        //cleaning
        RackspaceCloudFilesStreamWrapper::setStreamWrapperUnregisterFunction($old_swf);
        RackspaceCloudFilesStreamWrapper::$protocolName = $old_protocolName;
    }


    public function getStreamWrapperClass()
    {
        return '\\Liuggio\\RackspaceCloudFilesStreamWrapper\\StreamWrapper\\RackspaceCloudFilesStreamWrapper';
    }

    public function getRSCFServiceClass()
    {
        return '\\Liuggio\\RackspaceCloudFilesStreamWrapper\\RSCFService';
    }

     private function generateMockService($functions)
     {
         $obj = $this->getMockBuilder($this->getRSCFServiceClass())
                 ->setConstructorArgs(array())
                 ->setMethods($functions)
                 ->getMock();
         return $obj;
     }

    public function testInitFromPath()
    {
        //setting resource
        $resourceName = 'js_75a9295_bootstrap-modal_3.js';
        $resourceContainerName = 'liuggio_assetic';
        $path = 'rscf://' . $resourceContainerName . '/' . $resourceName;

        $resource = new RackspaceCloudFilesResource();
        $resource->setResourceName($resourceName);
        $resource->setContainerName($resourceContainerName);

        //mocking service
        $service =  $this->generateMockService(array('createResourceFromPath'));
        $service->expects($this->any())
            ->method('createResourceFromPath')
            ->will($this->returnValue($resource));

        //mocking sw
        $streamWrapper = $this->getMock($this->getStreamWrapperClass(), array('getService'));
        $streamWrapper->expects($this->any())
            ->method('getService')
            ->will($this->returnValue($service));
        // the call
        $ret = $streamWrapper->initFromPath($path);

        $this->assertTrue($ret !== false);
        // asserting Resource
        $this->assertEquals($streamWrapper->getResource(), $resource);
    }

    public function testReset()
    {
        $a = $this->getStreamWrapperClass();
        $a = new $a();
        $a->setOnWriteDataMode('true');
        $a->appendDataBuffer('dataaaa');

        $a->reset();
        $this->assertNull($a->getDataBuffer());
        $this->assertFalse($a->getOnWriteDataMode());
    }

    public function testUnlink()
    {
        //we want to test that the file is unlinked
        $resourceName = 'js_75a9295_bootstrap-modal_3.js';
        $resourceContainerName = 'liuggio_assetic';
        $path = 'rscf://' . $resourceContainerName . '/' . $resourceName;
        // assert that delete_object is called with the correct name

        $mockedCollection = $this->getMock('Collection',array('Size','First'));
        $phpunit = $this;
        $container = $this->getMock("\StdClass", array('ObjectList'));
        $container->expects($this->once())
            ->method('ObjectList')
            ->will($this->returnCallback(function ($filter) use ($phpunit, $resourceName, $mockedCollection) {

                $phpunit->assertArrayHasKey('limit',$filter);
                $phpunit->assertArrayHasKey('prefix', $filter);

                $phpunit->assertEquals($resourceName, $filter['prefix']);

                $mockedCollection->expects($phpunit->once())
                    ->method('Size')
                    ->will($phpunit->returnValue(1));

                $mockedObject = $phpunit->getMock('Object',array('Delete'));
                $mockedObject->expects($phpunit->once())
                    ->method('Delete');

                $mockedCollection->expects($phpunit->once())
                    ->method('First')
                    ->will($phpunit->returnValue($mockedObject));

                return $mockedCollection;
        }));


        $resource = new RackspaceCloudFilesResource();
        $resource->setResourceName($resourceName);
        $resource->setContainerName($resourceContainerName);
        $resource->setContainer($container);

        //mocking sw
        $streamWrapper = $this->getMock($this->getStreamWrapperClass(), array('getResource','initFromPath'));
        $streamWrapper->expects($this->any())
            ->method('getResource')
            ->will($this->returnValue($resource));
        $streamWrapper->expects($this->any())
            ->method('initFromPath')
            ->will($this->returnValue(true));

        // the call
        $ret = $streamWrapper->unlink($path);

        $this->assertTrue($ret !== false);
    }


    public function testStream_write()
    {
        $data = '1234567890';

        $streamWrapper = $this->getMock($this->getStreamWrapperClass(), array('getOnWriteDataMode'));
        $streamWrapper->expects($this->any())
            ->method('getOnWriteDataMode')
            ->will($this->returnValue(true));

        $ret = $streamWrapper->stream_write($data);
        $this->assertEquals($data, $streamWrapper->getDataBuffer());
        $this->assertEquals($ret, strlen($data));
    }



    public function testStream_read()
    {
        //we want to test that the file is unlinked
        $resourceName = 'js_75a9295_bootstrap-modal_3.js';
        $resourceContainerName = 'liuggio_assetic';
        $path = 'rscf://' . $resourceContainerName . '/' . $resourceName;


        $objectDataBuffer = '1234567890';
        // creating the object
        $object = $this->getMock("\StdClass", array('read'));
        $object->expects($this->any())
            ->method('read')
            ->will($this->returnValue($objectDataBuffer));
        $object->content_length = strlen($objectDataBuffer);

        $resource = new RackspaceCloudFilesResource();
        $resource->setResourceName($resourceName);
        $resource->setContainerName($resourceContainerName);
        $resource->setObject($object);

        //mocking sw
        $streamWrapper = $this->getMock($this->getStreamWrapperClass(), array('getPosition', 'getResource'));
        $streamWrapper->expects($this->any())
            ->method('getPosition')
            ->will($this->returnValue(0));
        $streamWrapper->expects($this->any())
            ->method('getResource')
            ->will($this->returnValue($resource));

        // the call
        $ret = $streamWrapper->stream_read(strlen($objectDataBuffer));
        //asserting
        $this->assertEquals($ret, $objectDataBuffer);
    }


    public function testStream_flush()
    {
        //with flush we want to test that the function object->write is called correctly
        //we want to test that the file is unlinked
        $resourceName = 'js_75a9295_bootstrap-modal_3.js';
        $resourceContainerName = 'liuggio_assetic';
        $path = 'rscf://' . $resourceContainerName . '/' . $resourceName;

        $phpunit = $this;
        $objectDataBuffer = '1234567890';
        // creating the object
        $object = $this->getMock("\StdClass", array('setData','Create'));
        //asserting that the object -> write is called correctly
        $object->expects($this->any())
            ->method('setData')
            ->will($this->returnCallback(function ($buffer) use ($phpunit, $objectDataBuffer) {
                $phpunit->assertEquals($buffer, $objectDataBuffer);
                //$phpunit->assertEquals($len, strlen($objectDataBuffer));
                return true;
        }));

        $object->expects($this->once())
            ->method('Create');

        $resource = new RackspaceCloudFilesResource();
        $resource->setResourceName($resourceName);
        $resource->setContainerName($resourceContainerName);
        $resource->setObject($object);

        $service =  $this->generateMockService(array('guessFileType'));
        $service->expects($this->any())
            ->method('guessFileType')
            ->will($this->returnValue('mimetypeTest'));


        //mocking sw
        $streamWrapper = $this->getMock($this->getStreamWrapperClass(), array('getDataBuffer', 'getResource','getService'));
        $streamWrapper->expects($this->any())
            ->method('getDataBuffer')
            ->will($this->returnValue($objectDataBuffer));
        $streamWrapper->expects($this->any())
            ->method('getResource')
            ->will($this->returnValue($resource));
        $streamWrapper->expects($this->any())
            ->method('getService')
            ->will($this->returnValue($service));


        $streamWrapper->setService($streamWrapper);
        // the call
        $ret = $streamWrapper->stream_flush();
        //asserting
        $this->assertEquals($ret, true);

    }


    public function testMkdir() {

        //testing that the API create_paths is called
        //we want to test that the file is unlinked
        $resourceName = 'js_75a9295_bootstrap-modal_3.js';
        $resourceContainerName = 'liuggio_assetic';
        $path = 'rscf://' . $resourceContainerName . '/' . $resourceName;
        // assert that delete_object is called with the correct name
        $phpunit = $this;
        $container = $this->getMock("\StdClass", array('create_paths'));
        $container->expects($this->any())
            ->method('create_paths')
            ->will($this->returnCallback(function ($path) use ($phpunit, $resourceName) {
            $phpunit->assertEquals($resourceName, $path);
            return true;
        }));


        $resource = new RackspaceCloudFilesResource();
        $resource->setResourceName($resourceName);
        $resource->setContainerName($resourceContainerName);
        $resource->setContainer($container);

        //mocking sw
        $streamWrapper = $this->getMock($this->getStreamWrapperClass(), array('getResource','initFromPath'));
        $streamWrapper->expects($this->any())
            ->method('getResource')
            ->will($this->returnValue($resource));
        $streamWrapper->expects($this->any())
            ->method('initFromPath')
            ->will($this->returnValue(true));

        // the call
        $ret = $streamWrapper->mkdir($path, '', '');

        $this->assertTrue($ret !== false);

    }
}
