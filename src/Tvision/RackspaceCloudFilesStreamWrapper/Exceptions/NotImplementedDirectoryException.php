<?php
namespace Tvision\RackspaceCloudFilesStreamWrapper\Exceptions;

use Tvision\RackspaceCloudFilesStreamWrapper\Exceptions\NotImplementedException;

class NotImplementedDirectoryException extends NotImplementedException
{

    public function __construct($message = "", $code = 0, \Exception $previous = null)
    {
    	
    	$message.= ' Rackspace Cloud Files will simulate a nested directory (or folder) structure.';

    	parent::__construct($message, $code, $previous);
    }

}
