Rackspace Streamwrapper for PHP
=========================
still alpha.

This works, but this is not a stable version yet, some tests must still be written.

[![Build Status](https://secure.travis-ci.org/liuggio/RackspaceCloudFilesStreamWrapper.png)](http://travis-ci.org/liuggio/RackspaceCloudFilesStreamWrapper)


Requirements
------------

- PHP > 5.3.0
- Git installed on the machine running the PHP code

Run tests
---------

1. clone the repository
2. run `phpunit` from within the cloned project folder

Please note that the library has been tested on a Mac OS X 10.7 with the bundled PHP 5.3.6 (git version 1.7.6), on several Ubuntu Linux installations and on Windows Vista running PHP 5.3.7 (1.7.6.msysgit.0). Due to currently unknown reasons the test run a bit unstable on Windows. All tests should be *green* but during cleanup there may be the possibility that some access restrictions randomly kick in and prevent the cleanup code from removing the test directories. 

The unit test suite is continuously tested with [Travis CI](http://travis-ci.org/) on PHP 5.3 and 5.4 and its current status is.

Contribute
----------

Please feel free to use the Git issue tracking to report back any problems or errors. You're encouraged to clone the repository and send pull requests if you'd like to contribute actively in developing the library.

License
-------

Copyright (C) 2012 by liuggio

Permission is hereby granted, free of charge, to any person obtaining a copy of this software and associated documentation files (the "Software"), to deal in the Software without restriction, including without limitation the rights to use, copy, modify, merge, publish, distribute, sublicense, and/or sell copies of the Software, and to permit persons to whom the Software is furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in all copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.