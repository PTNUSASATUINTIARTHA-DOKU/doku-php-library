<?php

require_once  "src/Snap.php";

$privateKey = "";
$clientKey = "";
$isProduction = false;

$Doku = new DokuSnap($privateKey, $clientKey, $isProduction);