<?php
function localizeIp($ip){
	require("config.php");

	$url = "http://ip-api.com/json/{$ip}";

	$response = file_get_contents($url);
	
	$data = json_decode($response);

	if ($data->status == "success") {
		return $data;
	} else {
		error_log("\n" . __FILE__ . " : " . time() . " : Can't localize ip: " . $data, errorLogMode, errorLogPath);
		return false;
	}
}
?>