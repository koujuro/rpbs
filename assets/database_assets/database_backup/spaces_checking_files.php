<?php

require_once __DIR__ . "/Spaces-API-master/spaces.php";

$key = "373BIKGCITDAMCUULN6Z";
$secret = "edRjISPRqnTnrhxn6e4DL+eONqQIMVIYv4nG94xa8mQ";
$space_name = "pbs-database_assets-backup";
$region = "ams3";

// Connecting to space
$space = new SpacesConnect($key, $secret, $space_name, $region);

echo "Connected to space." . PHP_EOL;

// Listing all files/folders
$files = $space->ListObjects();
echo ">>> List of files and folders <<<" . PHP_EOL;
foreach ($files as $file)
    echo "- " . $file['Key'] . PHP_EOL;

?>