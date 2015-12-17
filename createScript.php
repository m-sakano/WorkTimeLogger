<?php

function createScript($email, $credential, $status) {
	$script = <<<EOS
Option Explicit

Dim myHttpRequest
Dim myURL
Dim myPostData
Dim email
Dim credential
Dim status

email = "$email"
credential = "$credential"
status = "$status"

myURL = "https://www.bft.tokyo/WorkTimeLogger/attendanceApi"
myPostData = "email=" & email & "&credential=" & credential & "&status=" & status

Set myHttpRequest = WScript.CreateObject("MSXML2.XMLHTTP.3.0")

Call myHttpRequest.Open("POST", myURL, False)
Call myHttpRequest.setRequestHeader("Content-Type", "application/x-www-form-urlencoded")
Call myHttpRequest.Send(myPostData)

Set myHttpRequest = Nothing
EOS;

	return $script;
}