Introduction
------------

Rackspace Cloud Files bundle is a simple and easy way to use [RackspaceCloudFilesStreamWrapper](https://github.com/tvision/RackspaceCloudFilesStreamWrapper/tree/2.2) library with Symfony2 applications, but it has also some facilities for handle the static file with the rackspace cloud files.

This Bundle borns as fork of the escapestudios/EscapeRackspaceCloudFilesBundle, now these two bundles are very different.

[![Build Status](https://secure.travis-ci.org/tvision/RackspaceCloudFilesStreamWrapper.png)](http://travis-ci.org/tvision/RackspaceCloudFilesStreamWrapper)
[![Total Downloads](https://poser.pugx.org/tvision/rackspace-cloud-files-streamwrapper/downloads.png)](https://packagist.org/packages/tvision/rackspace-cloud-files-streamwrapper)
[![Latest Stable Version](https://poser.pugx.org/tvision/rackspace-cloud-files-streamwrapper/v/stable.png)](https://packagist.org/packages/tvision/rackspace-cloud-files-streamwrapper)
see the blog post for more detail

[http://www.welcometothebundle.com/symfony2-assets-on-rackspace-cloud-files/](http://www.welcometothebundle.com/symfony2-assets-on-rackspace-cloud-files)

Installation (old school)
-------------------------------

see the blog post for more detail

deps:

```
[php-opencloud]
    git=git://github.com/rackspace/php-opencloud.git
    target=/rackspace/php-opencloud

[RackspaceCloudFilesBundle]
    git=https://github.com/liuggio/RackspaceCloudFilesBundle.git
    target=/bundles/Liuggio/RackspaceCloudFilesBundle

[RackspaceCloudFilesStreamWrapper]
    git=https://github.com/tvision/RackspaceCloudFilesStreamWrapper.git
    target=rackspace-cloud-files-streamwrapper

```

app/autoload.php

```
$loader->registerNamespaces(array(
    //other namespaces
    'Tvision\\RackspaceCloudFilesStreamWrapper' =>  __DIR__.'/../vendor/liuggio-rscf-streamwrapper/src',
    'Tvision\\RackspaceCloudFilesBundle'        =>  __DIR__.'/../vendor/bundles',
  ));

```

app/AppKernel.php

```
public function registerBundles()
{
    return array(
        //other bundles
        new Tvision\RackspaceCloudFilesBundle\LiuggioRackspaceCloudFilesBundle(),
    );
    ...
```

Installation Composer
-------------------------------

* 1 First, add the dependent bundles to the vendor/bundles directory. Add the following lines to the composer.json file

```
    "require": {
    # ..
    "liuggio/rackspace-cloud-files-bundle": ">=2.2",
    # ..
    }
```

* 2 Then run `composer install`


* 3 Then add in your `app/AppKernel`

``` php

 class AppKernel extends Kernel
 {
     public function registerBundles()
     {
         $bundles = array(
         // ...
            new Tvision\RackspaceCloudFilesBundle\LiuggioRackspaceCloudFilesBundle(),
         // ...

```


## Configuration

app/config/config.yml

```
#  Rackspace Cloud Files configuration

liuggio_rackspace_cloud_files:
    service_class: Liuggio\RackspaceCloudFilesStreamWrapper\StreamWrapper\RackspaceCloudFilesStreamWrapper
    stream_wrapper:
        register: true  # do you want to register stream wrapper?
#        protocol_name: rscf
#        class: Liuggio\StreamWrapper\RackspaceCloudFilesStreamWrapper
    auth:
        username: YOUR-USERNAME
        api_key: YOUR-API-KEY
        host: https://lon.identity.api.rackspacecloud.com/v2.0 # or usa
        container_name: YOUR-CONTAINER-NAME
        region: LON
```

## Service(s)

Get the Rackspace service to work with:

```
$auth = $this->get('liuggio_rackspace_cloud_files.service');

```

## Usage example without assetic
See the [**test file**](https://github.com/tvision/RackspaceCloudFilesStreamWrapper/blob/2.2/tests/test_api.php) 


## Usage example with assetic

see

http://www.welcometothebundle.com/symfony2-assets-on-rackspace-cloud-files/

## Installing bundles assets (public directory) to cloudfiles with `rscf:assets:install` special console command

```
app/console rscf:assets:install rscf://my_container/my/path
```

This will copy assets just like the `assets:install` command would but directly to rackspace.
**Note**: For those wondering why this command could be needed, note that assetic mainly handles js/css assets, and when
 not using the cssembed filter, you still need to install images to your cloudfiles container. This command prevent you
 from having to do that by hand.


## Installing application assets (public directory) to cloudfiles with `assetic:install` special console command

add this into the config.yml

```
assetic:
    debug: false
    use_controller: false
    write_to: rsfc://%rackspace_container_name%
```

Type to the console

```
app/console assetic:dump
```

Requirements
------------

- PHP > 5.3.0

- rackspace/php-cloudfiles.git

- liuggio/RackspaceCloudFilesStreamWrapper

- Symfony2


Contribute
----------

Please feel free to use the Git issue tracking to report back any problems or errors. You're encouraged to clone the repository and send pull requests if you'd like to contribute actively in developing the library.
than add your name to this file under the contributor section


Contributor
------------

- thanks for cystbear for the tips

- the bundle is a reengeneering of the escapestudios/EscapeRackspaceCloudFilesBundle


1. liuggio

2. benjamindulau

3. toretto460


License
-------

This bundle is under the MIT license. See the complete license in the bundle:

    Resources/meta/LICENSE
