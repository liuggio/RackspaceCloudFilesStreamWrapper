<?php

namespace Tvision\RackspaceCloudFilesStreamWrapper\Exception;


class NotImplementedDirectoryException extends NotImplementedException
{

    public function __construct($message = "", $code = 0, \Exception $previous = null)
    {
    	
    	$message.= ' Rackspace Cloud Files simulate will a nested directory (or folder) structure.';

    	parent::__construct($message, $code, $previous);
    }

}
