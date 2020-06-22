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

function doCurl($url) {
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	$data = json_decode(curl_exec($ch), true);
	curl_close($ch);
	return $data;
}

function ak_getphone_from_authcode($auth_code) {
	global $ak_appid, $ak_secret;

	// Exchange authorization code for access token
	$token_exchange_url = 'https://graph.accountkit.com/'.$ak_version.'/access_token?'.
		'grant_type=authorization_code'.
		'&code='.$auth_code.
		"&access_token=AA|$ak_appid|$ak_secret";
	$data = doCurl($token_exchange_url);
	if(!$data || isset($data['error']) || !isset($data['access_token'])) {
		return false;
	}

	$user_id = $data['id'];
	$user_access_token = $data['access_token'];
	$refresh_interval = $data['token_refresh_interval_sec'];
	return ak_getphone_from_accesstoken($user_access_token);
	return true;
}

function ak_getphone_from_accesstoken($token) {
	global $ak_version;
	// Get Account Kit information
	$me_endpoint_url = 'https://graph.accountkit.com/'.$ak_version.'/me?'.
		'access_token='.$token;
	$data = doCurl($me_endpoint_url);
	if($data && isset($data['phone'])) {
		$p = array();
		$p['code'] = $data['phone']['country_prefix'];
		$p['number'] = $data['phone']['national_number'];
		return $p;
	}
	return false;
}
