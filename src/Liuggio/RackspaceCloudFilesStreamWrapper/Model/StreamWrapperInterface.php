<?php

namespace  Liuggio\RackspaceCloudFilesStreamWrapper\Model;

/**
 *
 * @author liuggio
 */
interface StreamWrapperInterface
{

    function dir_closedir();

    function dir_opendir($path, $options);

    function dir_readdir();

    function dir_rewinddir();

    function mkdir($path, $mode, $options);

    function rename($path_from, $path_to);

    function rmdir($path, $options);

    function stream_cast($cast_as);

    function stream_close();

    function stream_eof();

    function stream_flush();

    function stream_lock($operation);

    function stream_metadata($path, $option, $var);

    function stream_open($path, $mode, $options, &$opened_path);

    function stream_read($count);

    function stream_seek($offset, $whence);

    function stream_set_option($option, $arg1, $arg2);

    function stream_stat();

    function stream_tell();

    function stream_write($data);

    function unlink($path);

    function url_stat($path, $flags);
}

