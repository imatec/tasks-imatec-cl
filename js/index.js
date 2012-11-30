$(document).ready(function(){
    $("a.tasklist").click(function(event){
	$("a.tasklist").parent().removeClass('active');
	$(this).parent().addClass('active');
	showTaskList($(this).attr('listId'));
    });
	
    $(document).on('keypress','div.taskTitleHolder', function(e){
		if(e.which == 13 && $.trim($(this).text()) != '') {
			var newRow = $('tbody#taskList tr:first').clone();
			//newRow.show();   
			var e = jQuery.Event("keydown");
			e.which = 8; // # Some key code value
			//$(this).parent().parent().after(newRow).trigger(e);
			newRowMap(newRow).insertAfter($(this).parent().parent()).find('.taskTitleHolder').focus();
			/*
			$(this).parent().parent().after(newRowMap(newRow));
			$(this).parent().parent().next().find('.taskTitleHolder').focus().text('text');
			$(this).parent().parent().next().find('.taskTitleHolder').focus();*/
			//$(this).parent().parent().next().find('.taskTitleHolder').children().remove();
			//$(this).parent().parent().next().find('.taskTitleHolder').trigger(e);
			/*COmentando*/
		}	
    });
	$(document).on('keypress', '#newTask', function(e){
		if(e.which == 13 && $.trim($(this).val()) != ''){
			$.get('addTask.php', {'name' : $(this).val(), 'taskListId' : $(this).attr('taskListId')}, function(){
				console.log('asdajsdhjashdkajsh');
				$('#listadelistas').load('displayTasksLists.php');
			});
		}
	});
    $(document).on('blur','div.taskTitleHolder', function(){
	    if($.trim($(this).text()) != ''){
			$.post('addTask.php',{'taskTitle' : $(this).text(), 'taskListId' : $('input#taskListId').val(), 'taskId' : $(this).attr('taskId')});  
	    }else{
			$(this).parent().parent().remove();
	    }
		$('#listadelistas').load('displayTasksLists.php');
		
    });    
    $(document).on('click','i.taskStatusToggler',function(){
		$.post('toggleTaskState.php', {'taskTitle' : $(this).next().text(), 'taskListId' : $('input#taskListId').val(), 'taskId' : $(this).next().attr('taskId'), 'taskStatus' : $(this).next().attr('taskStatus')});
		$(this).toggleClass('icon-thumbs-up');
		$(this).toggleClass('icon-hand-right');
		$(this).parent().parent().toggleClass('taskCompleted');
		showTaskList($('input#taskListId').val());
    });

    $(document).on('hover','.taskContainer',function(){
		$(this).find('.taskDelete i, .taskDueDate').toggleClass('icon-gray');
    });

    $(document).on('click','.taskDelete',function(){
		$('#taskTitleForDelete').html($(this).parent().parent().prev().find('.taskTitleHolder').text());
		//$('#taskIdForDeleteShow').html($(this).attr('taskId'));
		$('#taskIdForDelete').val($(this).attr('taskId'));
    });

    $(document).on('click','#deleteTaskButton',function(){
		$.post('deleteTask.php', {'taskListId' : $('input#taskListId').val(), 'taskId' : $('#taskIdForDelete').val()}, function(){
			$('#deleteTaskModal').modal('hide');
			$('[taskId='+$('#taskIdForDelete').val()+']').parent().parent().fadeOut().remove();
		});
    });
	
	$(document).on('click','.tasklist', function(){
		showTaskList($(this).attr('listid'));
	});
	
	$('.taskDueDate').datepicker();
	
});

function newRowMap(newRow){
	console.log(newRow.html());
	newRow.removeClass('hide');
	newRow.removeClass('newCreated');
	
	return newRow;
}
function showTaskList(listId){
    $('#lasListas').load('displayTasksByList.php', {id : listId}, function(){
	$("a#addButton").click(function(){
	    $("#testArea").load('addTask.php',{'name' : 'nombre prueba', 'taskListId' : $(this).attr('taskListId')});
	});

    });    
}