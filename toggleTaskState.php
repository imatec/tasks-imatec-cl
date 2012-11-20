<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', '1');
require_once 'safarilib.php';
require_once 'google-api-php-client/src/apiClient.php';
require_once 'google-api-php-client/src/contrib/apiTasksService.php';
require_once 'google-api-php-client/src/contrib/apiOauth2Service.php';

$client = new apiClient();
$client->setApplicationName('imatec-tasks01');
$client->setClientId('65558362391.apps.googleusercontent.com');
$client->setClientSecret('0b4Z8Uc2PtUmhR6ASgeAFvsy');
$client->setRedirectUri('http://tasks.imatec.cl/');
$client->setDeveloperKey('AIzaSyB_eCQw6ZAJbw4_okmk-VRxuzn5UB1r2Vw');
$tasksService = new apiTasksService($client);

if (isset($_SESSION['access_token'])) {
  $client->setAccessToken($_SESSION['access_token']);
} else {
  $client->setAccessToken($client->authenticate());
  $_SESSION['access_token'] = $client->getAccessToken();
}

$task = new Task();

if($_REQUEST['taskStatus'] == 'needsAction'){
    $newStatus = 'completed';
}elseif($_REQUEST['taskStatus'] == 'completed'){
    $newStatus = 'needsAction';
}

if($_REQUEST['taskId']){
    $task->setId($_REQUEST['taskId']);
    if($_REQUEST['taskStatus']){
	$task->setStatus($newStatus);
	$tasksService->tasks->update($_REQUEST['taskListId'], $_REQUEST['taskId'], $task);
    }
}

?>