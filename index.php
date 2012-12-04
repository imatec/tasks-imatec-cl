<?php
/*
 * Copyright 2011 Google Inc.
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *     http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */
session_start();
require_once 'google-api-php-client/src/apiClient.php';
require_once 'google-api-php-client/src/contrib/apiTasksService.php';
require_once 'google-api-php-client/src/contrib/apiOauth2Service.php';
require_once 'safarilib.php';
error_reporting(E_ALL);
ini_set('display_errors', '1');
$client = new apiClient();
$client->setApplicationName('imatec-tasks01');
switch ($_SERVER["SERVER_NAME"]) {
	case 'http://tasks.imatec.cl':
		$client->setClientId('65558362391.apps.googleusercontent.com');
		$client->setClientSecret('0b4Z8Uc2PtUmhR6ASgeAFvsy');
		break;
	case 'http://tasks-dev.imatec.cl':
		$client->setClientId('65558362391-dmrhm6ndt1m1lb7i038rbscj7smf8pvt.apps.googleusercontent.com');
		$client->setClientSecret('sZSOctuqwIF-aRESMdZPDY7a');
		break;
}
$client->setRedirectUri($_SERVER["SERVER_NAME"]);
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
?>
<!doctype html>
<html>
<head>
  <title>Tasks (ImaTec/Google)</title>

  <link rel='stylesheet' href='http://fonts.googleapis.com/css?family=Droid+Serif|Droid+Sans:regular,bold' />
  <!--<link rel='stylesheet' href='css/style_1.css' />-->
  <link rel='stylesheet' href='css/bootstrap.min.css' />
  <link rel="stylesheet" href="css/bootstrap-responsive.css"/>
  <link rel='stylesheet' href='css/datepicker.css' />
  <link rel='stylesheet' href='css/ImatecGoogleTasks.css' />
      <style type="text/css">
      body {
        padding-top: 60px;
        padding-bottom: 40px;
      }
      .sidebar-nav {
        padding: 9px 0;
      }
    </style>
    <script src="js/jquery-1.8.0.min.js"></script>
    <script src="js/bootstrap.js"></script>
    <script src="js/bootstrap-datepicker.js"></script>
    <script src="js/index.js"></script>
</head>
<body>
  <div class="navbar navbar-inverse navbar-fixed-top">
      <div class="navbar-inner">
        <div class="container-fluid">
          <a class="btn btn-navbar" data-toggle="collapse" data-target=".nav-collapse">
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
          </a>
          <a class="brand" href="#">ImaTec/Google Tasks HOLA</a>
          <div class="nav-collapse collapse">
            <p class="navbar-text pull-right">
              Logged in as <a href="#" class="navbar-link"><?=$response->name?> (<?=$response->email?>)</a>
	      &nbsp;<a href="?logout=1" class="navbar-link"><i class="icon-off icon-white"></i></a>
            </p>
            <ul class="nav">
            <!--  <li class="active"><a href="#">Home</a></li>
              <li><a href="#about">About</a></li>
              <li><a href="#contact">Contact</a></li> -->
            </ul>
          </div><!--/.nav-collapse -->
        </div>
      </div>
    </div>

    <div class="container-fluid">
      <div class="row-fluid">
	  
        <div class="span3">
          <div class="well sidebar-nav">

            <ul class="nav nav-list" id="listadelistas">
			  <?php include('displayTasksLists.php'); ?>

            </ul>
          </div><!--/.well -->
        </div><!--/span-->
	
        <div class="span9">
          <!--<div class="hero-unit" id="lasListas">-->
	  <div class="hide well" id="testArea"><!--<span class="taskDueDate datepicker">datepickerTest</span>--> <input type="text" class="taskDueDate datepicker"></div>
          <div class="well" id="addTaskArea">
			  <input id="newTask" placeholder="ingresa la nueva tarea y presiona ENTER" type="text" class="input-block-level">
          </div>
          <div class="well" id="lasListas">

          </div>
        </div><!--/span-->
      </div><!--/row-->

      <hr>

      <footer>
        <p>&copy; ImaTec Comp. SpA 2012</p>
      </footer>

    </div><!--/.fluid-container-->
</body>
</html>
<?php $_SESSION['access_token'] = $client->getAccessToken(); ?>