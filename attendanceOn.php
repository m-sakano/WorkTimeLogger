<?php

require_once('config.php');
require_once('createDynamoDBClient.php');
require_once('addDynamoDBItem.php');
session_start();

if (is_null($_SESSION['me'])) {
	header('Location: '.SITE_URL);
}

$client = createDynamoDBClient();
$result = addDynamoDBItem($client, $_SESSION['email'], time(), '出勤');

header('Location: '.SITE_URL);
