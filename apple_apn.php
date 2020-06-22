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

function notify_apn($to, $appstore) {
	global $apn_passphrase, $apn_pem;

	// Put your alert message here:
	$message = 'Mesibo push notification!';

	$ctx = stream_context_create();
	stream_context_set_option($ctx, 'ssl', 'local_cert', $apn_pem);
	stream_context_set_option($ctx, 'ssl', 'passphrase', $apn_passphrase);

	$server = "gateway.sandbox.push.apple.com";
	if($appstore)
		$server = "gateway.push.apple.com";

	// Open a connection to the APNS server
	$fp = stream_socket_client("ssl://$server:2195", $err, $errstr, 60, STREAM_CLIENT_CONNECT|STREAM_CLIENT_PERSISTENT, $ctx);

	if (!$fp) {
		return true; //false will delete token
	}
		
	// Create the payload body
	$body['aps'] = array(
			'content-available' => '1',
			//	'alert' => $message,
			//	'sound' => 'default'
			);

	// Encode the payload as JSON
	$payload = json_encode($body);

	// This is legacy format, we MUST move to HTTP/2 format to get response etc 
	// Build the binary notification (legacy format). 
	// 0, 32 for device token (to) len, token, payload len, payload
	$msg = chr(0) . pack('n', 32) . pack('H*', $to) . pack('n', strlen($payload)) . $payload;

	// Send it to the server
	$result = fwrite($fp, $msg, strlen($msg));

	// Close the connection to the server
	fclose($fp);

	if(FALSE == $result)
		return false;

	return true;
}
