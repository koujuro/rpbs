<?php

require_once "../../header/header.php";

SessionUtilities::unsetAllSessionOnSite();
header("location: ../../index.php");
die();