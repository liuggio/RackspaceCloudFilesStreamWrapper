<?php
namespace Liuggio\RackspaceCloudFilesStreamWrapper\Model;
/**
 *
 */
interface FileTypeGuesserInterface
{
    /**
     * @static
     * @abstract
     * @param $filename
     * @return String
     */
    public static function guessByFileName($filename);

}
