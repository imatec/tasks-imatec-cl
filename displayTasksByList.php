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

$tasks = $tasksService->tasks->listTasks($_REQUEST['id']);
function printTask($task, $level=0){
    if($task['status'] == 'needsAction'){
	$iconClass = 'icon-hand-right';
	$inputClass = '';
    }elseif($task['status'] == 'completed'){
	$iconClass = 'icon-thumbs-up';
	    $inputClass = 'taskCompleted';
    }
    $inputPaddingLeft = $level*30;
    $inputWidth = 400 - $inputPaddingLeft;
?>
<tr class="taskContainer <?=$inputClass?>" >
    <td></td>
    <td style="margin-left: <?=$inputPaddingLeft?>px; border-bottom: 1px #0480be dashed" ><i class="<?=$iconClass?> taskStatusToggler pull-left" style="margin-right: 10px"></i>
	<div contenteditable="true" taskId="<?=$task['id']?>" taskStatus="<?=$task['status']?>" class="taskTitleHolder"><?=$task['title']?></div>
    </td>
    <td>
		<div class="pull-right">
			<i class="icon-calendar icon-gray taskDueDate" style="margin-top: 2px"></i>
			<a href="#deleteTaskModal" data-toggle="modal" taskId="<?=$task['id']?>" class="taskDelete"><i class="icon-trash icon-gray" style="margin-top: 2px"></i></a>
		</div>
    </td>
</tr>
<?
}
?>
<input type="hidden" value="<?=$_REQUEST['id']?>" id="taskListId">

<div>

</div>

<table style="width: 100%">
    <colgroup>
	<col width="5px">
	<col width="*">
	<col width="60px">
    </colgroup>
    <tbody id="taskList">
    
    <tr class="newCreated taskContainer hide">
	<td></td>
	<td style="margin-left: 0px;"><i class="icon-hand-right taskStatusToggler pull-left" style="margin-right: 10px"></i>
	    <div contenteditable="true" taskId="" taskStatus="needsAction" class="taskTitleHolder"></div>
	</td>
	<td>
	    <div class="pull-right">
			<i class="icon-calendar icon-gray taskDueDate" style="margin-top: 2px"></i>
			<a href="#deleteTaskModal" data-toggle="modal" taskId="" ><i class="icon-trash icon-gray taskDelete" style="margin-top: 2px"></i></a>
	    </div>
	</td>
    </tr>
<?
foreach($tasks['items'] as $task0){
	if(!isset($task0['parent'])){
	    printTask($task0);
	    reset($tasks['items']);
	    foreach($tasks['items'] as $task1){
		if(isset($task1['parent']) && $task1['parent'] == $task0['id']){
		    printTask($task1,1);
		    reset($tasks['items']);
		    foreach($tasks['items'] as $task2){
			if(isset($task2['parent']) && $task2['parent'] == $task1['id']){
			    printTask($task2,2);
			    reset($tasks['items']);
			    foreach($tasks['items'] as $task3){
				if(isset($task3['parent']) && $task3['parent'] == $task2['id']){
				    printTask($task3,3);
				    reset($tasks['items']);
				    foreach($tasks['items'] as $task4){
					if(isset($task4['parent']) && $task4['parent'] == $task3['id']){
					    printTask($task4,4);
					}
				    }
				}
			    }	    
			}
		    }
		}
	    }
	}
    }
?>
    </tbody>
</table>
<div class="modal hide" id="deleteTaskModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
    <h3 id="myModalLabel">Eliminar Tarea</h3>
  </div>
  <div class="modal-body">
    <p>Desea eliminar la tarea:</p>
    <p></p>
    <span id="taskTitleForDelete"></span>
    <input type="hidden" id="taskIdForDelete"></input>
  </div>
  <div class="modal-footer">
    <button class="btn" data-dismiss="modal" aria-hidden="true">Cancelar</button>
    <button class="btn btn-primary" id="deleteTaskButton">Eliminar</button>
  </div>
</div>