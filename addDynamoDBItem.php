<?php

require_once('config.php');

function addDynamoDBItem($client,$email,$unixTime,$attendance) {
	try {
		$result = $client->putItem(array(
		    'TableName' => DynamoDB_TABLE,
		    'Item' => array(
		        'Email'      => array('S' => $email),
		        'UnixTime'    => array('N' => $unixTime),
		        'Attendance'   => array('S' => $attendance)
		    )
		));
	} catch (exception $e) {
		echo 'DynamoDB登録の例外：', $e->getMessage(), "\n";
		exit;
	}
}
