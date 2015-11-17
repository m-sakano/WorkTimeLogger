<?php

require_once('config.php');

function getDynamoDBItem($client,$email,$unixTimeFrom) {
	try {
		$result = $client->getIterator('Query', array(
		    'TableName' => DynamoDB_TABLE,
		    'KeyConditions' => array(
		        'Email' => array(
		            'AttributeValueList' => array(
		                array('S' => $email)
		            ),
		            'ComparisonOperator' => 'EQ'
		        ),
		        'UnixTime' => array(
		            'AttributeValueList' => array(
		                array('N' => $unixTimeFrom)
		            ),
		            'ComparisonOperator' => 'GE'
		        )
		    )
		));
	} catch (exception $e) {
		echo 'DynamoDBアイテム取得の例外：', $e->getMessage(), "\n";
		exit;
	}
	return $result;
}
