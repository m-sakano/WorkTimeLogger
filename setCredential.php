<?php

require_once('config.php');
require_once('createDynamoDBClient.php');
session_start();

if (is_null($_SESSION['me'])) {
	header('Location: '.SITE_URL);
}

$n = 24; // クレデンシャルの文字数
$seed = strtr(substr(base64_encode(openssl_random_pseudo_bytes($n)),0,$n),'/+','_-');

$client = createDynamoDBClient();
$email = $_SESSION['email'];
$credential = openssl_encrypt($seed, OpenSSL_ENCRYPT_METHOD, OpenSSL_ENCRYPT_KEY);
$unixTime = time();

try {
	$result = $client->putItem(array(
	    'TableName' => DynamoDB_CREDENTIAL_TABLE,
	    'Item' => array(
	        'Email'      => array('S' => $email),
	        'UnixTime'    => array('N' => $unixTime),
	        'Credential'   => array('S' => $credential)
	    )
	));
} catch (exception $e) {
	echo 'DynamoDB登録の例外：', $e->getMessage(), "\n";
	exit;
}

header('Location: '.SITE_URL);
