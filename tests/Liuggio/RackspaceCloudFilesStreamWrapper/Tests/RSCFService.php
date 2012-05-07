<?php

namespace Liuggio\RackspaceCloudFilesStreamWrapper\Tests\StreamWrapper;

use Liuggio\RackspaceCloudFilesStreamWrapper\RackspaceCloudFilesServiceInterface;
use Liuggio\RackspaceCloudFilesStreamWrapper\RackspaceCloudFilesResource;
/**
 * test service
 *
 * @author liuggio
 */
class RSCFService implements RackspaceCloudFilesServiceInterface
{
    /**
     * @param string $container_name
     * @return \stdClass
     */
    public function apiGetContainer($container_name) {

        return new \stdClass();
    }
    /**
     * @param $container
     * @param string$object_name
     * @return \stdClass
     */
    public function apiGetObjectByContainer($container, $object_name) {

        return new \stdClass();
    }
    /**
     *
     * @param type $resource
     * @return false|container
     */
    public function getContainerByResource($resource)
    {
        return $resource->getContainer();
    }

    /**
     *
     * @param $resource
     * @return false|object
     */
    public function getObjectByResource($resource) {
        $container = $resource->getContainer();
        if ($container) {
            return $resource->getObject();
        } else {
            return false;
        }
    }
    /**
     * 
     * @param string $path
     * @return resource|false
     */
    public function createResourceFromPath($path) {
        $resource = new RackspaceCloudFilesResource($path); 
        if (!$resource) {
            return false;
        }

        $container = $this->apiGetContainer($resource->getResourceName());
        if (!$container) {
            return false;
        }
        $resource->setContainer($container);
        //create_object but no problem if already exists
        $obj = $this->apiGetObjectByContainer($container, $resource->getResourceName());
        if (!$obj) {
            return false;
        }
        $resource->setObject($obj);

        return $resource;
    }
    
}

 
