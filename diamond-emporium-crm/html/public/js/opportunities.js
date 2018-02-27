function getOpportunities(recordsPerPage, columnList, keyword, searchParams){
	$after = $('#jqxOpportunities').prev('#bindAfterThis');
	if ($after.length == 0) return;

	$('#jqxOpportunities').remove();
    $after.after('<div class="formTable manageMembers" id="jqxOpportunities"></div>');

    if(keyword && !searchParams){
		var url = '/ajaxgetopportunities?keyword='+keyword;
	} else if(!keyword && searchParams){
		var url = '/ajaxgetopportunities?'+searchParams;
	}else if(keyword && searchParams){
		var url = '/ajaxgetopportunities?keyword='+keyword+'&'+searchParams;
	}else if(!keyword && !searchParams){
		var url = '/ajaxgetopportunities';
	}

	// prepare the data
	var source =
	{
		datatype: "json",
		datafields: [
			{ name: 'id' },
			{ name: 'created_date' },
			{ name: 'opportunity_status' },
			{ name: 'order_id' },
			{ name: 'customer_fullname' },						
			{ name: 'customer_email' },
			{ name: 'opportunity_name' },
			{ name: 'customer_mobile' },
			{ name: 'rating' },
			{ name: 'budget' },
			{ name: 'est_close_date' },
			{ name: 'looking_for' },
			{ name: 'product' },
			{ name: 'preferred_contact' },
			{ name: 'note_description' },
			{ name: 'note_id' },
			{ name: 'lead_owner' }
		],
		//localdata: data,
		//id: 'id',
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
			$("#jqxOpportunities").jqxGrid('updatebounddata', 'filter');
		}
	};
	
	var cellsrenderer = function (row, columnfield, value, defaulthtml, columnproperties) {
		if (value > 0 && columnfield == 'note_id') {
			var datarow = $('#jqxOpportunities').jqxGrid('getrowdata', row);
			return '<div style="margin-top:15px; text-align:left;"><a href="javascript:;" onclick="javascript:getNotes('+recordsPerPage+', \'opportunity\', '+datarow.id+', 1);" data-popup="editnoteslookup" class="lightBoxClick gridLink">Edit</a></div>';
		} else if (columnfield == 'note_description') {
			return '<div style="margin-top:15px; text-align:left;">' + value.substring(0, 40) + '</div>';			
		}
    }
		
	var pageable = true, sortable = true;
	var dataAdapter = new $.jqx.dataAdapter(source, {
		downloadComplete: function (data, status, xhr) {},
		loadComplete: function (data) { 
			if($("#jqxOpportunities").find('.jqx-grid-empty-cell').length>0){
					if($("#jqxOpportunities").find('.jqx-grid-empty-cell').parent('div').height()<30){
						$('.jqx-grid-empty-cell').parent('div').addClass('norecordsFoundRow');
						$('.jqx-grid-empty-cell').closest("#jqxOpportunities").addClass('noInfoFound');
					}
					if(keyword){
						$("#jqxOpportunities").find('.jqx-grid-empty-cell >span').text("No matches were found");
						//$('#searchResultsHint').text("No matches were found");
					} else {
						$("#jqxOpportunities").find('.jqx-grid-empty-cell >span').text("No records found");
					}
			}else{
				if($("#jqxOpportunities").hasClass('noInfoFound')){
					$("#jqxOpportunities").removeClass('noInfoFound');
				}
			} 
		},
		loadError: function (xhr, status, error) { }
	});
		
	if(columnList == '' || columnList == undefined){
		/* Default columns */
		columnList = 'created_date,opportunity_status,order_id,customer_fullname,customer_email,opportunity_name,customer_mobile,rating,budget,est_close_date,note_description,note_id,preferred_contact,lead_owner';
	}

	var colList = columnList.split(',');
	var gridColumnList = new Array();
	var countData = colList.length;
	var colWidthData = 100/countData;
	if(colList.length>6){
		for(i = 0; i < colList.length; i++){
			if(colList[i] == 'created_date'){
				gridColumnList.push({ text: 'Created', type: 'string', datafield: 'created_date', width:colWidthData+'%'});
			}
			if(colList[i] == 'customer_fullname'){
				gridColumnList.push({ text: 'Customer Owner', type: 'string', datafield: 'customer_fullname', width:colWidthData+'%'});
			}
			if(colList[i] == 'customer_email'){
				gridColumnList.push({ text: 'Email', type: 'string', datafield: 'customer_email',  width:colWidthData+'%'});
			}
			if(colList[i] == 'opportunity_name'){
				gridColumnList.push({ text: 'Opportunity Name', type: 'string', datafield: 'opportunity_name',  width:colWidthData+'%'});
			}
			if(colList[i] == 'customer_mobile'){
				gridColumnList.push({ text: 'Mobile	Number', type: 'string', datafield: 'customer_mobile', width:colWidthData+'%' });
			}
			if(colList[i] == 'rating'){
				gridColumnList.push({ text: 'Rating', type: 'string', datafield: 'rating', width:'6%'});
			}
			if(colList[i] == 'budget'){
				gridColumnList.push({ text: 'Est.<br/> Revenue', type: 'string', datafield: 'budget', width:colWidthData+'%'});
			}
			if(colList[i] == 'est_close_date'){
				gridColumnList.push({ text: 'Predicted Close Date', type: 'string', datafield: 'est_close_date', width:colWidthData+'%'});
			}
			if(colList[i] == 'looking_for'){
				gridColumnList.push({ text: 'Notes', type: 'string', datafield: 'looking_for',  width:colWidthData+'%'});
			}
			if(colList[i] == 'product'){
				gridColumnList.push({ text: 'Edit Notes', type: 'string', datafield: 'product', width:colWidthData+'%'});
			}
			if(colList[i] == 'preferred_contact'){
				gridColumnList.push({ text: 'Follow up Date', type: 'string', datafield: 'preferred_contact', width:colWidthData+'%'});
			}
			if(colList[i] == 'note_description'){
				gridColumnList.push({ text: 'Notes', type: 'string', datafield: 'note_description', cellsrenderer: cellsrenderer,width:colWidthData+'%'});
			}
			if(colList[i] == 'note_id'){
				gridColumnList.push({ text: 'Edit Notes', type: 'string', datafield: 'note_id', cellsrenderer: cellsrenderer, width:colWidthData+'%'});
			}
			if(colList[i] == 'lead_owner'){
				gridColumnList.push({ text: 'Lead Owner', type: 'string', datafield: 'lead_owner', width:colWidthData+'%'});
			}
			if(colList[i] == 'opportunity_status'){
				gridColumnList.push({ text: 'Status', type: 'string', datafield: 'opportunity_status',  width:colWidthData+'%'});
			}
			if(colList[i] == 'order_id'){
				gridColumnList.push({ text: 'Status', type: 'string', datafield: 'order_id',  width:colWidthData+'%'});
			}
		}
	}else{
		for(i = 0; i < colList.length; i++){
			if(colList[i] == 'created_date'){
				gridColumnList.push({ text: 'Created', type: 'string', datafield: 'created_date', width:colWidthData+'%'});
			}
			if(colList[i] == 'opportunity_status'){
				gridColumnList.push({ text: 'Status', type: 'string', datafield: 'opportunity_status', width:colWidthData+'%'});
			}
			if(colList[i] == 'order_id'){
				gridColumnList.push({ text: 'Order', type: 'string', datafield: 'order_id', width:colWidthData+'%'});
			}
			if(colList[i] == 'customer_fullname'){
				gridColumnList.push({ text: 'Customer Owner', type: 'string', datafield: 'customer_fullname', width:colWidthData+'%'});
			}
			if(colList[i] == 'customer_email'){
				gridColumnList.push({ text: 'Email', type: 'string', datafield: 'customer_email', width:colWidthData+'%'});
			}
			if(colList[i] == 'opportunity_name'){
				gridColumnList.push({ text: 'Opportunity Name', type: 'string', datafield: 'opportunity_name', width:colWidthData+'%'});
			}
			if(colList[i] == 'customer_mobile'){
				gridColumnList.push({ text: 'Mobile	Number', type: 'string', datafield: 'customer_mobile', width:colWidthData+'%'});
			}
			if(colList[i] == 'rating'){
				gridColumnList.push({ text: 'Rating', type: 'string', datafield: 'rating', width:colWidthData+'%'});
			}
			if(colList[i] == 'budget'){
				gridColumnList.push({ text: 'Est. <br/> Revenue', type: 'string', datafield: 'budget', width:colWidthData+'%'});
			}
			if(colList[i] == 'est_close_date'){
				gridColumnList.push({ text: 'Predicted Close Date', type: 'string', datafield: 'est_close_date', width:colWidthData+'%'});
			}
			if(colList[i] == 'looking_for'){
				gridColumnList.push({ text: 'Notes', type: 'string', datafield: 'looking_for', width:colWidthData+'%'});
			}
			if(colList[i] == 'product'){
				gridColumnList.push({ text: 'Edit Notes', type: 'string', datafield: 'product', width:colWidthData+'%'});
			}
			if(colList[i] == 'preferred_contact'){
				gridColumnList.push({ text: 'Follow up Date', type: 'string', datafield: 'preferred_contact', width:colWidthData+'%'});
			}
			if(colList[i] == 'note_description'){
				gridColumnList.push({ text: 'Notes', type: 'string', datafield: 'note_description', cellsrenderer: cellsrenderer, width:colWidthData+'%'});
			}
			if(colList[i] == 'note_id'){
				gridColumnList.push({ text: 'Edit Notes', type: 'string', datafield: 'note_id', cellsrenderer: cellsrenderer, width:colWidthData+'%'});
			}
			if(colList[i] == 'lead_owner'){
				gridColumnList.push({ text: 'Lead Owner', type: 'string', datafield: 'lead_owner', width:colWidthData+'%'});
			}
		}
	}

	$("#jqxOpportunities").jqxGrid(
	{
		width: '100%',
		source: dataAdapter,
		sorttogglestates:1,
		//showfilterrow: keyword ? false : true,
		//filterable: true,
		pageable: pageable,
		sortable: sortable,
		pagesize: parseInt(recordsPerPage),
		pagesizeoptions: ['5', '10', '20', '50', '100'],
		autorowheight: true,
		autoheight: true,
		enabletooltips: true,
		rowsheight:50,
		columnsheight:50,
		pagerheight:50,
		//headerheight: 60,
		virtualmode: true,
		//enablemousewheel: false,
		//pagermode: 'simple',
		//pager: '#gridpager',
		//columnsresize: true,
		rendergridrows: function (params) {
			return params.data;
		},
		columns:gridColumnList,
	});

	
	$('#jqxOpportunities').bind('cellclick', function(event)  {
		var current_index = event.args.rowindex;
		var current_column = event.args.column.datafield;
		var datarow = $('#jqxOpportunities').jqxGrid('getrowdata', current_index);
		var url = '/opportunitydetails/'+datarow.id;
		if(current_column != 'note_id'){
			$(location).attr('href', url);
		}
		$('#gridTypeId').val(datarow.id);
		$('#gridType').val('opportunity');
	});
	
	$("#jqxOpportunities").bind("sort", function (event) {
		$("#jqxOpportunities").jqxGrid('updatebounddata', 'filter');
	});
	
	$("#jqxOpportunities").bind("pagesizechanged", function (event) {
		var args = event.args;
		var pagenumber = args.pagenum;
		var pagesize = args.pagesize;
		$('#pageSizeGrabing').val(pagesize);
	});
}

function customersMobileLookup(recordsPerPage, keyword){
	$('#searchResultsHintjqxCustomersMob').text("");
	if(keyword)
		var url = '/ajaxcustomerslookup?keyword='+keyword;
	else
		var url = '/ajaxcustomerslookup';
	
	// prepare the data
	var source =
	{
		datatype: "json",
		datafields: [
			{ name: 'id' },
			{ name: 'first_name' },
			{ name: 'last_name' },
			{ name: 'email' },
			{ name: 'mobile' },
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
			$("#jqxCustomersMobile").jqxGrid('updatebounddata', 'filter');
		}
	};
	
	var dataAdapter = new $.jqx.dataAdapter(source, {
		downloadComplete: function (data, status, xhr) { },
		loadComplete: function (data) { 
			if($("#jqxCustomersMobile").find('.jqx-grid-empty-cell').length>0){
					if($("#jqxCustomersMobile").find('.jqx-grid-empty-cell').parent('div').height()<30){
						$('.jqx-grid-empty-cell').parent('div').addClass('norecordsFoundRow');
						$('.jqx-grid-empty-cell').closest("#jqxCustomersMobile").addClass('noInfoFound');
					}
					if(keyword){
						$("#jqxCustomersMobile").find('.jqx-grid-empty-cell >span').text("No matches were found");
						//$('#searchResultsHintjqxCustomersMob').text("No matches were found");
					} else {
						$("#jqxCustomersMobile").find('.jqx-grid-empty-cell >span').text("No records found");
					}
			}else{
				if($("#jqxCustomersMobile").hasClass('noInfoFound')){
					$("#jqxCustomersMobile").removeClass('noInfoFound');
				}
			}
		},
		loadError: function (xhr, status, error) { }
	});
	
	$("#jqxCustomersMobile").jqxGrid(
	{
		width: "100%",
		source: dataAdapter,
		sortable: true,
		//showfilterrow: keyword ? false : true,
		//filterable: true,
		pageable: true,
		pagesize: parseInt(recordsPerPage),
		pagesizeoptions: ['5', '10', '20', '50', '100'],
		autorowheight: true,
		autoheight: true,
		enabletooltips: true,
		rowsheight:50,
		columnsheight:50,
		pagermode: 'simple',
		pagerheight: 0,
		virtualmode: true,
		//columnsresize: true,
		rendergridrows: function (params) {
			return params.data;
		},
		columns: [
			{ text: 'First Name', type: 'string', datafield: 'first_name', width:100 },
			{ text: 'Last Name', type: 'string', datafield: 'last_name', width:100 },
			{ text: 'Mobile	Number', type: 'string', datafield: 'mobile', width:130 },
			{ text: 'Email', type: 'string', datafield: 'email' },
		]
	});
	
	$("#jqxCustomersMobile").next('.pagerHTML').html($('#pagerjqxCustomers'));
	
	$('#jqxCustomersMobile').jqxGrid('clearselection');
	
	$('#jqxCustomersMobile').bind('rowselect', function(event)  {
		var current_index = event.args.rowindex;
		var datarow = $('#jqxCustomersMobile').jqxGrid('getrowdata', current_index);
		
		$('#frm_opportunity #user_id').val(datarow.id);
		$('#frm_opportunity #mobile').val(datarow.mobile);
		
		$('#customermobilelookup .closePopup').click();
		
		// Use datarow for display of data in div outside of grid
	});
	
	$("#jqxCustomersMobile").bind("sort", function (event) {
	    /*var sortinformation = event.args.sortinformation;
	    
	    var sortdatafield = sortinformation.sortcolumn;
	    
	    var sortorder = sortinformation.sortdirection.ascending ? "asc" : "desc";
	    
	    var sortUrl = '/ajaxcustomerslookup?sortdatafield='+sortdatafield+'&sortorder='+sortorder;*/
	    
	   //$.get(sortUrl, function(source){
	    	//$("#jqxCustomersMobile").jqxGrid('databind', source);
	    	$("#jqxCustomersMobile").jqxGrid('updatebounddata', 'filter');
	   //});
	});
}

function validateOpportunity(){
	var errors = 0;	
	$('.errorText').remove();
	if($('#frm_opportunity #opportunity_name').val() == ''){
		$( '<p class="errorText">Please enter opportunity Name</p>' ).insertAfter( '#frm_opportunity #opportunity_name' );
		errors++;
	}
	if($('#frm_opportunity #mobile').val() == ''){
		$( '<p class="errorText">Please select customer lookup</p>' ).insertAfter( '#frm_opportunity #oppmobilelookup' );
		errors++;
	}
	
	if($('#frm_opportunity #opportunity_type option:selected').val() == ''){
		$( '<p class="errorText">Please select opportunity type</p>' ).insertAfter( '#frm_opportunity #opportunity_type' );
		errors++;
	}
	
	if($('#frm_opportunity #lead_source option:selected').val() == ''){
		$( '<p class="errorText">Please select lead source</p>' ).insertAfter( '#frm_opportunity #lead_source' );
		errors++;
	}
	
	/*if($('#frm_opportunity #referred_by_name').val() == ''){
		$( '<p class="errorText">Please enter referred by customer</p>' ).insertAfter( '#frm_opportunity #oppcustomerlookup' );
		errors++;
	}*/
	
	if($('#frm_opportunity #looking_for').val() == ''){
		$( '<p class="errorText">Please enter what are they looking for?</p>' ).insertAfter( '#frm_opportunity #looking_for' );
		errors++;
	}
	
	if($('#frm_opportunity #product option:selected').val() == 0){
		$( '<p class="errorText">Please select product</p>' ).insertAfter( '#frm_opportunity #product' );
		errors++;
	}
	
	if($('#frm_opportunity #preferred_contact option:selected').val() == ''){
		$( '<p class="errorText">Please select preferred method of contact</p>' ).insertAfter( '#frm_opportunity #preferred_contact' );
		errors++;
	}
	
	if($('#frm_opportunity #budget').val() == ''){
		$( '<p class="errorText">Please enter budget</p>' ).insertAfter( '#frm_opportunity #budget' );
		errors++;
	}

	if($('#frm_opportunity #rating option:selected').val() == ''){
		$( '<p class="errorText">Please select rating</p>' ).insertAfter( '#frm_opportunity #rating' );
		errors++;
	}
	
	if($('#frm_opportunity .datepickerInput').val() == ''){
		$( '<p class="errorText">Please select est. close date</p>' ).insertAfter( '#frm_opportunity .datepickerInput' );
		errors++;
	}
	
	return errors;
}

function validateUpdateOpportunity(){
	var errors = 0;
	$('.errorText').remove();
	/*if($('#frm_opportunity #special_instructions').val() == ''){
		$( '<p class="errorText">Please enter special instructions</p>' ).insertAfter( '#frm_opportunity #special_instructions' );
		errors++;
	}*/
	
	if($('#frm_opportunity #opportunity_name').val() == ''){
		$( '<p class="errorText">Please enter Opportunity Name</p>' ).insertAfter( '#frm_opportunity #opportunity_name' );
		errors++;
	}
	if($('#frm_opportunity #record_owner_id option:selected').val() == 0){
		$( '<p class="errorText">Please select lead owner</p>' ).insertAfter( '#frm_opportunity #record_owner_id' );
		errors++;
	}
		
	if($('#frm_opportunity #budget').val() == ''){
		$( '<p class="errorText">Please enter budget</p>' ).insertAfter( '#frm_opportunity #budget' );
		errors++;
	}
	
	if($('#frm_opportunity #looking_for').val() == ''){
		$( '<p class="errorText">Please enter what are they looking for?</p>' ).insertAfter( '#frm_opportunity #looking_for' );
		errors++;
	}
	
	if($('#frm_opportunity #rating option:selected').val() == ''){
		$( '<p class="errorText">Please select rating</p>' ).insertAfter( '#frm_opportunity #rating' );
		errors++;
	}
		
	if($('.datepickerInput').val() == ''){
		$( '<p class="errorText">Please select est. close date</p>' ).insertAfter( '.datepickerInput' );
		errors++;
	}
	
	if($('#frm_opportunity #opportunity_type option:selected').val() == ''){
		$( '<p class="errorText">Please select opportunity type</p>' ).insertAfter( '#frm_opportunity #opportunity_type' );
		errors++;
	}
	
	if($('#frm_opportunity #lead_source option:selected').val() == ''){
		$( '<p class="errorText">Please select lead source</p>' ).insertAfter( '#frm_opportunity #lead_source' );
		errors++;
	}
	
	/*if($('#frm_opportunity #referred_by_name').val() == ''){
		$( '<p class="errorText">Please enter referred by customer</p>' ).insertAfter( '#frm_opportunity #oppcustomerlookup' );
		errors++;
	}*/
	
	if($('#frm_opportunity #product option:selected').val() == 0){
		$( '<p class="errorText">Please select product</p>' ).insertAfter( '#frm_opportunity #product' );
		errors++;
	}
	
	if($('#frm_opportunity #preferred_contact option:selected').val() == ''){
		$( '<p class="errorText">Please select preferred method of contact</p>' ).insertAfter( '#frm_opportunity #preferred_contact' );
		errors++;
	}
	
	/*if($('#frm_opportunity #reference_product').val() == ''){
		$( '<p class="errorText">Please enter reference product</p>' ).insertAfter( '#frm_opportunity #reference_product' );
		errors++;
	}*/
	
	return errors;
}

function saveOpportunity(form){
		var errors = validateOpportunity();
		if(errors == 0){
			$('#saveOpp').attr('disabled','disabled');
			var url = '/opportunities';
			var data = $(form).serialize();
			$.post(url, data, function(response){
				if(response > 0){
					/*$('#leadsList .closePopup').click();
					$('#jqxOpportunities').jqxGrid('updatebounddata');
					getCount('opportunities');
					$('#saveOpp').removeAttr('disabled');
					$('#frm_opportunity')[0].reset();*/
					
					if(!$('#opportunityId').length)
						window.location.href = '/opportunitydetails/'+response;
				}
			});
		}else{
			$('#saveOpp').removeAttr('disabled');
		}
}

function updateOpportunity(form){
	if($('#saveOpportunityButton').text() === "Edit"){
		$('#saveOpportunityButton').text('Save');
		$('#frm_opportunity .displayHide').show();
		//$('#frm_opportunity #saveNoteButton').show();
		$('#frm_opportunity #oppStatusButton').show();
		$('#frm_opportunity .hiddenUnqValues').hide();
		//$('#frm_opportunity #cancelNoteButton').show();
		//getNotes($('#recordsPerPage').val(), 'opportunity', $('#opportunity_statusId').val(), 2);
	} else if($('#saveOpportunityButton').text() === "Save"){
		var errors = validateUpdateOpportunity();
		if(errors == 0){
			var url = '/opportunities';
			var data = $(form).serialize();
			$.post(url, data, function(response){
				if(response > 0){
					//BOF Pushing New Values
					$('#special_instructions_opp').text($('#special_instructions').val());
					$('#record_owner_id_opp').text($("#record_owner_id option[value="+$('#record_owner_id option:selected').val()+"]").text());
					$('#budget_opp').text($('#budget').val());
					$('#looking_for_opp').text($('#looking_for').val());
					$('#rating_opp').text($("#rating option[value='"+$('#rating option:selected').val()+"']").text());
					
					var date = $('.datepickerInput').val().split('/');
					var dateStr = date[0]+'/'+date[1]+'/'+date[2].substr(2, 2) ;					
					$('#est_close_date_opp').text(dateStr);
					
					$('#opportunity_type_opp').text($("#opportunity_type option[value='"+$('#opportunity_type option:selected').val()+"']").text());
					$('#lead_source_opp').text($("#lead_source option[value='"+$('#lead_source option:selected').val()+"']").text());
					$('#referred_by_name_opp').text($('#referred_by_name').val());
					$('#product_opp').text($("#product option[value="+$('#product option:selected').val()+"]").text());
					$('#preferred_contact_opp').text($("#preferred_contact option[value='"+$('#preferred_contact option:selected').val()+"']").text());
					$('#reference_product_opp').text($('#reference_product').val());
					/*var opportunity_status = $("#opportunity_status option[value='"+$('#opportunity_status option:selected').val()+"']").text();
					if(opportunity_status == 'Pending'){
						$('#opportunity_status_opp').text('-');
					} else {
						$('#opportunity_status_opp').text(opportunity_status);
					}*/
					//EOF Pushing New Values
					$('#saveOpportunityButton').text('Edit');
					$('#frm_opportunity .displayHide').hide();
					//$('#frm_opportunity #saveNoteButton').hide();
					$('#frm_opportunity #oppStatusButton').hide();
					$('#frm_opportunity .hiddenUnqValues').show();
					//$('#frm_opportunity #cancelNoteButton').hide();
					//getNotes($('#recordsPerPage').val(), 'opportunity', $('#opportunity_statusId').val(), 3);
					alert('Opportunity details updated successfully');
				}
			});
		}
	}
}

/*function setFormsReadOnly(form){
	var opportunityForm = form;
	var opportunityFormFields = $("input", opportunityForm);
	var opportunitySelectFields = $("select", opportunityForm);
	opportunityFormFields.each(function(i) {
		var formField = opportunityFormFields.eq(i);
		formField.attr("disabled", true);
	});
	opportunitySelectFields.each(function(i) {
		var formSelectField = opportunitySelectFields.eq(i);
		formSelectField.attr("disabled", true);
	});
}*/

function deleteOpportunity(id){
	if (confirm("Are you sure you want to delete opportunity")) {
        var url = '/deleteopportunity/'+id;
		var data = 'id='+id;
		$.post(url, data, function(response){
			if(response == 1){
				window.location.href = '/opportunities';
			}
		});
    } else {
		return false;
	}
}

function validateStatusOpportunity(){
	var errors = 0;
	$('.errorText').remove();
	
	if($('#opportunity_status #opportunity_status option:selected').val() == ''){
		$( '<p class="errorText">Please select status of opportunity</p>' ).insertAfter( '#opportunity_status #opportunity_status' );
		errors++;
	}
	if($('#opportunity_status #opportunity_status option:selected').val() != 'Open'){
		if($('#opportunity_status .oppCloseDate').val() == ''){
			$( '<p class="errorText">Please select close date of opportunity</p>' ).insertAfter( '#opportunity_status .oppCloseDate' );
			errors++;
		}

		if($('#opportunity_status #opportunity_status option:selected').val() == 'Closed Lost'){
			if($('#opportunity_status #opportunity_reason option:selected').val() == ''){
				$( '<p class="errorText">Please enter reason</p>' ).insertAfter( '#opportunity_status #opportunity_reason' );
				errors++;
			}
		}
	}
	
	return errors;
}

function updateOpportunityStatus(form){
	var errors = validateStatusOpportunity();
	if(errors == 0){
		var url = '/updateopportunitystatus';
		var data = $(form).serialize();
		$.post(url, data, function(response){
			if(response == 1){
				$('#opportunityStatusDiv').html($('#opportunity_status option:selected').val());
				$('#opportunityReasonDiv').html($('#opportunity_reason option:selected').val());
				$('#OpportunityStatusLable').html($('#opportunity_status option:selected').val());
				$('#opportunityCloseDateDiv').html($('.oppCloseDate').val());
				if($('#opportunity_status option:selected').val() == 'Open'){
					//$('#opportunityStatusDiv').html('-');
					//$('#opportunityReasonDiv').html('-');
					//$('#OpportunityStatusLable').html($('#opportunity_status option:selected').val());
					//$('#opportunityCloseDateDiv').html('-');
					$('#oppStatusButton').text('Close Opportunity');
					$('#oppStatusHeading').html('Close Opportunity');
				} else {
					$('#oppStatusButton').text('Open');
					$('#oppStatusHeading').html('Open Opportunity');
				}
				$('.closePopup').click();
			}
		});
	}
}

function oppCustomersLookup(recordsPerPage, selectedCustId, selectedCustName, keyword){
	$('#searchResultsHintjqxOppCus').text("");
	if(keyword)
		//var url = '/ajaxoppcustomerslookup?keyword='+keyword;
		var url = '/ajaxcustomerslookup?keyword='+keyword;
	else
		var url = '/ajaxcustomerslookup';
		//var url = '/ajaxoppcustomerslookup';
	
	// prepare the data
	var source =
	{
		datatype: "json",
		datafields: [
			{ name: 'id' },
			{ name: 'first_name' },
			{ name: 'last_name' },
			{ name: 'email' },
			{ name: 'mobile' },
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
			$("#jqxOppCustomersLookup").jqxGrid('updatebounddata', 'filter');
		}
	};
	
	var dataAdapter = new $.jqx.dataAdapter(source, {
		downloadComplete: function (data, status, xhr) { },
		loadComplete: function (data) { 
			if($("#jqxOppCustomersLookup").find('.jqx-grid-empty-cell').length>0){
					if($("#jqxOppCustomersLookup").find('.jqx-grid-empty-cell').parent('div').height()<30){
						$('.jqx-grid-empty-cell').parent('div').addClass('norecordsFoundRow');
						$('.jqx-grid-empty-cell').closest("#jqxOppCustomersLookup").addClass('noInfoFound');
					}
					if(keyword){
						$("#jqxOppCustomersLookup").find('.jqx-grid-empty-cell >span').text("No matches were found");
						//$('#searchResultsHintjqxOppCus').text("No matches were found");
					} else {
						$("#jqxOppCustomersLookup").find('.jqx-grid-empty-cell >span').text("No records found");
					}
					//$("#jqxOppCustomersLookup").find('.jqx-grid-empty-cell >span').text("No records found");
			}else{
				if($("#jqxOppCustomersLookup").hasClass('noInfoFound')){
					$("#jqxOppCustomersLookup").removeClass('noInfoFound');
				}
			}
		},
		loadError: function (xhr, status, error) { }
	});
	
	$("#jqxOppCustomersLookup").jqxGrid(
	{
		width: "100%",
		source: dataAdapter,
		sortable: true,
		//showfilterrow: keyword ? false : true,
		//filterable: true,
		pageable: true,
		pagesize: parseInt(recordsPerPage),
		pagesizeoptions: ['5', '10', '20', '50', '100'],
		autorowheight: true,
		autoheight: true,
		enabletooltips: true,
		rowsheight:50,
		columnsheight:50,
		pagermode: 'simple',
		pagerheight: 0,
		virtualmode: true,
		//columnsresize: true,
		rendergridrows: function (params) {
			return params.data;
		},
		columns: [
			{ text: 'First Name', type: 'string', datafield: 'first_name', width:100 },
			{ text: 'Last Name', type: 'string', datafield: 'last_name', width:100 },
			{ text: 'Mobile	Number', type: 'string', datafield: 'mobile', width:130 },
			{ text: 'Email', type: 'string', datafield: 'email' },
		]
	});
	
	$("#jqxOppCustomersLookup").next('.pagerHTML').html($('#pagerjqxCustomers'));
	
	$('#jqxOppCustomersLookup').jqxGrid('clearselection');
	
	$('#jqxOppCustomersLookup').bind('rowselect', function(event)  {
		var current_index = event.args.rowindex;
		var datarow = $('#jqxOppCustomersLookup').jqxGrid('getrowdata', current_index);
		
		$('#'+selectedCustId).val(datarow.id);
		$('#'+selectedCustName).val(datarow.first_name + ' ' + datarow.last_name);
		
		$('#oppCustomerDatalookup .closePopup').click();
		
		// Use datarow for display of data in div outside of grid
	});
	
	$("#jqxOppCustomersLookup").bind("sort", function (event) {
    	$("#jqxOppCustomersLookup").jqxGrid('updatebounddata', 'filter');
	});
}

function getOpportunitiesLookup(containerId, recordsPerPage, customerId, keyword, searchParams){

	if (typeof(searchParams) != 'object') {
		searchParams = {};
	}
	if (keyword) {
		searchParams['keyword'] = keyword;;
	}
	if (customerId) {
		searchParams['oppCustomerId'] = customerId;
	}
	
	var url = '/ajaxgetopportunities';
	if (!$.isEmptyObject(searchParams)) {
	  url += '?' + $.param(searchParams);
	}

	// prepare the data
	var source =
	{
		datatype: "json",
		datafields: [
			{ name: 'id' },
			{ name: 'cust_id' },
			{ name: 'created_date' },
			{ name: 'opportunity_name' },
			{ name: 'customer_fullname' },
			{ name: 'customer_email' },
			{ name: 'customer_mobile' },
			{ name: 'rating' },
			{ name: 'budget' },
			{ name: 'est_close_date' },
			{ name: 'looking_for' },
			{ name: 'product' },
			{ name: 'preferred_contact' },
			{ name: 'note_description' },
			{ name: 'note_id' },
			{ name: 'lead_owner' }
		],
		//localdata: data,
		//id: 'id',
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
			$("#"+containerId).jqxGrid('updatebounddata', 'filter');
		}
	};
	
	var pageable = true, sortable = true;
	var dataAdapter = new $.jqx.dataAdapter(source, {
		downloadComplete: function (data, status, xhr) {},
		loadComplete: function (data) { 
			if($("#"+containerId).find('.jqx-grid-empty-cell').length>0){
				if($("#"+containerId).find('.jqx-grid-empty-cell').parent('div').height()<30){
					$('.jqx-grid-empty-cell').parent('div').addClass('norecordsFoundRow');
					$('.jqx-grid-empty-cell').closest("#"+containerId).addClass('noInfoFound');
				}
				if(keyword){
					$("#"+containerId).find('.jqx-grid-empty-cell >span').text("No matches were found");
					//$('#searchResultsHint').text("No matches were found");
				} else {
					$("#"+containerId).find('.jqx-grid-empty-cell >span').text("No records found");
				}
			}else{
				if($("#"+containerId).hasClass('noInfoFound')){
					$("#"+containerId).removeClass('noInfoFound');
				}
			} 
		},
		loadError: function (xhr, status, error) { }
	});
		
	$("#"+containerId).jqxGrid(
	{
		width: '100%',
		source: dataAdapter,
		sorttogglestates:1,
		pageable: pageable,
		sortable: sortable,
		pagesize: parseInt(recordsPerPage),
		pagesizeoptions: ['5', '10', '20', '50', '100'],
		autorowheight: true,
		autoheight: true,
		enabletooltips: true,
		rowsheight:50,
		columnsheight:50,
		pagerheight:50,
		virtualmode: true,
		rendergridrows: function (params) {
			return params.data;
		},
		
		columns: [
			{ text: 'Customer Name', type: 'string', datafield: 'customer_fullname', width:'30%'},
			{ text: 'Email', type: 'string', datafield: 'customer_email' },
			{ text: 'Mobile Number', type: 'string', datafield: 'customer_mobile', width:150 },
			{ text: 'Rating', type: 'string', datafield: 'rating', width:150 },
		]

	});
	
	$('#'+containerId).bind('cellclick', function(event)  {
		var index = event.args.rowindex;
		var column = event.args.column.datafield;
		var datarow = $('#'+containerId).jqxGrid('getrowdata', index);
		
		$('#opp_id').val(datarow.id);
		$('#opp_name').val(datarow.opportunity_name);
		
		if(!customerId && $('#cust_id').length > 0)
			$('#cust_id').val(datarow.cust_id);
		
		$('#opportunitylookup .closePopup').click();
	});
	
	$("#"+containerId).bind("sort", function (event) {
	    	$("#"+containerId).jqxGrid('updatebounddata', 'filter');
	});
}

function opportunitiesStatusFormReason() {
	 if ($('#opportunity_status #opportunity_status option:selected').val() == 'Closed Lost') {
		 /* Reason is mandatory */
		 $('#opportunityStatusReason').show();
	 } else {
		 $('#opportunityStatusReason').hide();
	 }
}

$(document).ready(function () {
	$("#opportunity_status.dropdown").dropkick({
	     change: function(value,label){
	    	 opportunitiesStatusFormReason();
	     }
	});
	opportunitiesStatusFormReason();
});