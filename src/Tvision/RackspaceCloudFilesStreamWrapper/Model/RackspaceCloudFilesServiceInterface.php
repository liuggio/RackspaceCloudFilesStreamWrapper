<?php

namespace  Tvision\RackspaceCloudFilesStreamWrapper\Model;

use \OpenCloud\ObjectStore\Resource\Container;

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
     * @param Container $container
     * @param $objectData
     *
     * @return mixed
     */
    public function apiGetObjectByContainer(Container $container, $objectData);
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

    /**
     * try to guess the mimetype from a filename
     *
     * @abstract
     * @param $filename
     * @return string
     */
    public function guessFileType($filename);


}
