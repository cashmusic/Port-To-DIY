<?php
	// uploads a file to s3 with attachment disposition 
	// (forces download for MP3, instead of play-in-browser)
	require_once('../../settings/S3.php');

	if (!defined('awsAccessKey')) define('awsAccessKey', 's3key');
	if (!defined('awsSecretKey')) define('awsSecretKey', 's3secretkey');
	
	// Instantiate the class
	$s3 = new S3(awsAccessKey, awsSecretKey);
	
	$bucket = 'bucketname';
	$uri = 'url/for/asset/inside/bucket/name.mp3';

    // PUT with custom headers:
 
    $put = S3::putObject(
        S3::inputFile('./path/to/loca/file/name.mp3'),
        $bucket,
        $uri,
        S3::ACL_PUBLIC_READ,
        array(),
        array( // Custom $requestHeaders
            "Content-Disposition" => "attachment;filename=name.mp3"
        )
    );
    var_dump($put); // spits out true/fase. that's some high-tech shit right there
?>