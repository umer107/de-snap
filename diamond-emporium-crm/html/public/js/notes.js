function getNotes(recordsPerPage, type, id, displayActions){
	var url = '/ajaxgetnotes/'+type+'/'+id;
	// prepare the data
	var source =
	{
		datatype: "json",
		datafields: [
			{ name: 'id' },
			{ name: 'follow_up_date' },
			{ name: 'note_type' },
			{ name: 'note_description' },
			{ name: 'created_date' },
			{ name: 'created_by' },
			{ name: 'created_by_name' },
			{ name: 'modified_date' },
			{ name: 'modified_by' },
			{ name: 'grid_type' }
		],
		//localdata: data,
		//id: 'ubd_id',
		cache: false,
		url: url,
		root: 'Rows',
		sortcolumn: 'id',
		sortdirection: 'desc',
		beforeprocessing: function (data) {
			source.totalrecords = data.TotalRows;
		},
		filter: function () {
			// update the grid and send a request to the server.
			$("#jqxNotes").jqxGrid('updatebounddata', 'filter');
		}
	};
	
	var pageable;
	var dataAdapter = new $.jqx.dataAdapter(source, {
		downloadComplete: function (data, status, xhr) { },
		loadComplete: function (data) { 
			if($("#jqxNotes").find('.jqx-grid-empty-cell').length>0){
					if($("#jqxNotes").find('.jqx-grid-empty-cell').parent('div').height()<30){
						$('.jqx-grid-empty-cell').parent('div').addClass('norecordsFoundRow');
					}
					$('.jqx-grid-empty-cell').closest("#jqxNotes").addClass('noInfoFound');
					$("#jqxNotes").find('.jqx-grid-empty-cell >span').text("No records found");
					pageable = false;
			}else{
				if($("#jqxNotes").hasClass('noInfoFound')){
					$("#jqxNotes").removeClass('noInfoFound');
				}
				pageable = true;
			}
		},
		loadError: function (xhr, status, error) { }
	});
	
	var action = function (row, column, value) {
		var note = $("#jqxNotes").jqxGrid('getrowdata', row);
		if (note.grid_type == type) {
			return '<div style="text-overflow: ellipsis; text-align: left; overflow: hidden; margin-top: 11px; margin-right: 2px; margin-left: 4px; padding-bottom: 2px;"><a href="javascript:;" onclick="editNote('+note.id+');" class="gridLink">Edit</a> | <a href="javascript:;" class="gridLink" onclick="deleteNote('+note.id+', \''+type+'\');">Delete</a></div>';
		}
	}
	
	var FilteredData = new Array();
		FilteredData.push({ text: 'Date Created', type: 'string', datafield: 'created_date', width:'10%'});
		FilteredData.push({ text: 'Note Type', type: 'string', datafield: 'note_type', width:'15%'});
		FilteredData.push({ text: 'Logs/Notes', type: 'string', datafield: 'note_description', width:'45%'});
		FilteredData.push({ text: 'Follow up Date', type: 'string', datafield: 'follow_up_date', width:'10%'});
		FilteredData.push({ text: 'Action', cellsrenderer: action, width:'10%'});
		FilteredData.push({ text: 'User', type: 'string', datafield: 'created_by_name', width:'10%'});
	
	$("#jqxNotes").jqxGrid(
	{
		width: '100%',
		source: dataAdapter,
		sortable: false,
		//showfilterrow: keyword ? false : true,
		//filterable: true,
		pageable: pageable,
		pagesize: parseInt(recordsPerPage),
		pagesizeoptions: ['5', '10', '20', '50', '100'],
		autorowheight: true,
		autoheight: true,
		enabletooltips: true,
		rowsheight:40,
		columnsheight:40,
		columns:FilteredData,
		pagerheight: 50,
		//headerheight: 60,
		virtualmode: true,
		//pagermode: 'simple',
		//pager: '#gridpager',
		//columnsresize: true,
		rendergridrows: function (params) {
			return params.data;
		},
	});

	//$("#jqxNotes").next('.pagerHTML').html($('#pagerjqxNotes'));
	
}

function validateNotes(){
	var errors = 0;
	$('.errorText').remove();
	if($('.followUpDate').val() == ''){
		$( '<p class="errorText">Please select follow up date</p>' ).insertAfter( '.followUpDate' );
		errors++;
	}
	
	if($('#note_type option:selected').val() == ''){
		$( '<p class="errorText">Please select note type</p>' ).insertAfter( '#note_type' );
		errors++;
	}
	
	if($('#note_description').val() == ''){
		$( '<p class="errorText">Please enter task description</p>' ).insertAfter( '#note_description' );
		errors++;
	}
	
	return errors;
}

function saveNote(type, typeId){
	var errors = validateNotes();
	if(errors == 0){
		var follow_up_date = $('.followUpDate').val();
		var note_type = $('#note_type option:selected').val();
		var note_description = $('#note_description').val();
		var noteUpdateId = $('#noteUpdateId').val();
		var url = '/notes';
		var data = 'follow_up_date='+follow_up_date+'&note_type='+note_type+'&note_description='+note_description+'&type='+type+'&typeId='+typeId;
		if(noteUpdateId > 0){
			var data = data+'&noteUpdateId='+noteUpdateId;
		}
		$.post(url, data, function(response){
			if(response == 1){
				$('#cancelNoteButton').hide();
				$('#saveNoteButton').attr('value', 'Add Note');
				$('#jqxNotes').jqxGrid('updatebounddata');
				$('.followUpDate').val('');
				$('#note_type').val('');
				$('#note_type').dropkick('refresh');
				$('#note_description').val('');
				$('#noteUpdateId').val('');
				if(type == 'lead'){
					$('#jqxWidget').jqxGrid('updatebounddata');
				} else if(type == 'opportunity'){
					$('#jqxOpportunities').jqxGrid('updatebounddata');
				}
			}
		});
	}
}

function editNote(noteId){
	var url = '/notes';
	var data = 'noteId='+noteId;
	$.post(url, data, function(response){
		var settings = JSON.parse(response);
		$('.followUpDate').val('');
		$('#note_type').val('');
		$('#note_description').val('');
		var follow_up_date = settings.follow_up_date;
		var res = follow_up_date.replace(" 00:00:00", "");
		var resArray = res.split('-');
		var finalUpdate = resArray[2]+'/'+resArray[1]+'/'+resArray[0];
		$('.followUpDate').val(finalUpdate);
		$("#note_type").val(settings.note_type);
		$('#note_type').dropkick('refresh');
		$('#note_description').val(settings.note_description);
		$('#noteUpdateId').val(settings.id);
		$('#saveNoteButton').attr('value', 'Save Note');
		$('#cancelNoteButton').show();
	});
}

function cancelNote(){
	$('.followUpDate').val('');
	$('#note_type').val('');
	$('#note_type').dropkick('refresh');
	$('#note_description').val('');
	$('#noteUpdateId').val('');
	$('#cancelNoteButton').hide();
	$('#saveNoteButton').attr('value', 'Add Note');
}

function deleteNote(noteId, type){
	if (confirm("Are you sure you want to delete note")) {
        var url = '/notes';
		var data = 'deleteNote='+noteId;
		$.post(url, data, function(response){
			if(response == 1){
				$('#jqxNotes').jqxGrid('updatebounddata');
				$('.followUpDate').val('');
				$('#note_type').val('');
				$('#note_type').dropkick('refresh');
				$('#note_description').val('');
				$('#noteUpdateId').val('');
				$('#cancelNoteButton').hide();
				$('#saveNoteButton').attr('value', 'Add Note');
				if(type == 'lead'){
					$('#jqxWidget').jqxGrid('updatebounddata');
				} else if(type == 'opportunity'){
					$('#jqxOpportunities').jqxGrid('updatebounddata');
				}
			}
		});
    } else {
		return false;
	}
}

function gridEditNote(noteId){
	var url = '/notes';
	var data = 'noteId='+noteId;
	$.post(url, data, function(response){
		var settings = JSON.parse(response);
		$('#update_notefrm #note_description').val(settings.note_description);
		$('#update_notefrm #gridType').val(settings.grid_type);
		$('#update_notefrm #gridTypeId').val(settings.grid_type_id);
		//$('#update_notefrm #recordsPerPage').val(recordsPerPage);
		//$('#update_notefrm #keyword').val(keyword);
		//$('#update_notefrm #customerId').val(customerId);
		$('#update_notefrm #noteUpdateId').val(noteId);
	});
}

function updateNoteData(form){
	$('.errorText').remove();
	var gridType = $('#update_notefrm #gridType').val();
	if($('#update_notefrm #note_description').val() != ''){
		var url = '/notes';
		var data = $(form).serialize();
		$.post(url, data, function(response){
			if(response > 0){
				if(gridType == 'lead'){
					$('#jqxWidget').jqxGrid('updatebounddata');
				} else if(gridType == 'opportunity'){
					$('#jqxOpportunities').jqxGrid('updatebounddata');
				}
				$('#editnoteslookup .closePopup').click();
			}
		});
	} else {
		$( '<p class="errorText">Please enter note description</p>' ).insertAfter( '#update_notefrm #note_description' );
	}
}

function clearValues(){
	$('#resumeForm').trigger("reset");
	$('#saveNoteButton').attr('value', 'Add Note');
	$('#update_notefrm #cancelNoteButton').hide();
}

/* TODO: clean up and merge with above */
function relatedNotes(id) {
	var url = '/ajaxgetnotes/opportunity/'+id+'?related=1';

	var source =
		{
			datatype: "json",
			datafields: [
				{ name: 'id' },
				{ name: 'follow_up_date' },
				{ name: 'note_type' },
				{ name: 'note_description' },
				{ name: 'created_date' },
				{ name: 'created_by' },
				{ name: 'modified_date' },
				{ name: 'modified_by' },
				{ name: 'grid_type' }
			],
			cache: false,
			url: url,
			root: 'Rows',
			sortcolumn: 'id',
			sortdirection: 'desc',
		};
		
		var pageable;
		var dataAdapter = new $.jqx.dataAdapter(source, {
			downloadComplete: function (data, status, xhr) { },
			loadComplete: function (data) { 
				if($("#jqxRelatedNotes").find('.jqx-grid-empty-cell').length>0){
						if($("#jqxRelatedNotes").find('.jqx-grid-empty-cell').parent('div').height()<30){
							$('.jqx-grid-empty-cell').parent('div').addClass('norecordsFoundRow');
						}
						$('.jqx-grid-empty-cell').closest("#jqxNotes").addClass('noInfoFound');
						$("#jqxRelatedNotes").find('.jqx-grid-empty-cell >span').text("No records found");
						pageable = false;
				}else{
					if($("#jqxRelatedNotes").hasClass('noInfoFound')){
						$("#jqxRelatedNotes").removeClass('noInfoFound');
					}
					pageable = true;
				}
			},
			loadError: function (xhr, status, error) { }
		});
		
		var FilteredData = new Array();
		FilteredData.push({ text: 'Date Created', type: 'string', datafield: 'created_date'});
		FilteredData.push({ text: 'Note Type', type: 'string', datafield: 'note_type'});
		FilteredData.push({ text: 'Logs/Notes', type: 'string', datafield: 'note_description'});
		FilteredData.push({ text: 'Follow up Date', type: 'string', datafield: 'follow_up_date'});
		
		$("#jqxRelatedNotes").jqxGrid(
		{
			width: '100%',
			source: dataAdapter,
			sortable: true,
			//showfilterrow: keyword ? false : true,
			//filterable: true,
			pageable: pageable,
			pagesize: parseInt(recordsPerPage),
			pagesizeoptions: ['5', '10', '20', '50', '100'],
			autorowheight: true,
			autoheight: true,
			enabletooltips: true,
			rowsheight:40,
			columnsheight:40,
			columns:FilteredData,
			pagerheight: 50,
			//headerheight: 60,
			virtualmode: true,
			//pagermode: 'simple',
			//pager: '#gridpager',
			//columnsresize: true,
			rendergridrows: function (params) {
				return params.data;
			},
		});
}