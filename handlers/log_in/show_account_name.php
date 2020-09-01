<?php

require_once __DIR__ . "/../../header/header.php";

if (SessionUtilities::checkIsSessionSet('User')) {
    echo SessionUtilities::getSession('User');
} else
    echo "redirect";