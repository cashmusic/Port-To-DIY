<?php
	// outputs a basic CSV file for download codes
	include_once('./path/to/core/core.php');
	$go = false;
	if ($go) {
		for ($i=1; $i<=100; $i++) {
			$download_id = 0; // id of download
			$download_code = dl_addNewDownloadCode($download_id);
			echo $download_id . $download_code . "\n";
		}
	}
?>