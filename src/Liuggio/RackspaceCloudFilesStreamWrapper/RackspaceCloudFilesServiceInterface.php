<?php

namespace  Liuggio\RackspaceCloudFilesStreamWrapper;

/**
 * Description of RackspaceCloudFilesServiceInterface
 *
 * @author liuggio
 */
interface RackspaceCloudFilesServiceInterface
{
    /**
     * @param string $container_name
     * @return \stdClass
     */
    public function apiGetContainer($container_name);

    /**
     * @param $container
     * @param string$object_name
     * @return \stdClass
     */
    public function apiGetObjectByContainer($container, $object_name);
    /**
     * 
     * @param string $path
     * @return resource|false
     */
    public function createResourceFromPath($path);
    /**
     *
     * @param type $resource
     * @return false|container
     */
    public function getContainerByResource($resource);

    /**
     *
     *
     * @param $resource
     * @return false|object
     */
    public function getObjectByResource($resource);


}
