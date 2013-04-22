<?php

/**
 * Instructions:
 * Before run this test export your Rackspace USER - API_KEY - CONTAINER_NAME
 *
**/

if (file_exists($file = __DIR__.'/../vendor/autoload.php')) {
    require_once $file;
} else {
    die('Please run composer install first.');
}


if (! getenv('RSCF_ENDPOINT') ) {
    //The default endpoint is v2.0
	$endPoint = 'https://lon.identity.api.rackspacecloud.com/v2.0/';
} else {
	$endPoint = getenv('RSCF_ENDPOINT');
}

$username      =	getenv('RSCF_USER');
$apiKey        =	getenv('RSCF_KEY');
$containerName = 	getenv('RSCF_CONTAINER');


// Crate a new Rackspace API Service
$api_service = new \Tvision\RackspaceCloudFilesStreamWrapper\Service\RackspaceApi('OpenCloud\Rackspace', $endPoint, $username, $apiKey, $containerName);

$protocol_name 			= 'rsfc';
$stream_wrapper_class   = 'Tvision\RackspaceCloudFilesStreamWrapper\StreamWrapper\RackspaceCloudFilesStreamWrapper';
$resource_entity_class  = 'Tvision\RackspaceCloudFilesStreamWrapper\Model\RackspaceCloudFilesResource';
$fileTypeGuesser 		= new \Tvision\RackspaceCloudFilesStreamWrapper\Service\FileTypeGuesser();

// Crate a new Rackspace Cloud Files Service
$rsfcService 			= new \Tvision\RackspaceCloudFilesStreamWrapper\Service\RSCFService(
	$protocol_name, 
	$api_service, 
	$stream_wrapper_class, 
	$resource_entity_class, 
	$fileTypeGuesser
	);


// Prepare the streamwrapper class and register it
$stream_wrapper_class::setService($rsfcService);
$stream_wrapper_class::registerStreamWrapperClass($protocol_name);

if ( in_array($protocol_name, stream_get_wrappers())) {
	echo("[" . $protocol_name . "] protocol is registered\n\n");
}

// Test the stream wrapper
echo("Reading data from test.jpg ...\n\n");

$content = file_get_contents(__DIR__ . "/test.jpg");
echo("Content ready...\n\n");


// Push the test.jpg file to Rackspace
if (false === file_put_contents($protocol_name . '://' . $containerName . '/test.jpg', $content)){
	echo("Test failed\n\n");
} else  {
	echo("Test succes, check file in [" . $containerName . '/test.jpg' . "]\n\n");
}