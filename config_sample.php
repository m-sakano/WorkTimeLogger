<?php

// SITE Settings
define('SITE_URL', 'http://www.myserver/WorkTimeLogger/');
define('BRAND', 'WorkTimeLogger');

// Cookie Settings
session_set_cookie_params(0, '/WorkTimeLogger/');

// Domain Settings
define('APPS_DOMAIN','mydomain');

// Google Authentication Settings
define('CLIENT_ID', '********.apps.googleusercontent.com');
define('CLIENT_SECRET', '********');

// AWS Settings
define('DynamoDB_TABLE', 'WorkTimeLogger');
define('DynamoDB_CREDENTIAL_TABLE', 'WorkTimeLoggerCredential');
define('DynamoDB_REGION', 'ap-northeast-1');
define('AWS_ACCESS_KEY_ID','********');
define('AWS_SECRET_ACCESS_KEY','********');

// Encription Settings
define('OpenSSL_ENCRYPT_KEY','********');
define('OpenSSL_ENCRYPT_METHOD','AES-256-ECB');

error_reporting(E_ALL &~E_NOTICE);
//ini_set( 'display_errors', 1 );

// Server Locale
setlocale(LC_ALL, 'ja_JP.UTF-8');

// timezone
date_default_timezone_set('Asia/Tokyo');
