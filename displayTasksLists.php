<?php

$encode = isset($_SESSION);

function encodingSelection($string, $doEncode){
	if($doEncode){
		return utf8_decode($string);
	}else{
		return $string;
	}
}
		
if(!isset($_SESSION)):
	session_start();
	require_once 'google-api-php-client/src/apiClient.php';
	require_once 'google-api-php-client/src/contrib/apiTasksService.php';
	require_once 'google-api-php-client/src/contrib/apiOauth2Service.php';
	require_once 'safarilib.php';
	error_reporting(E_ALL);
	ini_set('display_errors', '1');
	$client = new apiClient();
	$client->setApplicationName('imatec-tasks01');
	$client->setClientId('65558362391.apps.googleusercontent.com');
	$client->setClientSecret('0b4Z8Uc2PtUmhR6ASgeAFvsy');
	$client->setRedirectUri('http://tasks.imatec.cl/');
	$client->setDeveloperKey('AIzaSyB_eCQw6ZAJbw4_okmk-VRxuzn5UB1r2Vw');
	$tasksService = new apiTasksService($client);
	$oauth2Service = new apiOauth2Service($client);
	$userInfo = new Userinfo();

	if (isset($_REQUEST['logout'])) {
	unset($_SESSION['access_token']);
	}

	if (isset($_SESSION['access_token'])) {
	$client->setAccessToken($_SESSION['access_token']);
	} else {
	$client->setAccessToken($client->authenticate());
	$_SESSION['access_token'] = $client->getAccessToken();
	}

	if (isset($_GET['code'])) {
	$redirect = 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['PHP_SELF'];
	header('Location: ' . filter_var($redirect, FILTER_SANITIZE_URL));
	}

	$client->setScopes('https://www.googleapis.com/auth/userinfo.profile');
	$request = new apiHttpRequest("https://www.googleapis.com/oauth2/v1/userinfo?alt=json");
	$userinfo = $client->getIo()->authenticatedRequest($request);
	$response = json_decode($userinfo->getResponseBody());
endif;
	
$lists = $tasksService->tasklists->listTasklists();
	?><li class="nav-header">Listas</li><?
foreach ($lists['items'] as $list) {
	$tasks = $tasksService->tasks->listTasks($list['id']);
	$listItemsCount = count($tasks['items']);
	?><li><a href="#" listId="<?=$list['id']?>" class="tasklist"><i class="icon-list icon-grey"></i> <?= encodingSelection($list['title'],$encode)?> <span class="taskCount">(<?=$listItemsCount?>)</span></a></li><?
}	 