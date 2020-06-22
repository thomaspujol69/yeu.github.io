<?php
/** Copyright (c) 2019 Mesibo
 * https://mesibo.com
 * All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without modification,
 * are permitted provided that the terms and condition mentioned on https://mesibo.com
 * as well as following conditions are met:
 *
 * Redistributions of source code must retain the above copyright notice, this list
 * of conditions, the following disclaimer and links to documentation and source code
 * repository.
 *
 * Redistributions in binary form must reproduce the above copyright notice, this
 * list of conditions and the following disclaimer in the documentation and/or other
 * materials provided with the distribution.
 *
 * Neither the name of Mesibo nor the names of its contributors may be used to endorse
 * or promote products derived from this software without specific prior written
 * permission.
 *
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS" AND
 * ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED
 * WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE DISCLAIMED.
 * IN NO EVENT SHALL THE COPYRIGHT OWNER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT,
 * INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING,
 * BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA,
 * OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY,
 * WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE)
 * ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE
 * POSSIBILITY OF SUCH DAMAGE.
 *
 * Documentation
 * https://mesibo.com/documentation/
 *
 * Source Code Repository
 * https://github.com/mesibo/messenger-app-backend
 *
 * Android App Source code Repository
 * https://github.com/mesibo/messenger-app-android
 *
 * iOS App Source code Repository
 * https://github.com/mesibo/messenger-app-ios
 *
 */



include_once ('config.php');

function notify_gcm($to) {
	global $gcm_key;

	$url = 'https://android.googleapis.com/gcm/send';

	$priority = "high"; // or 'normal'
	$type = "data"; // or 'notification' . We use data as notification automatically notifies which is not required in our case. We inseatd set priority high

	$message = "none";
	$topic = ''; // not using, https://developers.google.com/cloud-messaging/topic-messaging
	$collapse_key = 'NEWMESSAGE'; // since we are using GCM to notify for new message and then client fetches, we can make it callopsable message (previous undelivered GCM messages to same client will be discarded)

	// there are two kinds of messages, data (default priority normal) and notification (default priority high). 
	$fields = array(
			'to' => $to,
			'priority' => $priority,
			'collapse_key' => $collapse_key,
			$type => array( "message" => $message ),
		       );

	$headers = array(
			'Authorization: key=' . $gcm_key,
			'Content-Type: application/json'
			);

	// Open connection
	$ch = curl_init();

	// Set the URL, number of POST vars, POST data
	curl_setopt( $ch, CURLOPT_URL, $url);
	curl_setopt( $ch, CURLOPT_POST, true);
	curl_setopt( $ch, CURLOPT_HTTPHEADER, $headers);
	curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
	curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode( $fields));

	// Execute post
	$result = curl_exec($ch);

	// Close connection
	curl_close($ch);

	$r = json_decode($result, 1);
	if(isset($r['failure']) && $r['failure'] > 0)
		return false;

	return true;
	//echo $result;
	//print_r($result);
	//var_dump($result);
}
