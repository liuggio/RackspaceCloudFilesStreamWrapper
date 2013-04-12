<?php

namespace Liuggio\RackspaceCloudFilesStreamWrapper\Model;

/**
 * Description of RSCFResource
 *
 * @author liuggio
 */
class RackspaceCloudFilesResource
{

    private $containerName;
    private $currentPath;
    private $resourceName;
    private $object = null;
    private $container = null;

    /**
     *
     * @param string $path
     */
    public function __construct($path = null)
    {
        if (!empty($path)) {
            $this->initResourceByPath($path);
        }
    }

    /**
     * Take the container and the resource name from the 
     * 
     * @param string $path
     * @return RSCFResource|false
     */
    public function initResourceByPath($path)
    {
        $parsed = parse_url($path);

        if ($parsed === false) {
            return false;
        }
        $this->currentPath = $path;

        if (isset($parsed['host'])) {
            $this->containerName = $parsed['host'];
        }
        if (isset($parsed['path'])) {
            $this->resourceName = $this->cleanName($parsed['path']);
        }
        return $this;
    }
    /**
     *
     * @return String 
     */
    public function getContainerName()
    {
        return $this->containerName;
    }
    /**
     *
     * @return String 
     */
    public function getResourceName()
    {
        return $this->resourceName;
    }

    public function setContainerName($containerName)
    {
        $this->containerName = $containerName;
    }
    /**
     * @param $resourceName
     */
    public function setResourceName($resourceName)
    {
        $this->resourceName = $resourceName;
    }
    /**
     *
     * @param String $pathName
     * @return String 
     */
    public function cleanName($pathName)
    {
        $pathName = ltrim($pathName, '/');
        return $pathName;
    }

    /**
     * set the variable given to the $object property
     * 
     * @param type $object 
     */
    public function setObject($object)
    {
        $this->object = $object;
    }

    /**
     * get the current $object
     * 
     * @return $object 
     */
    public function getObject()
    {
        return $this->object;
    }

    /**
     * set the variable given to the container property
     * 
     * @param type container 
     */
    public function setContainer($container)
    {
        $this->container = $container;
    }

    /**
     * get the current container
     * 
     * @return $object 
     */
    public function getContainer()
    {
        return $this->container;
    }
    /**
     * @return string
     */
    public function getCurrentPath()
    {
        return $this->currentPath;
    }
    /**
     * @param $currentPath
     */
    public function setCurrentPath($currentPath)
    {
        $this->currentPath = $currentPath;
    }

}

