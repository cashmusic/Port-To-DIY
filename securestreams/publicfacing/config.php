<?php

/**
 * @file
 * A single location to store configuration.
 */

// add your facebook credentials here
define('STREAM_ID', 0);
define('S3_LIB_LOCATION', dirname(__FILE__) . './path/to/lib/S3.php');
define('S3_BUCKET', 'bucketname');
define('INCLUDE_LOCATION', dirname(__FILE__) . './path/to/core/core.php');

// AWS access info
define('awsAccessKey', 's3key');
define('awsSecretKey', 's3secretkey');

$ss_playlist = array(
	'artist' => 'Artist name for display',
	'title' => 'Title for display',
	'data' => array(
		array(
			'uri' => 's3/url/to/private/01.mp3',
			'title' => 'Title 01'
		),
		array(
			'uri' => 's3/url/to/private/02.mp3',
			'title' => 'Title 02'
		)
	)
);

?>