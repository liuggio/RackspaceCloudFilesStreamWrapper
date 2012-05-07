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
        $resourceContainerName = 'terravision_assetic';
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


}
