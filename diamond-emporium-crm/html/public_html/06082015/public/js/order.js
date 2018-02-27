$(document).on('click','.selectMilestone label', function(){
	 if($(this).find('input[type="checkbox"]').is(':checked')){
		 $(this).addClass('active');
	 }else{
		 $(this).removeClass('active');
	 }
})
/**
 * Populates JQXGrid lookupo view for invoices
 */
function getInvoiceLookup(){
	var url = '/ajaxinvoicelookup';
	
	// prepare the data
	var source =
	{
		datatype: "json",
		datafields: [
			{ name: 'id' },
			{ name: 'opp_name' },
			{ name: 'invoice_number' },
			{ name: 'created_date' },
			//{ name: 'amount_paid' },
			//{ name: 'amount_due' },
			//{ name: 'amount_credited' },
			//{ name: 'date' },
			//{ name: 'due_date' },
			//{ name: 'total' },
			//{ name: 'status' },
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
			$("#jqxInvoiceLookup").jqxGrid('updatebounddata', 'filter');
		}
	};
	
	var pageable = true, sortable = true;
	var dataAdapter = new $.jqx.dataAdapter(source, {
		downloadComplete: function (data, status, xhr) { },
		loadComplete: function (data) { 
			if($("#jqxInvoiceLookup").find('.jqx-grid-empty-cell').length>0){
					if($("#jqxInvoiceLookup").find('.jqx-grid-empty-cell').parent('div').height()<30){
						$('.jqx-grid-empty-cell').parent('div').addClass('norecordsFoundRow');
						$('.jqx-grid-empty-cell').closest("#jqxInvoiceLookup").addClass('noInfoFound');
					}
					if(keyword){
						$("#jqxInvoiceLookup").find('.jqx-grid-empty-cell >span').text("No matches were found");
					} else {
						$("#jqxInvoiceLookup").find('.jqx-grid-empty-cell >span').text("No records found");
					}
			}else{
				if($("#jqxInvoiceLookup").hasClass('noInfoFound')){
					$("#jqxInvoiceLookup").removeClass('noInfoFound');
				}
			}
		},
		loadError: function (xhr, status, error) { }
	});
	
	$("#jqxInvoiceLookup").jqxGrid(
	{
		width: "100%",
		source: dataAdapter,
		sortable: sortable,
		pageable: pageable,
		autorowheight: true,
		autoheight: true,
		enabletooltips: true,
		rowsheight:50,
		columnsheight:50,
		pagerheight: 50,
		virtualmode: true,
		rendergridrows: function (params) {
			return params.data;
		},
		columns: [
			{ text: 'Invoice Number', type: 'string', datafield: 'invoice_number', width:100 },
			{ text: 'Opppotunity Name', type: 'string', datafield: 'opp_name', width:100 },
			{ text: 'Invoice Created Date', type: 'string', datafield: 'created_date', width:130 },
			//{ text: 'Amount Credited', type: 'string', datafield: 'amount_credited', width:100 },
			//{ text: 'Price', type: 'string', datafield: 'total', width:130 },
			//{ text: 'Date', type: 'string', datafield: 'date', width:130 },
			//{ text: 'Due Date', type: 'string', datafield: 'due_date', width:130 },
		]
	});
	
	$('#jqxInvoiceLookup').jqxGrid('clearselection');
	
	$('#jqxInvoiceLookup').bind('rowselect', function(event)  {
		var current_index = event.args.rowindex;
		var datarow = $('#jqxInvoiceLookup').jqxGrid('getrowdata', current_index);
		
		$('#invoice_number').val(datarow.invoice_number);
		
		$('#invoicelookup .closePopup').click();
		
		// Use datarow for display of data in div outside of grid
	});	
}

/**
 * Populates JQXGrid for orders
 * recordsPerPage - Number of records to be displayed in a page
 * keyword - Search keyword
 */

function getOrders(recordsPerPage, keyword, cust_id){
	if(keyword)
		var url = '/ajaxorderlist?keyword=' + keyword;
	else
		var url = '/ajaxorderlist?keyword=' + 0;
	
	if(cust_id)
		url = url + '&cust_id=' + cust_id;
		
	// prepare the data
	var source =
	{
		datatype: "json",
		datafields: [
			{ name: 'id' },
			{ name: 'exp_delivery_date' },			
			{ name: 'comment' },
			{ name: 'invoice_number' },
			{ name: 'created_date' },
			{ name: 'opportunity_name' },
			{ name: 'customer_name' },
			{ name: 'owner_name' },
			{ name: 'value' },
			{ name: 'payment_made' },
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
			$("#jqxOrders").jqxGrid('updatebounddata', 'filter');
		}
	};
	
	var pageable = true, sortable = true;
	var dataAdapter = new $.jqx.dataAdapter(source, {
		downloadComplete: function (data, status, xhr) {},
		loadComplete: function (data) {
			if($("#jqxOrders").find('.jqx-grid-empty-cell').length>0 ){
					if($("#jqxOrders").find('.jqx-grid-empty-cell').parent('div').height()<30){
						$('.jqx-grid-empty-cell').parent('div').addClass('norecordsFoundRow');
						$('.jqx-grid-empty-cell').closest("#jqxOrders").addClass('noInfoFound');
					}
					if(keyword){
						$("#jqxOrders").find('.jqx-grid-empty-cell >span').text("No matches were found");
						//$('#searchResultsHint').text("No matches were found");
					} else {
						$("#jqxOrders").find('.jqx-grid-empty-cell >span').text("No records found");
					}
					
			}else{
				if($("#jqxOrders").hasClass('noInfoFound')){
					$("#jqxOrders").removeClass('noInfoFound');
				}
			}
			return;
		},
		loadError: function (xhr, status, error) { }
	});
	
	/*var payment_made = function(row, columnfield, value, defaulthtml, columnproperties){
		var datarow = $('#jqxOrders').jqxGrid('getrowdata', row);
		return datarow.payment_made+'%';
	};*/
	
	$("#jqxOrders").jqxGrid(
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
		columns: [
			{ text: 'Order <br/> Number', type: 'string', datafield: 'id', width:90 },
			{ text: 'Description', type: 'string', datafield: 'comment', width:'14%'},
			{ text: 'Customer <br/> Name', type: 'string', datafield: 'customer_name', width:'14%'},
			{ text: 'Opportunity <br/> Name', type: 'string', datafield: 'opportunity_name', width:'12%'},
			{ text: 'Invoice / <br/> Quote No.', type: 'string', datafield: 'invoice_number', width:'10%' },
			{ text: 'Payment <br/> Made', type: 'string', datafield: 'payment_made', width:'10%'},
			{ text: 'Owner', type: 'string', datafield: 'owner_name', width:'12%'},
			{ text: 'Created <br/> On', type: 'string', datafield: 'created_date', width:'10%'},
			{ text: 'Value', type: 'string', datafield: 'value', width:'8%'},
		]
	});
	
	$('#jqxOrders').bind('cellclick', function(event)  {
		var current_index = event.args.rowindex;
		var current_column = event.args.column.datafield;
		var datarow = $('#jqxOrders').jqxGrid('getrowdata', current_index);
		var url = '/orderdetails/'+datarow.id;
		
		//if(current_column != null){
			$(location).attr('href', url);
		//}
	});
	
	$("#jqxOrders").bind("sort", function (event) {
	    /*var sortinformation = event.args.sortinformation;
		
	    var sortdatafield = sortinformation.sortcolumn;
	    
	    var sortorder = sortinformation.sortdirection.ascending ? "asc" : "desc";
	    
	    var sortUrl = '/ajaxorderlist?sortdatafield='+sortdatafield+'&sortorder='+sortorder;*/
	    
	   //$.get(sortUrl, function(source){
	    	//$("#jqxOrders").jqxGrid('databind', source);
	    	$("#jqxOrders").jqxGrid('updatebounddata', 'filter');
	   //});
	});
	
	$("#jqxOrders").bind("pagesizechanged", function (event) {
		var args = event.args;
		var pagenumber = args.pagenum;
		var pagesize = args.pagesize;
		$('#pageSizeGrabing').val(pagesize);
	});
}

/**
 * Validate NewOrder Form
 */
function validateOrderForm(){
	var errors = 0;
	$('.errorText').remove();
	if($('#frm_order #exp_delivery_date').val() == ''){
		$( '<p class="errorText">Please expected delivery date</p>' ).insertAfter( '#frm_order #exp_delivery_date' );
		errors++;
	}
	/*if($('#cust_id').val() == ''){
		alert( 'Please select a customer' );
		errors++;
	}*/
	if($('#opp_id').val() == ''){
		$( '<p class="errorText">Please select an opportunity</p>' ).insertAfter( '#orderOpportunityLookup' );
		errors++;
	}
	return errors;
}

/**
 * Make ajax call to create order
 * form - is the form element to get form data
 */
function createOrder(form){
	var errors = validateOrderForm();
	if(errors == 0){
		var url = '/createorder';
		$('#order_save').attr('disabled', 'disabled');
		$.ajax({
			url: url,
			data: form.serialize(),
			type: 'POST',
			success: function(response){
				$('#order_save').removeAttr('disabled');			
				if(response > 0){
					$('#createOrder .closePopup').click();
					//$("#jqxOrders").jqxGrid('updatebounddata');
					window.location.href = '/orderdetails/'+response;
				}
			}
		});
	}
}

function validateJobPacketForm(){
	var errors = 0;	
	$('.errorText').remove();
	
	if($('#frm_job_packet #owner_id option:selected').val() == 0){
		$( '<span class="errorText">Please select record owner</span>' ).insertAfter( '#frm_job_packet #owner_id' );
		errors++;
	}
	
    if($('#frm_job_packet label.active').length == 0){
       $( '<span class="errorText">Please select milestones</span>' ).insertAfter( '#frm_job_packet #milestonesSpan' );
		errors++;
    }
	
	if($('#frm_job_packet .tableNew input[type=checkbox]').length > 0 && $('#frm_job_packet .tableNew input[type=checkbox]:checked').length == 0){
       $( '<span class="errorText">Please select items</span>' ).insertAfter( '#frm_job_packet .tableNew' );
		errors++;
    }
	
	return errors;
}

/**
 * Make ajax call to create job
 * form - is the form element to get form data
 */
function createJobPacket(form){
	$('#job_save').attr('disabled', 'disabled');
	var errors = validateJobPacketForm();
	if(errors == 0){
		var url = '/createjobpacket';
		$('#order_save').attr('disabled', 'disabled');
		$.ajax({
			url: url,
			data: form.serialize(),
			type: 'POST',
			async: false,
			success: function(response){
				$('#order_save').removeAttr('disabled');			
				if(response > 0){
					$('#createOrder .closePopup').click();
					//$("#jqxOrders").jqxGrid('updatebounddata');
					window.location.href = '/jobdetails/'+response;
				}
			}
		});
	}
	$('#job_save').removeAttr('disabled');
}

/**
 * Populates JQXGrid for jobs
 * recordsPerPage - Number of records to be displayed in a page
 * keyword - Search keyword
 * order_id
 * cust_id
 */

function getJobPackets(recordsPerPage, keyword, order_id, cust_id){
	//if(keyword){
		var url = '/ajaxjoblist?keyword='+keyword+'&order_id='+order_id+'&cust_id='+cust_id;
	//} else {
		//var url = '/ajaxjoblist';
	//}
	
	InitGrid();
	function InitGrid() {
		$('#jqxJobpackets').remove();
        $('#bindAfterThis').after('<div class="formTable manageMembers" id="jqxJobpackets"></div>');
	}
	// prepare the data
	var source =
	{
		datatype: "json",
		datafields: [
			{ name: 'job_id' },
			{ name: 'milestones_str' },
			{ name: 'milestones_completed_str' },
			{ name: 'exp_delivery_date' },
			{ name: 'no_of_milectones' },
			{ name: 'no_of_milectones_completed' },
			{ name: 'customer_name'},
			{ name: 'due_days'},
			{ name: 'milestone_progress'},
			{ name: 'reserve_time' },
			{ name: 'reserve_notes' },
			{ name: 'tracking_id' },
			{ name: 'inventory_status_name' },
			{ name: 'inventory_status_reason' },
			{ name: 'inventory_type' },
			{ name: 'inventory_tracking_status' },
			{ name: 'inventory_tracking_reason' },
			{ name: 'owner_name' },
		],
		//localdata: data,
		//id: 'id',
		cache: false,
		url: url,
		root: 'Rows',
		//sortcolumn: 'job_id',
		//sortdirection: 'desc',
		beforeprocessing: function (data) {
			source.totalrecords = data.TotalRows;
		},
		filter: function () {
			// update the grid and send a request to the server.
			$("#jqxJobpackets").jqxGrid('updatebounddata', 'filter');
		}
	};	
	
	var pageable = true, sortable = true;	
	var dataAdapter = new $.jqx.dataAdapter(source, {
		downloadComplete: function (data, status, xhr) {},
		loadComplete: function (data) { 
			if($("#jqxJobpackets").find('.jqx-grid-empty-cell').length>0 ){					
					if($("#jqxJobpackets").find('.jqx-grid-empty-cell').parent('div').height()<30){
						$('.jqx-grid-empty-cell').parent('div').addClass('norecordsFoundRow');
						$('.jqx-grid-empty-cell').closest("#jqxJobpackets").addClass('noInfoFound');
					}
					if(keyword){
						$("#jqxJobpackets").find('.jqx-grid-empty-cell >span').text("No matches were found");
						//$('#searchResultsHint').text("No matches were found");
					} else {
						$("#jqxJobpackets").find('.jqx-grid-empty-cell >span').text("No records found");
					}
			}else{
				if($("#jqxJobpackets").hasClass('noInfoFound')){
					$("#jqxJobpackets").removeClass('noInfoFound');
				}
			}
		},
		loadError: function (xhr, status, error) { }
	});
	
	var consign = function(row, columnfield, value, defaulthtml, columnproperties){
		var datarow = $('#jqxJobpackets').jqxGrid('getrowdata', row);
		var html = '';
		if(datarow)
			html = '<a href="javascript:;" data-popup="consignItem" class="cmnBtn lightBoxClick" onclick="openConsignForm('+datarow.job_id+', \'job\')">Consign</a>';
		return html;
	};
		
	$("#jqxJobpackets").jqxGrid(
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
		rowsheight:50,
		columnsheight:50,
		pagerheight:50,
		autorowheight: true,
		autoheight: true,
		enabletooltips: true,
		
		//headerheight: 60,
		virtualmode: true,
		//enablemousewheel: false,
		//pagermode: 'simple',
		//pager: '#gridpager',
		//columnsresize: true,
		rendergridrows: function (params) {
			return params.data;
		},
		
		columns: [
			{ text: 'Consignment', type: 'string', cellsrenderer: consign, width:'16%', sortable: false},
			{ text: 'Inventory Status', type: 'string', datafield: 'inventory_status_name', width:'15%'},
			{ text: 'Reason', type: 'string', datafield: 'inventory_status_reason', width:'15%' },
			{ text: 'Reserve Time', type: 'string', datafield: 'reserve_time', width:'15%' },
			{ text: 'Reserve Note', type: 'string', datafield: 'reserve_notes', width:'15%' },
			{ text: 'Inventory Type', type: 'string', datafield: 'inventory_type', width:'15%' },
			{ text: 'Tracking', type: 'string', datafield: 'inventory_tracking_status', width:'15%' },
			{ text: 'Tracking Reason', type: 'string', datafield: 'inventory_tracking_reason', width:'15%' },
			{ text: 'Tracking ID', type: 'string', datafield: 'tracking_id', width:'15%' },
			{ text: 'Job Number', type: 'string', datafield: 'job_id', width:'15%' },
			{ text: 'Customer <br /> Name', type: 'string', datafield: 'customer_name', width:'16%' },
			//{ text: 'Supplier', type: 'string', cellsrenderer: function(){return '-';}, width:'15%' },
			{ text: 'Due In (Days)', type: 'string', datafield: 'due_days', width:'14%' },
			{ text: 'Milestone <br /> Progress', type: 'string', datafield: 'milestone_progress', width:'15%' },
			{ text: 'Milestone', type: 'string', datafield: 'milestones_completed_str', width:'24%' },
			//{ text: 'Status', type: 'string', cellsrenderer: function(){return '-';}, width:'14%' },
			{ text: 'Due Date', type: 'string', datafield: 'exp_delivery_date', width:'16%' },
			{ text: 'Milestones <br /> Required', type: 'string', datafield: 'milestones_str', width:'24%' },
			{ text: 'Record Owner', type: 'string', datafield: 'owner_name', width:'15%' },
			//{ text: 'Costs <br /> Updated', type: 'string', cellsrenderer: function(){return '-';}, width:'14%' },
			//{ text: 'Activity', type: 'string', cellsrenderer: function(){return '-';}, width:'14%' },
		]

	});

	
	$('#jqxJobpackets').bind('cellclick', function(event)  {
		var current_index = event.args.rowindex;
		var current_column = event.args.column.datafield;
		var datarow = $('#jqxJobpackets').jqxGrid('getrowdata', current_index);
		var url = '/jobdetails/'+datarow.job_id;
		
		if(current_column != null){
			$(location).attr('href', url);
		}
	});
	
	$("#jqxJobpackets").bind("sort", function (event) {
	    /*var sortinformation = event.args.sortinformation;
	    
	    var sortdatafield = sortinformation.sortcolumn;
	    
	    var sortorder = sortinformation.sortdirection.ascending ? "asc" : "desc";
	    
	    var sortUrl = '/ajaxjoblist?sortdatafield='+sortdatafield+'&sortorder='+sortorder;*/
	    
	   //$.get(sortUrl, function(source){
	    	//$("#jqxJobpackets").jqxGrid('databind', source);
	    	$("#jqxJobpackets").jqxGrid('updatebounddata', 'filter');
	   //});
	});
	
	$("#jqxJobpackets").bind("pagesizechanged", function (event) {
		var args = event.args;
		var pagenumber = args.pagenum;
		var pagesize = args.pagesize;
		$('#pageSizeGrabing').val(pagesize);
	});
}


/**
 * Open popup for edit order form
 * @param order_id
 */
function editOrderForm(order_id){
	var url = '/editorderform';
	$.ajax({
		url: url,
		type: 'POST',
		data: {order_id: order_id},
		success: function(response){
			$('#editOrder .lightBoxTitle').nextAll('div').remove();
			$(response).insertAfter('#editOrder .lightBoxTitle');
			$('#editOrder #order_cancel').attr('onclick', 'cancelButtonProperty(\'frm_order\', \'editOrder\');');
			lightboxmid();
		}
	});
}

/**
 * Make ajax request to delete an order
 * @param order_id
 */
function deleteOrder(order_id){
	var url = '/deleteorder';
	if(confirm('Are you sure to delete this order?')){
		$.ajax({
			url: url,
			type: 'POST',
			data: {order_id: order_id},
			success: function(response){
				if(response == 1)
					window.location.href = '/orders';
			}
		});
	}
}

/*Milestone Managment ==> CAD Design  ==> Save Create CAD Requirements*/

/**
 * CAD Milestone Stage One Validation Check
 */
function validateCADStageOne(){
	var errors = 0;	
	$('.errorText').remove();
	if($('#frm_cad_requirement #cad_supplier_id').val() == ''){
		$( '<p class="errorText">Please select supplier</p>' ).insertAfter( '#frm_cad_requirement #supplierLookupId' );
		errors++;
	}
	if($('#frm_cad_requirement .datepickerInput').val() == ''){
		$( '<p class="errorText">Please expected delivery date</p>' ).insertAfter( '#frm_cad_requirement .datePickCal' );
		errors++;
	}
	return errors;
}
/**
 * CAD Milestone Stage Two Validation Check
 */
function validateCADStageTwo(){
	var errors = 0;	
	//var stage = checkCADStage($('#data_job_id').val());
	$('.errorText').remove();
	/*if(stage == 0){
		$( '<span class="errorText">Please complete stage one</span>' ).insertAfter( '#frm_cad_files #stageTwo' );
		errors++;
	}*/
	var multipleimagesHiddenVal = JSON.parse($('#frm_cad_files #multipleimagesHidden').val());
	if(multipleimagesHiddenVal == ''){
		$( '<p class="errorText">Please upload images</p>' ).insertAfter( '#frm_cad_files #cad_steptwo_image_preview' );
		errors++;
	}
	return errors;
}
/**
 * CAD Milestone Stage Three Validation Check
 */
function validateCADStageThree(){
	var errors = 0;	
	//var stage = checkCADStage($('#data_job_id').val());
	$('.errorText').remove();
	/*if(stage == 0){
		$( '<span class="errorText">Please complete stage one</span>' ).insertAfter( '#frm_cad_review #stageThree' );
		errors++;
	} else if(stage == 1){
		$( '<span class="errorText">Please complete stage two</span>' ).insertAfter( '#frm_cad_review #stageThree' );
		errors++;
	}*/
	if($('#frm_cad_review #cad_client_review_image_data').val() == ''){
		$( '<p class="errorText">Please upload image</p>' ).insertAfter( '#frm_cad_review #cad_client_review_image_preview' );
		errors++;
	}
	return errors;
}

/**
 * Make ajax request to get stage of CAD MileStone
 */
function checkCADStage(jobId){
		var dynamicUrl = '/getcaddesignstage';
		var data = {'job_id':jobId};
		var stage = 0;
		$.ajax({
			url: dynamicUrl,
			type: 'POST',
			async: false,
			data: data,
			success: function(response){
				stage = response;
			}
		});
		
		if(stage > 0){
			return stage;
		} else {
			return 0;
		}
}

/**
 * CAD Milestone Stages Saving
 */
function saveCADRequirements(form, stage, elem){
	var errors = 0;
	elem.attr('disabled', 'disabled');
	if(stage == 1){
		var errors = validateCADStageOne();
	} else if(stage == 2){
		var errors = validateCADStageTwo();
	} else if(stage == 3){
		var errors = validateCADStageThree();
	}
	var saveResponse = 0;
	var dynamicUrl = '/savecaddesign';
	if(errors == 0){
		var url = dynamicUrl;
		var data = $('#'+form).serialize();
		$.ajax({
			url: dynamicUrl,
			type: 'POST',
			async: false,
			data: data,
			success: function(response){
				saveResponse = response;
			}
		});
		if(saveResponse > 0){
			elem.removeClass('btnEmpty').addClass('blueBtn2');
			elem.text('Marked Step As Complete');
			$('<span class="emailSentIcon"></span>').insertAfter( elem );
			if(stage == 1){
				$('#CADstageOne').attr('disabled', 'disabled');
				$('#frm_cad_requirement #multipleattachments').attr('disabled', 'disabled');
				$('#CADstageTwo').removeAttr('disabled');
				$('#frm_cad_files #caddesign_table_id').val(saveResponse);
				$('#frm_cad_files #multipleimages').removeAttr('disabled');
				$('.closeImg').show();
			} else if(stage == 2){
				$('#CADstageTwo').attr('disabled', 'disabled');
				$('.closeImg').hide();
				$('#frm_cad_files #multipleimages').attr('disabled', 'disabled');
				$('#CADstageThree').removeAttr('disabled');
				$('#cad_client_review_image').removeAttr('disabled');
				$('#frm_cad_review #caddesign_table_id').val(saveResponse);
				$('#frm_cad_requirement #multipleattachments').attr('disabled', 'disabled');
			} else if(stage == 3){
				$('#CADstageThree').attr('disabled', 'disabled');
				$('#frm_cad_requirement #multipleattachments').attr('disabled', 'disabled');
				$('#cad_client_review_image').attr('disabled', 'disabled');
				$('#cadHeaderBlock').addClass("completed");
				$('#cadMainDiv').hide();
				
				elem.closest('.productionStep').next('li.productionStep').addClass('editStep');
				elem.closest('.productionStep').next('li.productionStep').find('.title').click();
			}
			return stage;
		} else {
			elem.removeAttr('disabled');
			//return 0;
		}
	} else {
		elem.removeAttr('disabled');
	}
}

/**
 * Validate step 1 submit for prototype
 * return count of errors
 */
function validatePrototypeStep1(){
	var errors = 0;
	$('.errorText').remove();
	
	if($('#prototype_exp_delivery_date').val() == ''){
		$( '<p class="errorText">Please select a date</p>' ).insertAfter( '#prototype_exp_delivery_date' );
		errors++;
	}
	return errors;
}

/**
 * Make ajax request to store 1st step of PrototypeMile Stone
 */
function savePrototypeStep1(elem, form){
	
	var url = '/prototypestep1';
	elem.attr('disabled', 'disabled');
	var errors = validatePrototypeStep1();
	
	if(errors == 0){
		$.ajax({
			url: url,
			type: 'POST',
			async: false,
			data: form.serialize(),
			success: function(response){
				if(response > 0){
					//alert('Prototype Step 1 is completed');
					elem.removeClass('btnEmpty').addClass('blueBtn2');
					elem.text('Marked Step As Complete');
					$('<span class="emailSentIcon"></span>').insertAfter( elem );
				}else{
					elem.removeAttr('disabled');	
				}
			}
		});
	}else{
		elem.removeAttr('disabled');
	}
}

/**
 * Make ajax request to store 2nd step of PrototypeMile Stone
 */
function  savePrototypeStep2(elem, form){
	$('.errorText').remove();
	var url = '/prototypestep2';
	elem.attr('disabled', 'disabled');
	$.ajax({
		url: url,
		type: 'POST',
		async: false,
		data: form.serialize(),
		success: function(response){
			if(response > 0){
				elem.removeClass('btnEmpty').addClass('blueBtn2');
				elem.text('Marked Step As Complete');
				$('<span class="emailSentIcon"></span>').insertAfter( elem );
				
				$('#divPrototype').hide();
				elem.closest('.information').siblings('.title').addClass('completed');
				
				elem.closest('.productionStep').next('li.productionStep').addClass('editStep');
				elem.closest('.productionStep').next('li.productionStep').find('.title').click();
			}else{
				$('.errorText').remove();
				$( '<span class="errorText">Please complete stage one</span>' ).insertAfter( elem );
				elem.removeAttr('disabled');	
			}
		}
	});
}

/**
 * Validate step 1 submit for prototype
 * return count of errors
 */
function validateCastStep1(){
	var errors = 0;
	$('.errorText').remove();
	
	if($('#cast_exp_delivery_date').val() == ''){
		$( '<p class="errorText">Please select a date</p>' ).insertAfter( '#cast_exp_delivery_date' );
		errors++;
	}
	return errors;
}

/**
 * Make ajax request to store 1st step of PrototypeMile Stone
 */
function saveCastStep1(elem, form){
	var url = '/caststep1';
	elem.attr('disabled', 'disabled');
	var errors = validatePrototypeStep1();
	
	if(errors == 0){
		$.ajax({
			url: url,
			type: 'POST',
			async: false,
			data: form.serialize(),
			success: function(response){
				if(response > 0){
					elem.removeClass('btnEmpty').addClass('blueBtn2');
					elem.text('Marked Step As Complete');
					$('<span class="emailSentIcon"></span>').insertAfter( elem );
				}else{
					elem.removeAttr('disabled');	
				}
			}
		});
	}else{
		elem.removeAttr('disabled');
	}
}

/**
 * Make ajax request to store 2nd step of PrototypeMile Stone
 */
function saveCastStep2(elem, form){
	$('.errorText').remove();
	var url = '/caststep2';
	elem.attr('disabled', 'disabled');
	$.ajax({
		url: url,
		type: 'POST',
		async: false,
		data: form.serialize(),
		success: function(response){
			if(response > 0){
				elem.removeClass('btnEmpty').addClass('blueBtn2');
				elem.text('Marked Step As Complete');
				$('<span class="emailSentIcon"></span>').insertAfter( elem );
				
				$('#divCast').hide();
				
				elem.closest('.information').siblings('.title').addClass('completed');
				
				elem.closest('.productionStep').next('li.productionStep').addClass('editStep');
				elem.closest('.productionStep').next('li.productionStep').find('.title').click();
			}else{
				$('.errorText').remove();
				$( '<span class="errorText">Please complete stage one</span>' ).insertAfter( elem );
				elem.removeAttr('disabled');	
			}
		}
	});
}

/**
 * Add html for metal type
 */
function addMetalType(elem, selectedOptElem, selectElemId){
	elem.append('<span><input type="hidden" name="metal_types[]" value="'+selectedOptElem.val()+'"><a href="javascript:;" onclick="deleteMetalType($(this));">-</a>'+selectedOptElem.text()+'</span>');
	
	$('#'+selectElemId+' option[value="0"]').attr('selected', 'selected');
	$('#'+selectElemId).dropkick('refresh');	
}

/**
 * Remove html for metal type
 */
function deleteMetalType(elem){
	elem.parent('span').remove();
}

function addJobType(elem, supplierNo){
	if(elem.is(':checked') == true){
		var html = '<div class="typeJobInfo" id="jobtype_'+supplierNo+'_'+elem.val()+'"><a href="javascript:;" class="deleteJob" >'+elem.parent('label').text()+'</a><div class="formRow"><label class="labelControll">Expected Cost</label><div class="inputDiv"><input type="text" name="cost_'+elem.val()+'" class="inputTxt width100" data-numeric="yes" placeholder="0.00"><div class="fl width100p padT10"><input type="text" name="task_'+elem.val()+'" class="inputTxt width60p taskInput"><a class="cmnBtn blueBtn" href="javascript:;" onclick="addSupplierTask($(this), '+elem.val()+');">Add Task</a><div class="addedInform"></div></div></div></div></div>';
		elem.closest('.commonForm').find('div.typeJobList').append(html);
	}else{
		if($(elem).closest('.suppliersListCount').find('div[id="'+$(elem).attr('data-typejob')+'"]').length > 0){
			$(elem).closest('.suppliersListCount').find('div[id="'+$(elem).attr('data-typejob')+'"]').remove();
		}
	}
}

$(document).on('click','.typeJobInfo > a.deleteJob', function(){
	$(this).closest('.suppliersListCount').find('input[data-typejob="'+$(this).parent().attr('id')+'"]').removeAttr('checked').parent('label').removeClass('active');
	$(this).parent().remove();
});

function supplierLookupForWorkshop(gridContainer, recordsPerPage, keyword, job_id){
	$('#searchResultsHint').text('');
	if(keyword){
		var url = '/ajaxgetsuppliers?keyword='+keyword;
	} else {
		var url = '/ajaxgetsuppliers';
	}
	//InitGrid();
	function InitGrid() {
		$('#'+gridContainer).remove();
        $('#bindAfterThis').after('<div class="formTable manageMembers" id="'+gridContainer+'"></div>');
	}
	// prepare the data
	var source =
	{
		datatype: "json",
		datafields: [
			{ name: 'id' },
			{ name: 'company_name' },
			{ name: 'first_name' },
			{ name: 'last_name' },
			{ name: 'email' },
			{ name: 'mobile' },
			{ name: 'phone' },
			{ name: 'service_name' },
			{ name: 'created_date' },
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
			$('#'+gridContainer).jqxGrid('updatebounddata', 'filter');
		}
	};
	
	var pageable = true, sortable = true;
	var dataAdapter = new $.jqx.dataAdapter(source, {
		downloadComplete: function (data, status, xhr) {},
		loadComplete: function (data) { 
			if($('#'+gridContainer).find('.jqx-grid-empty-cell').length>0){
					if($('#'+gridContainer).find('.jqx-grid-empty-cell').parent('div').height()<30){
						$('.jqx-grid-empty-cell').parent('div').addClass('norecordsFoundRow');
						$('.jqx-grid-empty-cell').closest("#jqxSuppliers").addClass('noInfoFound');
					}
					if(keyword){
						$('#'+gridContainer).find('.jqx-grid-empty-cell >span').text("No matches were found");
					} else {
						$('#'+gridContainer).find('.jqx-grid-empty-cell >span').text("No records found");
					}
			}else{
				if($('#'+gridContainer).hasClass('noInfoFound')){
					$('#'+gridContainer).removeClass('noInfoFound');
				}
			} 
		},
		loadError: function (xhr, status, error) { }
	});
		
	$('#'+gridContainer).jqxGrid(
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
		
		columns: [
			{ text: 'Date<br/>Created', type: 'string', datafield: 'created_date', width:'10%' },
			{ text: 'Company<br />Name', type: 'string', datafield: 'company_name', width:'12%' },
			{ text: 'First Name', type: 'string', datafield: 'first_name', width:'13%' },
			{ text: 'Last Name', type: 'string', datafield: 'last_name', width:'13%' },
			{ text: 'Email', type: 'string', datafield: 'email', width:'17%'},
			{ text: 'Number', type: 'string', datafield: 'phone', width:'11%' },
			{ text: 'Mobile No.', type: 'string', datafield: 'mobile', width:'11%' },
			{ text: 'Services<br/>Offered', type: 'string', datafield: 'service_name', width:'25%' },
		]

	});

	$('#'+gridContainer).bind('cellclick', function(event) {
		//var jobCount = $('#suppliersList .suppliersListCount').length + 1;
		var jobCount = $('#suppliersList .suppliersListCount').length==0 ? 1 : parseInt($('#suppliersList .suppliersListCount:last-child').attr('data-count')) + 1;
			
		var jobList = [{id: 1, type: 'Engrave'}, {id: 2, type: 'Polish'}, {id: 3, type: 'Setting'}, {id: 4, type: 'Value'}, {id: 5, type: 'Assemble'}, {id: 6, type: 'Other'}];
		
		var jobListHtml='';
		for(i = 0; i < jobList.length; i++){
			jobListHtml = jobListHtml + '<label class="checkinput"><input type="checkbox" name="job_type_id[]" data-typeJob="jobtype_'+jobCount+'_'+jobList[i].id+'" value="'+jobList[i].id+'" onclick="addJobType($(this), '+jobCount+');" />'+jobList[i].type+'</label>';
		}
		
		var current_index = event.args.rowindex;
		var current_column = event.args.column.datafield;
		var datarow = $('#'+gridContainer).jqxGrid('getrowdata', current_index);

		/*$('#'+form_name+' #'+supplier_id_hidden).val(datarow.id);
		$('#'+form_name+' #'+supplier_name_view).val(datarow.first_name+' '+datarow.last_name);
		$('#supplierLookup .closePopup').click();*/
		
		var  html='<div data-count="'+jobCount+'" class="suppliersListCount"><h3 class="subheading"><em class="countSuple"></em>'+datarow.first_name+' '+datarow.last_name+'<a href="javascript:;" class="cmnBtn deleteSupplier deleteBtn fr marT0">Delete</a></h3><form name="frm_workshop_supplier'+jobCount+'" id="frm_workshop_supplier'+jobCount+'"><input type="hidden" name="job_id" value="'+job_id+'" /><input type="hidden" name="supplier_id" value="'+datarow.id+'" /><div class="commonForm rowSpace padB0"><div class="formRow"><label class="labelControll">Add Job Type</label><div class="inputDiv selectMilestone padTB0 typeJob">'+jobListHtml+'</div></div><div class="typeJobList"></div><div class="formRow "><label class="labelControll">*Expected Delivery Date</label><div class="inputDiv"><div class="selectData width150"><input type="text" name="exp_delivery_date" class="inputTxt width100p datepickerInput" /></div></div></div><div class="formRow"><label class="labelControll">DE Review</label><div class="inputDiv"><button type="button" class="cmnBtn btnEmpty marT0" onclick="saveSupplierTasks($(\'#frm_workshop_supplier'+jobCount+'\'), $(this));">Complete Task</button></div></div></div></form></div>';
		$('#suppliersList').append(html);
		
		//$('#suppliersList .suppliersListCount:last-child').find('.splCount').text(listCount);
		//var formName=$('#suppliersList .suppliersListCount:last-child').find('form').attr('name');
		$('#supplierLookup .closePopup').click();
		$('.datepickerInput').datepicker({
            dateFormat: jsDateFormat,
            changeYear: true,
            yearRange: "-100:+0",
            beforeShow: function () {
                $(this).after($(this).datepicker("widget"));
            }
        });
	});
	
	$('#'+gridContainer).bind("sort", function (event) {
	    /*var sortinformation = event.args.sortinformation;
	    
	    var sortdatafield = sortinformation.sortcolumn;
	    
	    var sortorder = sortinformation.sortdirection.ascending ? "asc" : "desc";
	    
	    var sortUrl = '/ajaxgetsuppliers?sortdatafield='+sortdatafield+'&sortorder='+sortorder;*/
	    
	   //$.get(sortUrl, function(source){
	    	//$('#'+gridContainer).jqxGrid('databind', source);
	    	$('#'+gridContainer).jqxGrid('updatebounddata', 'filter');
	   //});
	});
	
	$('#'+gridContainer).bind("pagesizechanged", function (event) {
		var args = event.args;
		var pagenumber = args.pagenum;
		var pagesize = args.pagesize;
		$('#pageSizeGrabing').val(pagesize);
	});	
}

$(document).on('click','.deleteSupplier', function(){
    if (confirm("Are you want to delete this Supplier ?") == true) {
        $(this).closest('.suppliersListCount').remove();
    } else {
        //x = "You pressed Cancel!";
    }
});


/**
 * Make ajax to save step1 for workshop milestone
 */
function saveWorkshopStep1(elem,form){
	var url = '/workshopstep1';
	elem.attr('disabled', 'disabled');
	$.ajax({
		url: url,
		type: 'POST',
		async: false,
		data: form.serialize(),
		success: function(response){
			if(response){
				elem.removeClass('btnEmpty').addClass('blueBtn2');
				elem.text('Marked Step As Complete');
				$('<span class="emailSentIcon"></span>').insertAfter( elem );
			}else{
				elem.removeAttr('disabled');	
			}
		}
	});
}

/**
 * Add task html for suppliers in workshop
 */
function addSupplierTask(elem, task_id){
	if(elem.siblings('.taskInput').val() != ''){
		elem.siblings('.addedInform').append('<span><input type="hidden" name="taskinfo_'+task_id+'[]" value="'+elem.siblings('.taskInput').val()+'" /><a href="javascript:;" onclick="$(this).parent().remove()">-</a>'+elem.siblings('.taskInput').val()+'</span>');
		elem.siblings('.taskInput').val('').focus();
	}else{
		alert('Please Enter Task');
		elem.siblings('.taskInput').focus();
	}
}

/**
 * Validate Supplier Info in workshop
 */
function validateWorkshopSuppliers(form){	
	var errors = 0;
	$('.errorText').remove();
	
	if(form.find("input[name=exp_delivery_date]").val() == ''){
		$( '<p class="errorText">Please select a date</p>' ).insertAfter( form.find("input[name=exp_delivery_date]") );
		errors++;
	}
		
	form.find("input[name='job_type_id[]']:checked").each(function () {
		if(form.find("input[name='taskinfo_"+$(this).val()+"[]']").length == 0){
			$( '<p class="errorText">Please add a task</p>' ).insertAfter( form.find("input[name='task_"+$(this).val()+"']") );
			errors++;
		}
		if(form.find("input[name='cost_"+$(this).val()+"']").val() == ''){
			$( '<p class="errorText">Please enter an amount</p>' ).insertAfter( form.find("input[name='cost_"+$(this).val()+"']") );
			errors++;
		}
	});
	
	return errors;
}

/**
 * Make ajax call to store supplir and assigned task
 */
function saveSupplierTasks(form, elem){
	var errors = validateWorkshopSuppliers(form);
	
	if(errors == 0){
		var url = '/savesuppliertask';
		elem.attr('disabled', 'disabled');
		$.ajax({
			url: url,
			type: 'POST',
			async: false,
			data: form.serialize(),
			success: function(response){
				if(response > 0){
					elem.removeClass('btnEmpty').addClass('blueBtn2');
					elem.text('Marked Step As Complete');
					$('<span class="emailSentIcon"></span>').insertAfter( elem );
				}else{
					$( '<span class="errorText">Please complete previous steage</span>' ).insertAfter( elem );
					elem.removeAttr('disabled');	
				}
			}
		});
	}
}

/**
 * Validate Quality Control stage in workshop
 */
function validateWorkshopQualityControl(){	
	var errors = 0;
	$('.errorText').remove();
	
	if($('#qa_reviewed_by :selected').val() == 0){
		$( '<p class="errorText">Please select reviewed by</p>' ).insertAfter( '#qa_reviewed_by' );
		errors++;
	}
	return errors;
}

/**
 * Make ajax request to store Quality Control step of Workshop Milestone
 */
function saveWorkshopQualityControl(elem, form){
	
	var errors = validateWorkshopQualityControl();
	
	if(errors == 0){
		var url = '/workshopqualitycontrol';
		elem.attr('disabled', 'disabled');
		$.ajax({
			url: url,
			type: 'POST',
			async: false,
			data: form.serialize(),
			success: function(response){
				if(response > 0){
					elem.removeClass('btnEmpty').addClass('blueBtn2');
					elem.text('Marked Step As Complete');
					$('<span class="emailSentIcon"></span>').insertAfter( elem );
					
					elem.closest('.information').siblings('.title').addClass('completed');
				}else{
					$('.errorText').remove();
					$( '<span class="errorText">Please complete previous steage</span>' ).insertAfter( elem );
					elem.removeAttr('disabled');	
				}
			}
		});
	}
}

/**
 * Make ajax request to store Final step of Workshop Milestone
 */
function saveWorkshopFinalStepControl(elem, form){
	var url = '/workshopfinalstep';
	elem.attr('disabled', 'disabled');
	$.ajax({
		url: url,
		type: 'POST',
		async: false,
		data: form.serialize(),
		success: function(response){
			if(response > 0){
				elem.removeClass('btnEmpty').addClass('blueBtn2');
				elem.text('Marked Step As Complete');
				$('<span class="emailSentIcon"></span>').insertAfter( elem );
				
				$('#divWorkshop').hide();
				
				elem.closest('.information').siblings('.title').addClass('completed');
				
				//elem.closest('.productionStep').next('li.productionStep').addClass('editStep');
				//elem.closest('.productionStep').next('li.productionStep').find('.title').click();
			}else{
				$('.errorText').remove();
				$( '<span class="errorText">Please complete previous stage</span>' ).insertAfter( elem );
				elem.removeAttr('disabled');	
			}
		}
	});
}

/**
 * Remove Workshop image
 */
function removeWorkshopImage(elem){
	elem.siblings("img").remove();
	elem.remove();
	$('#workshop_production_line_image_data').val('');
}

/**
 * Make ajax request to start a job
 * job_id, primary key
 * invoice_number
 */
function startJob(job_id, invoice_number){
	var url = '/startjob';
	
	$.ajax({
		url: url,
		type: 'POST',
		async: false,
		data: {'start_job_id': job_id, 'invoice_number': invoice_number},
		success: function(response){
			if(response == 1){
				$('.closePopup').click();
				$('.startzone').hide();
				$('.pausezone').show();
				$('.waitingzone').hide();
				$('.manageList').addClass('startprduction');
							
				$('.milestoneManagement .manageList').addClass('startprduction');
				$('.milestoneManagement .manageList li:first-child').addClass('editStep');
				$('.milestoneManagement .manageList li:first-child .title').click();
				
			}else if(response == 2){
				$('#startJobBtn').trigger('click');
			}
		}
	});
}

/**
 * Make ajax request to start a job
 * form, form element
 */
function startJobRequest(form){
	
	var url = '/startjobrequest';
	$.ajax({
		url: url,
		type: 'POST',
		async: false,
		data: form.serialize(),
		success: function(response){
			if(response == 3){
				$('.closePopup').click();
				$('.startzone').hide();
				$('.pausezone').hide();
				$('.waitingzone').show();
				alert('Admin will approve to start the job');
			}
		}
	});
}

/**
 * Make ajax request to start a job
 * form, form element
 */
function deleteJobPacket(order_id, job_id){
	if(confirm('Are yousure to delete this job?')){
		var url = '/deletejob';
		$.ajax({
			url: url,
			type: 'POST',
			async: false,
			data: {'job_id': job_id},
			success: function(response){
				if(response == 1){
					window.location.href = '/orderdetails/'+order_id;
				}
			}
		});
	}
}