<?php
require __DIR__ . '/auto_api.php';

$apiDoc = new AutoApiDoc();
echo $apiDoc->generate();