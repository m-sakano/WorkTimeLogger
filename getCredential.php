<?php

require_once('config.php');

function getCredential($client,$email) {
	try {
		$result = $client->getIterator('Query', array(
		    'TableName' => DynamoDB_CONFIG_TABLE,
		    'KeyConditions' => array(
		        'Email' => array(
		            'AttributeValueList' => array(
		                array('S' => $email)
		            ),
		            'ComparisonOperator' => 'EQ'
		        ),
		        'AppsName' => array(
		            'AttributeValueList' => array(
		                array('S' => BRAND)
		            ),
		            'ComparisonOperator' => 'EQ'
		        )
		    )
		));
	} catch (exception $e) {
		echo 'DynamoDBアイテム取得の例外：', $e->getMessage(), "\n";
		exit;
	}
	return $result;
}
