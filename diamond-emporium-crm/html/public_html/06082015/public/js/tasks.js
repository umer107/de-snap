function fetchTaskInfo(id, individual){
	$('.pageLoader').show();
	var url = '/gettaskdetails';
	var data = 'taskId='+id;
	$.ajax({
			type: 'POST',
			url: url,
			//async: false,
			data: data,
			success: function(response){
				$('#tasksRightGrid').show();
				var taskData = JSON.parse(response);
				$('#taskTitleRightGrid').html(taskData.task_title);
				$('#currentTaskId').val(taskData.main_task_id);
				$('#taskHead'+id).text(taskData.task_title);
				$('#task_category').val(taskData.task_category);
				$('#task_category').dropkick('refresh');
				$('#task_subject').val(taskData.task_subject);
				$('#task_subject').dropkick('refresh');
				$('#task_priority').val(taskData.task_priority);
				$('#task_priority').dropkick('refresh');
				
				//$('input[name=due_date]').val(taskData.due_date);
				
				var thirdColHtml = '';
				
				if(taskData.due_date){
					if(taskData.is_overdue == 1)
						thirdColHtml = '<span class="datePosted newDatePosted overDue">'+taskData.due_date+'</span>';
					else
						thirdColHtml = '<span class="datePosted newDatePosted">'+taskData.due_date+'</span>';
					$('.taskpicker').text(taskData.due_date);
				}
				
				if(taskData.task_assigned_fullname){
					$('.dropValue').html('<span>'+taskData.task_assigned_shortname+'</span>'+taskData.task_assigned_fullname);
					thirdColHtml += '<span class="assignTo">'+taskData.task_assigned_shortname+'</span>';
				} else {
					$('.dropValue').html('Unassigned');
				}
				
				$('#task_'+taskData.main_task_id+' .thirdCol').html(thirdColHtml);
				
				if(individual){
					fetchLatestTaskHistory(id);
				} else {
					fetchTaskHistory(id);
				}
				
				$('.pageLoader').hide();
			}
		});
}

function closeCalender(){
	$('.datePickerPopup').appendTo('.tasksBlock');
	$('.datePickerPopup').hide();
}

function UpdateTaskParam(id, modColumn, modColumnValue){
	//$('.pageLoader').show();
	var url = '/updateindividual';
	var data = 'taskId='+id+'&column_title='+modColumn+'&column_value='+modColumnValue;
	
	if(modColumn == 'due_date' && (modColumnValue != 'Urgent' && modColumnValue != 'No Due Date')){
		data += '&repeat='+$('#repeat option:selected').val();
		if($('#endon_date').val() != '')
			data += '&endon_date='+$('#endon_date').val();
			
		$('#'+id+'_due_date').val($('#due_date').val());
		$('#'+id+'_repeat').val($('#repeat option:selected').val());
		$('#'+id+'_endon_date').val($('#endon_date').val());
		closeCalender();
	}else if(modColumn == 'due_date' && (modColumnValue == 'Urgent' || modColumnValue == 'No Due Date')){
		$('#'+id+'_due_date').val(modColumnValue);
		$('#'+id+'_repeat').val('');
		$('#'+id+'_endon_date').val('');
		closeCalender();
	}
	
	if(modColumn == 'task_title' && $.trim(modColumnValue) == ''){
		fetchTaskInfo(id, true);
	}else{
		$.ajax({
			type: 'POST',
			url: url,
			async: false,
			data: data,
			success: function(response){
				if(response > 0){					
					fetchTaskInfo(id, true);
					//$('.pageLoader').hide();
				}
			}
		});
	}
}

function fetchTaskHistory(id){
	$('.pageLoader').show();
	var url = '/gettaskhistorydetails';
	var data = 'taskId='+id;
	$.ajax({
			type: 'POST',
			url: url,
			//async: false,
			data: data,
			success: function(response){
				var taskHistoryData = JSON.parse(response);
				$('#historyMainDataDiv').html('');
				
				//alert(taskHistoryData.length);
				
				//for(i=0; i < taskHistoryData.length; i++){
				$.each(taskHistoryData, function (){
					var divData = '';
					var assignName = '';
					var category_title = '';
					var subject_title = '';
					var priority_title = '';
					if(this.task_assigned_fullname != null){
						var assignName = '<span class="assignTo">'+this.task_assigned_shortname+'</span> '+this.task_assigned_fullname;
					}
					if(this.category_title != null){
						var category_title = this.category_title
					}
					if(this.subject_title != null){
						var subject_title = this.subject_title;
					}
					if(this.priority_title != null){
						var priority_title = this.priority_title;
					}
					divData = fillRightBlockWithHistory(this.id, this.task_id, this.metadata, this.data, this.task_created_fullname, this.createdDate, this.files, assignName, category_title, subject_title, priority_title);
					$('#historyMainDataDiv').append(divData);					
				});
				$('.pageLoader').hide();
			}
		});
}

function fetchLatestTaskHistory(id){
	
	$('.pageLoader').show();
	var url = '/gettaskhistorydetails';
	var data = 'taskId='+id+'&type=Latest';
	$.ajax({
			type: 'POST',
			url: url,
			//async: false,
			data: data,
			success: function(response){
				var taskHistoryData = JSON.parse(response);
				//for(i=0; i < taskHistoryData.length; i++){
					var divData = '';
					var assignName = '';
					var category_title = '';
					var subject_title = '';
					var priority_title = '';
					if(taskHistoryData.task_assigned_fullname != null){
						var assignName = '<span class="assignTo">'+taskHistoryData.task_assigned_shortname+'</span> '+taskHistoryData.task_assigned_fullname;
					}
					if(taskHistoryData.category_title != null){
						var category_title = taskHistoryData.category_title
					}
					if(taskHistoryData.subject_title != null){
						var subject_title = taskHistoryData.subject_title;
					}
					if(taskHistoryData.priority_title != null){
						var priority_title = taskHistoryData.priority_title;
					}
					divData = fillRightBlockWithHistory(taskHistoryData.id, taskHistoryData.task_id, taskHistoryData.metadata, taskHistoryData.data, taskHistoryData.task_created_fullname, taskHistoryData.createdDate, taskHistoryData.files, assignName, category_title, subject_title, priority_title);
					$('#historyMainDataDiv').prepend(divData);
					
					$('.pageLoader').hide();
				//}
			}
		});
}

function fillRightBlockWithHistory(historyId, taskId, columnTitle, columnValue, createdBy, createdDate, attachments, assignName, category_title, subject_title, priority_title){
	
	if(columnTitle == 'comment'){
		
		var matches = createdBy.match(/\b(\w)/g);
		var initial = matches.join('');
		
		var htmlData = '<div class="commentData" id="div_comment_'+historyId+'"><div class="editDrop"><span class="editTaskBtn"></span><ul><li onclick="prepareEditComment('+historyId+');">Edit</li><li onclick="deleteComment('+historyId+');">Delete</li></ul></div><div class="cb"></div><div class="commentedOne"><span>'+initial+'</span>'+createdBy+'</div><p class="prevPost" id="text_comment_'+historyId+'">'+columnValue+'</p><textarea id="input_comment_'+historyId+'" class="commentTextarea" onblur="editComment('+historyId+', $(\'#input_comment_'+historyId+'\').val());" style="display:none;">'+columnValue+'</textarea><div class="attachedImages">';
				
		//var htmlData = '<div class="prevComments"><div class="editDrop"> <span class="editTaskBtn"></span><ul><li>Edit</li><li>Delete</li></ul></div><div class="cb"></div><div class="commentedOne"><span>MD</span>'+createdBy+'</div><p class="prevPost">'+columnValue+'</p><div class="attachedImages">';
		var files = new Array();
		if(attachments){
			files = attachments.split(',');

			for(i = 0; i < files.length; i++){
				
				var file = files[i];
				
				var ext = file.split('.').pop().toLowerCase();
				if($.inArray(ext, ['gif','png','jpg','jpeg']) > 0) {
					htmlData += '<div class="attachmentDisplay"><a href="/downloadattachment/'+historyId+'/'+file+'"><img src="/comment_attachments/'+historyId+'/'+file+'" /></a></div>';
				}else if($.inArray(ext, ['doc', 'docx']) > 0){
					htmlData += '<div class="attachmentDisplay"><a href="/downloadattachment/'+historyId+'/'+file+'"><img src="/images/docImg.png" /><span>'+file+'</span></a></div>';
				}else{
					htmlData += '<div class="attachmentDisplay"><a href="/downloadattachment/'+historyId+'/'+file+'"><img src="/images/zipIcon.png" /><span>'+file+'</span></a></div>';
				}
			}
			
			files = new Array();
		}
		
		htmlData += '<div class="cb"></div><div class="datePosted">'+createdDate+'</div></div></div>';
	}else if(columnTitle == 'assigned_to'){
		if(columnValue == 0){
			var htmlData = '<div class="historyData"> <span class="commentPrioruty">Unassigned</span><div class="transitionText">by <strong>'+createdBy+'</strong><span class="dotseparator">.</span><time class="timestamp" >'+createdDate+'</time></div></div>';
		} else {
		var htmlData = '<div class="historyData"> <span class="commentPrioruty">'+assignName+'</span><div class="transitionText">by <strong>'+createdBy+'</strong><span class="dotseparator">.</span><time class="timestamp" >'+createdDate+'</time></div></div>';
		}
	}else if(columnTitle == 'task_category'){
		if(columnValue > 0){
			var htmlData = '<div class="historyData"> <span class="commentPrioruty">'+category_title+'</span><div class="transitionText">by <strong>'+createdBy+'</strong><span class="dotseparator">.</span><time class="timestamp" >'+createdDate+'</time></div></div>';
		}
	}else if(columnTitle == 'task_subject'){
		if(columnValue > 0){
			var htmlData = '<div class="historyData"> <span class="commentPrioruty">'+subject_title+'</span><div class="transitionText">by <strong>'+createdBy+'</strong><span class="dotseparator">.</span><time class="timestamp" >'+createdDate+'</time></div></div>';
		}
	}else if(columnTitle == 'task_priority'){
		if(columnValue > 0){
			var htmlData = '<div class="historyData"> <span class="commentPrioruty">'+priority_title+'</span><div class="transitionText">by <strong>'+createdBy+'</strong><span class="dotseparator">.</span><time class="timestamp" >'+createdDate+'</time></div></div>';
		}
	} else if(columnTitle == 'task_title'){
		var htmlData = '';
	} else {
		var htmlData = '<div class="historyData"> <span class="commentPrioruty">'+columnValue+'</span><div class="transitionText">by <strong>'+createdBy+'</strong><span class="dotseparator">.</span><time class="timestamp" >'+createdDate+'</time></div></div>';
	}
	return htmlData;
}

function saveComment(){

	$('.pageLoader').show();
	var url = '/savecomment';
	var data = {task_comment: $('#task_comment').val(), task_id: $('#currentTaskId').val()};
	$('#task_comment').val('');
	$.ajax({
		type: 'POST',
		url: url,
		async: false,
		data: data,
		success: function(response){
			if(response && selectedFiles.length > 0){
				$('#file_upload').uploadify('settings', 'formData', {'comment_id': response});
				$('#file_upload').uploadify('upload', '*');
				$('#file_upload').uploadify('settings', 'onQueueComplete', function(queueData){
					
					var filesUploaded = '';
					for(i=0;i<files.length;i++){
						filesUploaded += files[i]+',';
					}
					
					var url = '/saveattachments';
					var data = {'comment_id': response, files: filesUploaded};
					$.ajax({
						type: 'POST',
						url: url,
						async: false,
						data: data,
						success: function(response){
							if(response){			
								fetchLatestTaskHistory($('#currentTaskId').val());
								$('.pageLoader').hide();
							}
							files = new Array();
							selectedFiles = new Array();
						}
					});
				});
			}else{
				fetchLatestTaskHistory($('#currentTaskId').val());
			}
		}
	});
}

function changeTaskStatus(task_id, checkbox){
	$('.pageLoader').show();
	//$(checkbox).prop('checked', false); 
	var url = '/changetaskstatus';
	var data = {'task_id': task_id};
	$.ajax({
		type: 'POST',
		url: url,
		//async: false,
		data: data,
		success: function(response){
			//var jsResponse = JSON.parse(response);
			if(response == 1){				
				jQuery("#task_"+task_id).detach().prependTo('div .tasksList');
				$('#closed_task_count').text(parseInt($('#closed_task_count').text())-1);
				//alert('**'+$('#closed_task_count').text());
			}else{
				jQuery("#task_"+task_id).detach().prependTo('div .completedTasks .toggleInfo');
				$('#closed_task_count').text(parseInt($('#closed_task_count').text())+1);
				//alert('##'+$('#closed_task_count').text());
			}			
			
			$('.pageLoader').hide();
		}
	});
}

function deleteTask(task_id, entity_id, assigned_for){
	$('.pageLoader').show();
	var url = '/deletetask';
	var data = {task_id: task_id, entity_id: entity_id, assigned_for: assigned_for};
	$.ajax({
		type: 'POST',
		url: url,
		data: data,
		success: function(response){
			var jsResponse = JSON.parse(response);
			if(jsResponse.taskIsdeleted){
				$('#tab_task_count').text(parseInt(jsResponse.countOpenedTasks) + parseInt(jsResponse.countClosedTasks));
				$('#closed_task_count').text(jsResponse.countClosedTasks);
				$('#task_'+task_id).remove();
				$('#tasksRightGrid').hide();
			}
			$('.pageLoader').hide();
		}
	});
}

function prepareEditComment(comment_id){
	$('#text_comment_'+comment_id).hide();
	$('#div_comment_'+comment_id).find('.editDrop').trigger('click');
	$('#input_comment_'+comment_id).show();
	$('#input_comment_'+comment_id).focus();
}

function editComment(comment_id, comment){
	$('.pageLoader').show();
	var url = '/editcomment';
	var data = {comment_id: comment_id, comment: comment};
	$.ajax({
		type: 'POST',
		url: url,
		data: data,
		success: function(response){
			//if(response == 1){
				$('#text_comment_'+comment_id).text(comment);
				$('#text_comment_'+comment_id).show();
				$('#input_comment_'+comment_id).hide();
			//}
			$('.pageLoader').hide();
		}   
	});
}

function deleteComment(comment_id){
	$('.pageLoader').show();
	var url = '/deletecomment';
	var data = {comment_id: comment_id};
	$.ajax({
		type: 'POST',
		url: url,
		data: data,
		success: function(response){
			if(response == 1){
				$('#div_comment_'+comment_id).remove();
			}
			$('.pageLoader').hide();
		}   
	});
}

function selectTask(task_id){
	$('.listView').removeClass('current');	
	$('#task_'+task_id).addClass('current');
}

function resetCalender(){
	$('#due_date_option').hide();
	$('#due_date').val('');
	$("#repeat option").filter(function() {
		return $(this).val() == 1; 
	}).prop('selected', true);
	$('#endon').removeAttr('checked', 'checked');
	$('#endon_date').val('');
	$('#endon_date').attr('readonly', 'readonly');
	$('#urgent').removeAttr('disabled');
	$('#urgent').removeClass('active');
	$('#noduedate').removeAttr('disabled');
	$('#noduedate').removeClass('active');
	$('#save_due_date').attr('disabled', 'disabled');
	g_globalObject.unsetSelection();
	//g_globalObject.datePatternSelected($('#repeat option:selected').val(), '');	
	//populateCalender();
}

function populareDueDateForm(task_id){	
	resetCalender();
	
	if($('#'+task_id+'_due_date').val() == 'Urgent'){
		$('#urgent').attr('disabled', 'disabled');
		$('#urgent').addClass('active');
		$('#noduedate').removeAttr('disabled');
		$('#noduedate').removeClass('active');
		$('#due_date_option').hide();
	}else if($('#'+task_id+'_due_date').val() == 'No Due Date'){
		$('#urgent').removeAttr('disabled');
		$('#urgent').removeClass('active');
		$('#noduedate').attr('disabled', 'disabled');
		$('#noduedate').addClass('active');
		$('#due_date_option').hide();
	}else if($('#'+task_id+'_due_date').val() != ''){
		
		$("#repeat option").filter(function() {
			return $(this).val() == $('#'+task_id+'_repeat').val(); 
		}).prop('selected', true);
		
		var endOnDate = '';
		
		if($('#'+task_id+'_endon_date').val() != ''){
			$('#endon').attr('checked', 'checked');
			$('#endon_date').removeAttr('readonly');
			$('#endon_date').val($('#'+task_id+'_endon_date').val());
			
			//var dateArr = $('#endon_date').val().split('/');
			//endOnDate = new Date(dateArr[2], dateArr[1] - 1, dateArr[0]);
		}

		$('#due_date_option').show();
		
		var dueDate = $('#'+task_id+'_due_date').val();
		var dueDateArr = dueDate.split('/');
		
		g_globalObject.setSelectedDay({
			year:dueDateArr[2],
			month:dueDateArr[1],
			day:dueDateArr[0]
		});
		//g_globalObject.datePatternSelected($('#repeat option:selected').val(), endOnDate);
	}
	
	$('#due_date').val($('#'+task_id+'_due_date').val());
	populateCalender();
}

function manageEndDate(){
	if($('#endon').is(':checked') && $('#repeat option:selected').val() > 1){
		$('#endon_date').removeAttr('readonly');
	}else{
		$('#endon').removeAttr('checked');
		$('#endon_date').attr('readonly', 'readonly');
	}
	
	$('#save_due_date').removeAttr('disabled');
}

function validateEndOnDate(date){
	if($('#endon').is(':checked') && date != ''){
		if(!checkDate(date)){
			$('#endon_date').val('');
		}else{
			$('#save_due_date').removeAttr('disabled');
			populateCalender();			
		}
	}
}

function populateCalender(){
	var date = $('#endon_date').val();
	var endOnDate = 0;
	if(date){
		var dateArr = date.split('/');
		endOnDate = new Date(dateArr[2], dateArr[1] - 1, dateArr[0]);
	}
	
	g_globalObject.datePatternSelected($('#repeat option:selected').val(), endOnDate);
}

$(document).on('click', '.creatTaskBtn', function(){
	$(this).siblings('.createTaskInput').show().focus().end().hide();
});

$(document).on('blur', '.createTaskInput', function(){
	createTask();
});

function createTask(){
	if($.trim($('input[name=task_title]').val()) != ''){
		var url = '/createtask';
		//var data = $('#frm_create_task').serialize();
		var data = {entity_type: $('input[name=entity_type]').val(), entity_id: $('input[name=entity_id]').val(), task_title: $('input[name=task_title]').val()};
		$.ajax({
			type: 'POST',
			url: url,
			async: false,
			data: data,
			success: function(response){
				if(response){
					//alert("Task created successfully");
					$('.lightBoxTitle .closePopup').click();						
					
					var task = JSON.parse(response);						
					
					var html = '<div id="task_'+task.task_id+'" class="listView"><input type="hidden" name="'+task.task_id+'_due_date" id="'+task.task_id+'_due_date" value="No Due Date" /><input type="hidden" name="'+task.task_id+'_repeat" id="'+task.task_id+'_repeat" value="" /><input type="hidden" name="'+task.task_id+'_endon_date" id="'+task.task_id+'_endon_date" value="" /><div class="firstCol"><label class="labelCheck"><input type="checkbox" onchange="changeTaskStatus('+task.task_id+', this);" /><i></i></label></div><div class="secondCol"><h3 id="taskHead'+task.task_id+'" class="taskHead" onclick="fetchTaskInfo('+task.task_id+');">'+task.task_title+'<br><span>Opportunity name</span></h3></div><div class="thirdCol"> </div></div>';
					
					$('div .tasksList').prepend(html);
					$('#tab_task_count').text(parseInt($('#tab_task_count').text())+1);
				}
			}
		});
	}
	$('input[name=task_title]').val('');
	$('.createTaskInput').siblings('.creatTaskBtn').show().end().hide();
}


