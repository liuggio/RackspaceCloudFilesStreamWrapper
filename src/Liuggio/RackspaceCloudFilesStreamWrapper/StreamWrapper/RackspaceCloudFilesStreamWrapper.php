<?php

namespace Liuggio\RackspaceCloudFilesStreamWrapper\StreamWrapper;

use Liuggio\RackspaceCloudFilesStreamWrapper\StreamWrapperInterface;


/**
 * Description of RackspaceStreamWrapper
 *
 * @author liuggio
 */
class RackspaceCloudFilesStreamWrapper implements StreamWrapperInterface
{

    static $service;    
    static $protocolName;
    
    private $resource = null;
    private $dataBuffer = null;
    private $dataPosition = 0;
    private $onWriteDataMode = false;
    private $containerList = array();


    static $stream_wrapper_register = 'stream_wrapper_register';
    static $stream_wrapper_unregister = 'stream_wrapper_unregister';


    /*
     * Registers the stream wrapper to handle the specified protocolName
     *
     * @param  string $schema Default is rscf
     */

    public static function registerStreamWrapperClass($protocol_name = 'rscf')
    { 
        self::$protocolName = $protocol_name;
        $registerFunction = self::getStreamWrapperRegisterFunction();

        if (!isset(self::$protocolName)) {
            throw new \RuntimeException(
                    sprintf('Scheme name not found for %s', __CLASS__));
        }

        self::unregisterStreamWrapperClass();

        if (!$registerFunction(self::$protocolName, __CLASS__)) {
            throw new \RuntimeException(sprintf(
                            'Could not register stream wrapper class %s for protocolName %s.', __CLASS__, self::$protocolName
            ));
        }
    }

    /**
     * Registers the stream wrapper to handle the specified protocolName
     *
     * @param  string $schema Default is rscf
     */
    public static function unregisterStreamWrapperClass()
    { 
        $unregisterFunction = self::getStreamWrapperUnregisterFunction();
        if (!isset(self::$protocolName)) {
            throw new \RuntimeException(
                    sprintf('Scheme name not found for %s', __CLASS__));
        }

        @$unregisterFunction(self::$protocolName);
    }

    /**
     *
     * @param $service 
     */
    public function setService($service)
    { 
        self::$service = $service;
    }

    /**
     *
     * @return service 
     */
    public function getService()
    {   

        return self::$service;
    }

    public function dir_closedir()
    { 
        throw new \NotImplementedException(__FUNCTION__);
//        $array = array();
//        $this->setContainerList($array);
        return true;
    }

    /**
     *
     * @param type $path
     * @param  $options
     * @return bool
     */
    public function dir_opendir($path, $options)
    {  //@todo with nested list object with paths
        throw new \NotImplementedException(__FUNCTION__);
        $retVal = false;
        if ($this->initFromPath($path) && $this->getResource()->getContainer()) {
            $this->containerList = $this->getResource()->getContainer()->list_objects();
            $retVal = true;
        }
        return $retVal;
    }

    /**
     * 
     * @return bool
     */
    public function dir_readdir()
    {   // @todo with nested
        throw new \NotImplementedException(__FUNCTION__);
        $object = current($this->containerList);
        if ($object !== false) {
            next($this->containerList);
        }
        return $object;
    }

    /**
     * 
     * @return bool
     */
    public function dir_rewinddir()
    {    //@todo with nested
        throw new \NotImplementedException(__FUNCTION__);
        reset($this->containerList);
        return true;
    }

    /**
     *
     * @param type $path
     * @param  $mode
     * @param  $options 
     * @return bool
     */
    public function mkdir($path, $mode, $options)
    { 
        if (!$this->initFromPath($path)) {
            return false;
        }
        $this->getResource()->getContainer()->create_paths($this->getResource()->getResourceName());
        $this->reset();
        return true;
    }

    /**
     *
     * @param type $path_from
     * @param type $path_to 
     * @return bool
     */
    public function rename($path_from, $path_to)
    {    // @todo
        throw new \NotImplementedException(__FUNCTION__);
        return false;
    }

    /**
     *
     * @param type $path
     * @param  $options
     * @return bool
     */
    public function rmdir($path, $options)
    {    // @todo
        throw new \NotImplementedException(__FUNCTION__);
        return false;
    }

    /**
     * 
     * @param  $cast_as 
     * @return bool
     */
    public function stream_cast($cast_as)
    { 
        throw new \BadFunctionCallException();
    }

    public function stream_close()
    { 
        if ($this->getOnWriteDataMode()) {
            $this->stream_flush();
        }
        $this->reset();
    }

    /**
     *
     * @return bool
     */
    public function stream_eof()
    { 
        if (!$this->getResource() || !$this->getResource()->getObject()) {
            return true;
        }

        return ((int) $this->getPosition() >= (int) $this->getResource()->getObject()->content_length);
    }

    /**
     * 
     * @return bool
     */
    public function stream_flush()
    { 
        if (!$this->getResource()) {
            return false;
        }

        $buffer = $this->getDataBuffer();
        $retVal = false;
        if (!empty($buffer)) {
            $object = $this->getResource()->getObject();
            $retVal = $object->write($buffer, strlen($buffer));
        }
        $this->setDataBuffer(null);
        return $retVal;
    }

    /**
     *
     * @param mode $operation 
     * @return bool
     */
    public function stream_lock($operation)
    { 
        throw new \BadFunctionCallException();
    }

    /**
     *
     * @param  $path
     * @param  $option
     * @param  $var 
     * @return bool
     */
    public function stream_metadata($path, $option, $var)
    { 
        throw new \BadFunctionCallException();
    }

    /**
     *
     * @param type $path
     * @param type $mode
     * @param  $options
     * @param type $opened_path 
     * @return bool
     */
    public function stream_open($path, $mode, $options, &$opened_path)
    { 
        if ($this->initFromPath($path)) {
            //if this stream is being opened for writing, clear the object buffer
            //return true as we'll create the object on flush call
            if (strpbrk($mode, 'wax')) {
                $this->setOnWriteDataMode(true);
                //we'll return true as we'll create the object on the stream_flush call
                return true;
            } else {
                $this->setOnWriteDataMode(false);
                return true;
            }
        }
        return false;
    }

    /**
     *
     * @param  $count 
     * @return string
     */
    public function stream_read($count)
    { 
        if (!$this->getResource() || !$this->getResource()->getObject()) {
            return false;
        }
        $object = $this->getResource()->getObject();
        // make sure that count doesn't exceed object size
        if ($count + $this->getPosition() > $object->content_length) {
            $count = $object->content_length - $this->getPosition();
        }
        $data = substr($object->read(), $this->getPosition(), $count);
        $this->appendPosition(strlen($data));
        return $data;
    }

    /**
     * Update the read/write position of the stream
     *
     * @param integer $offset
     * @param integer $whence
     * @return boolean
     */
    public function stream_seek($offset, $whence)
    { 
        if (!$this->getResource() || !$this->getResource()->getObject()) {
            return false;
        }
        $object = $this->getResource()->getObject();
        switch ($whence) {
            case SEEK_CUR:
                // Set position to current location plus $offset
                $new_pos = $this->getPosition() + $offset;
                break;
            case SEEK_END:
                // Set position to end-of-file plus $offset
                $new_pos = $object->content_length + $offset;
                break;
            case SEEK_SET:
            default:
                // Set position equal to $offset
                $new_pos = $offset;
                break;
        }
        $ret = ($new_pos >= 0 && $new_pos <= $object->content_length);
        if ($ret) {
            $this->setPosition($new_pos);
        }
        return $ret;
    }

    /**
     *
     * @param  $option
     * @param  $arg1
     * @param  $arg2 
     * @return bool
     */
    public function stream_set_option($option, $arg1, $arg2)
    { 
        throw new \BadFunctionCallException();
    }

    /**
     * 
     * @return array
     */
    public function stream_stat()
    { 
        return $this->statCurrentResource();
    }

    /**
     *
     * @return 
     */
    public function stream_tell()
    { 
        return $this->getPosition();
    }

    /**
     * 
     * @param string $data 
     * @return int
     */
    public function stream_write($data)
    { 
        if ($this->getOnWriteDataMode()) {

            $length = strlen($data);
            $this->appendDataBuffer($data);
            $this->appendPosition($length);
            return $length;
        } else {
            throw new \Exception('dirty mode!!');
        }
    }

    /**
     * @return bool
     */
    public function unlink($path)
    { 
        if (!$this->initFromPath($path)) {
            return false;
        }
        if ($this->getResource()->getContainer()) {
            $retVal = $this->getResource()->getContainer()->delete_object($this->getResource()->getResourceName());
        }
        $this->reset();
        return $retVal;
    }

    /**
     * @param type $path
     * @param type $flags 
     * @return array
     */
    public function url_stat($path, $flags)
    { 
        if (!$this->getResource() || !$this->getResource()->getObject()) {
            return false;
        }
        return $this->statCurrentResource();
    }



    /**
     * reset the variable 
     */
    public function reset()
    { 
        $this->setPosition(0);
        $this->setOnWriteDataMode(false);
        $this->setDataBuffer(null);
        $this->setResource(null);
    }

    /**
     * creates the resource, the container and the object by the path given
     *
     * @param string $path
     * @return boolean|\Liuggio\RackspaceCloudFilesBundle\StreamWrapper\RackspaceStreamWrapper 
     */
    public function initFromPath($path)
    { 
        $this->setPosition(0);
        $this->setDataBuffer(null);

        $resource = $this->getService()->createResourceFromPath($path);

        if (!$resource) {
            return false;
        }
        $this->setResource($resource);

        return $this;
    }

    /**
     *  @todo better understanding if is a dir  from the "Content-Type of "application/directory"
     */
    private function statCurrentResource()
    { 
        $objectAlreadyExists = true;
        $isADir = false;
        if (!$this->getResource()) {
            return false;
        }

        $name = $this->getResource()->getResourceName();
        $pathParts = pathinfo($name);

        $object = $this->getResource()->getObject();
        if (!$object) {
            $isADir = true;
        }
        if ($object && $object->content_length == 0) {
            $objectAlreadyExists = false;
        }

        if (!$objectAlreadyExists && !isset($pathParts['extension'])) {
            //there's no extension hoping is a dir 
            // it could be a bug if the filename doesnt' have extension.
            $isADir = true;
        }

        $stat = array();
        $stat['dev'] = 0;
        $stat['ino'] = 0;
        $stat['mode'] = 0777;
        $stat['nlink'] = 0;
        $stat['uid'] = 0;
        $stat['gid'] = 0;
        $stat['rdev'] = 0;
        $stat['size'] = 2;
        $stat['atime'] = 0;
        $stat['mtime'] = 0;
        $stat['ctime'] = 0;
        $stat['blksize'] = 0;
        $stat['blocks'] = 0;

        if ($objectAlreadyExists) {
            $stat['size'] = $object->content_length;
            $stat['mtime'] = $object->last_modified;
        }

        if (!$isADir) {
            //S_IFREG indicating "file"
            $stat['mode'] |= 0100000;
        } else {
            $stat['mode'] |= 040000;
        }

        return $stat;
    }

    /**
     * @param $stream_wrapper_register
     */
    public static function setStreamWrapperRegisterFunction($stream_wrapper_register)
    {
        self::$stream_wrapper_register = $stream_wrapper_register;
    }
    /**
     *
     * @return string
     */
    public static function getStreamWrapperRegisterFunction()
    {
        return self::$stream_wrapper_register;
    }
    /**
     * @param $stream_wrapper_register
     */
    public static function setStreamWrapperUnregisterFunction($stream_wrapper_unregister)
    {
        self::$stream_wrapper_unregister = $stream_wrapper_unregister;
    }
    /**
     *
     * @return string
     */
    public static function getStreamWrapperUnregisterFunction()
    {
        return self::$stream_wrapper_unregister;
    }

    /**
     * get if the data status is in write mode
     *
     * @return boolean
     */
    public function getOnWriteDataMode()
    {
        return $this->onWriteDataMode;
    }

    /**
     * set the data status
     *
     */
    public function setOnWriteDataMode($mode = true)
    {
        $this->onWriteDataMode = $mode;
        return $this;
    }

    /**
     * Append some data to the current property data
     *
     * @param string $data
     * @return \Liuggio\RackspaceCloudFilesBundle\StreamWrapper\RackspaceStreamWrapper
     */
    public function appendDataBuffer($data)
    {
        if (is_null($this->dataBuffer)) {
            $this->dataBuffer = $data;
        } else {
            $this->dataBuffer .= $data;
        }
        return $this;
    }

    /**
     * sum the int of the position to the var given
     *
     * @param type $length
     * @return \Liuggio\RackspaceCloudFilesBundle\StreamWrapper\RackspaceStreamWrapper
     */
    public function appendPosition($length)
    {
        $this->dataPosition = (int) $this->dataPosition;
        $this->dataPosition += (int) $length;
        return $this;
    }

    /**
     * get the current position
     *
     * @return int
     */
    public function getPosition()
    {
        return $this->dataPosition;
    }

    /**
     * set the variable given to the dataPosition property
     *
     * @param type $dataPosition
     */
    public function setPosition($position)
    {
        $position = (int) $position;
        $this->dataPosition = $position;
    }

    /**
     * set the variable given to the resource property
     *
     * @param type $resource
     */
    public function setResource($resource)
    {
        $this->resource = $resource;
    }

    /**
     * get the current resource
     *
     * @return resource
     */
    public function getResource()
    {
        return $this->resource;
    }

    /**
     * set the variable given to the buffer property
     *
     * @param type dataBuffer
     */
    public function setDataBuffer($data)
    {
        $this->dataBuffer = $data;
    }

    /**
     * get the current buffer
     *
     * @return $dataBuffer
     */
    public function getDataBuffer()
    {
        return $this->dataBuffer;
    }

}

