<?php

require_once __DIR__ . "/Spaces-API-master/spaces.php";

$key = "373BIKGCITDAMCUULN6Z";
$secret = "edRjISPRqnTnrhxn6e4DL+eONqQIMVIYv4nG94xa8mQ";
$space_name = "pbs-database_assets-backup";
$region = "ams3";

// Connecting to space
$space = new SpacesConnect($key, $secret, $space_name, $region);

// Downloading file
$file_name = $argv[1];
$save_as = "downloads/" . $argv[1];

$space->DownloadFile($file_name, $save_as);

?>