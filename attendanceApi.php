<?php

require_once('config.php');
require_once('createDynamoDBClient.php');
require_once('addDynamoDBItem.php');
require_once('getCredential.php');

if (is_null($_POST['email']) || is_null($_POST['credential']) || is_null($_POST['status'])) {
	exit;
}

$client = createDynamoDBClient();
$email = $_POST['email'];

// DynamoDBのcredentialを取得
$credentials = getCredential($client, $email);
if (iterator_count($credentials) > 0) {
	foreach ($credentials as $c) {
		$credential = openssl_decrypt($c['Credential']['S'], OpenSSL_ENCRYPT_METHOD, OpenSSL_ENCRYPT_KEY);
		if ($credential == '') {
			exit;
		}
		break;
	}
} else {
	exit;
}

// credentialが一致するか確認
if ($credential != $_POST['credential']){
	exit;
}

// status: 1.自社出社 2.自社退社 3.案件先出社 4.案件先退社
switch ($_POST['status']) {
	case '1':
		$status = '自社出社';
		break;
	case '2':
		$status = '自社退社';
		break;
	case '3':
		$status = '案件先出社';
		break;
	case '4':
		$status = '案件先退社';
		break;
	default:
		header('Location: '.SITE_URL);
}

$result = addDynamoDBItem($client, $email, time(), $status);

echo 'Success!';
