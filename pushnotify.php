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



	include_once("httpheaders.php");
	$mysqli = new mysqli ($db_host,  $db_user,  $db_pass, $db_name);
	if($mysqli->connect_errno) {
		DoExit(APIERROR_DBERROR, $result);
	}

	$db = new MysqliDb ($mysqli);
	if(1) {
		log_request($_REQUEST);
		//LogRequest("/webdata/applogs/mesibopushnotify-".date("m-Y").".txt");  
	}

	$uid=GetRequestField('uid', '');
	if($uid == '') {
		print 'ok';
		exit;
	}

	$n = dbhelper_getrow("select device, token, production from notifytokens where uid=$uid");
	if(!$n) {
		print 'oK';
		exit;
	}

	$rv = true;
	if($n['device'] == '2') {
		$prod = $n['production'];
		//error_log("Sending apple push to uid: $uid prod $prod");
		include_once("apple_apn.php");
		//$n['production'] = '1'; // temporary
		$rv = notify_apn($n['token'], $n['production']);
	} else {
		include_once("google_gcm.php");
		//error_log("Sending android push to uid: $uid");
		$rv = notify_gcm($n['token']);
	}

	if($rv) {
		echo 'OK';
	} else {
		echo 'fail';
		//delete token
	}

