var globalElem;
var globalForm;

$(document).on('click','.selectMilestone label', function(){
		if($(this).find('input[type="checkbox"]').length > 0){													  
		 if($(this).find('input[type="checkbox"]').is(':checked')){
			 $(this).addClass('active');
		 }else{
			 $(this).removeClass('active');
		 }
	}
	if($(this).find('input[type="radio"]').length > 0){
		var radioName=$(this).find('input[type="radio"]').attr('name');
		 if($(this).find('input[type="radio"]').is(':checked')){
			 $('.selectMilestone label input[name="'+radioName+'"]').parent('label').removeClass('active');
			 $(this).addClass('active');
		 }
	}
});


/**
 * Populates JQXGrid lookupo view for invoices
 */
function getInvoiceLookup(opp_id){
	if(opp_id)
		var url = '/ajaxinvoicelookup?opp_id='+opp_id;
	else
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
					/*if(keyword){
						$("#jqxInvoiceLookup").find('.jqx-grid-empty-cell >span').text("No matches were found");
					} else {*/
						$("#jqxInvoiceLookup").find('.jqx-grid-empty-cell >span').text("No records found");
					//}
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
	
	$('#jqxInvoiceLookup').bind('cellclick', function(event)  {
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
	$after = $('#jqxOrders').prev('#bindAfterThis');
	if ($after.length == 0) return;
	
	$('#jqxOrders').remove();
    $after.after('<div class="formTable manageMembers" id="jqxOrders"></div>');

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
			{ name: 'special_request' },
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
	
	$("#jqxOrders").jqxGrid(
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
					{ text: 'Description', type: 'string', datafield: 'comment', width:'14%'},
					{ text: 'Customer<br/>Name', type: 'string', datafield: 'customer_name', width:'14%'},
					{ text: 'Opportunity<br/>Name', type: 'string', datafield: 'opportunity_name', width:'12%'},
					{ text: 'Invoice/<br/>Quote No.', type: 'string', datafield: 'invoice_number', width:'10%' },
					{ text: 'Payment<br/>Completed', type: 'string', datafield: 'payment_made', width:'10%'},
					{ text: 'Owner', type: 'string', datafield: 'owner_name', width:'12%'},
					{ text: 'Created<br/>On', type: 'string', datafield: 'created_date', width:'10%'},
					{ text: 'Value', type: 'string', datafield: 'value', width:'8%'},
					{ text: 'Special<br/>Request', type: 'string', datafield: 'special_request', cellclassname: 'special', width:'10%'},
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
	    	$("#jqxOrders").jqxGrid('updatebounddata', 'filter');
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
		$( '<p class="errorText" style="white-space: nowrap;">Please expected delivery date</p>' ).insertAfter( '#frm_order #exp_delivery_date' );
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
	if($('#invoice_number').val() == ''){
		$( '<p class="errorText">Please select an invoice/quote</p>' ).insertAfter( '#orderInvoiceQuoteLookup' );
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
	
	var form = $('#frm_job_packet');	
	
	if(form.find('input[name=owner_id]').val()== 0 || form.find('input[name=owner_id]').val()==''){		
		$( '<span class="errorText" style="white-space: nowrap;">Please select record owner</span>' ).insertAfter($('#frm_job_packet').find('#jobOwner'));
		errors++;
	}
	
    if($('#milestonesSpan').length > 0 && $('#frm_job_packet label.active').length == 0){
       $( '<span class="errorText">Please select milestones</span>' ).insertAfter( '#frm_job_packet #milestonesSpan' );
		errors++;
    }
	
	if($('#exp_delivery_date').val() == ''){
		$( '<span class="errorText" style="white-space: nowrap;">Please select expected delivery date</span>' ).insertAfter( '#frm_job_packet #exp_delivery_date' );
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
        $('#bindJobListAfterThis').after('<div class="formTable manageMembers" id="jqxJobpackets"></div>');
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
			{ name: 'milestones_current_status' },
			{ name: 'milestones_supplier' },
			{ name: 'milestones_activity' },
			{ name: 'owner_name' },
			{ name: 'costs_updated' },
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
			{ text: 'Tracking', type: 'string', datafield: 'inventory_tracking_status', width:'15%' },
			{ text: 'Tracking Reason', type: 'string', datafield: 'inventory_tracking_reason', width:'15%' },
			{ text: 'Tracking ID', type: 'string', datafield: 'tracking_id', width:'15%' },
			{ text: 'Job Number', type: 'string', datafield: 'job_id', width:'15%' },
			//{ text: 'Customer <br /> Name', type: 'string', datafield: 'customer_name', width:'16%' },
			{ text: 'Supplier', type: 'string', datafield:'milestones_supplier', width:'15%' },
			{ text: 'Due In (Days)', type: 'string', datafield: 'due_days', width:'14%' },
			{ text: 'Milestone <br /> Progress', type: 'string', datafield: 'milestone_progress', width:'15%' },
			{ text: 'Milestone', type: 'string', datafield: 'milestones_completed_str', width:'24%' },
			{ text: 'Status', type: 'string', datafield: 'milestones_current_status', width:'15%' },
			{ text: 'Due Date', type: 'string', datafield: 'exp_delivery_date', width:'16%' },
			{ text: 'Milestones <br /> Required', type: 'string', datafield: 'milestones_str', width:'24%' },
			{ text: 'Costs <br /> Updated', type: 'string', datafield: 'costs_updated', width:'14%' },
			{ text: 'Activity', type: 'string', datafield: 'milestones_activity', width:'14%' },
			
			/*{ text: 'Inventory Status', type: 'string', datafield: 'inventory_status_name', width:'15%'},
			{ text: 'Reason', type: 'string', datafield: 'inventory_status_reason', width:'15%' },
			{ text: 'Reserve Time', type: 'string', datafield: 'reserve_time', width:'15%' },
			{ text: 'Reserve Note', type: 'string', datafield: 'reserve_notes', width:'15%' },
			{ text: 'Inventory Type', type: 'string', datafield: 'inventory_type', width:'15%' },
			{ text: 'Record Owner', type: 'string', datafield: 'owner_name', width:'15%' },*/
			
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
	if(confirm('Any job created under this order will be deleted. Are you sure you would like to proceed?')){
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
function validateCADStageOne(form){
	var errors = 0;	
	$('.errorText').remove();	
	if(form.find('input[name=supplier_id]').val() == ''){
		$( '<p class="errorText">Please select a supplier</p>' ).insertAfter( form.find('input[name=supplier_name]').siblings('input') );
		errors++;
	}
	if(form.find('input[name=exp_delivery_date]').val() == ''){
		$( '<p class="errorText">Please enter delivery date</p>' ).insertAfter( form.find('.datePickCal') );
		errors++;
	}	
	return errors;
}
/**
 * CAD Milestone Stage Two Validation Check
 */
function validateCADStageTwo(form){
	var errors = 0;	
	$('.errorText').remove();
	
	if(form.find('input[name=multipleimagesHidden]').val() != ''){
		var multipleimagesHiddenVal = JSON.parse(form.find('input[name=multipleimagesHidden]').val());
		if(multipleimagesHiddenVal == ''){
			$( '<p class="errorText">Please upload images</p>' ).insertAfter( form.find('.uploadedImages') );
			errors++;
		}
	}else{
		$( '<p class="errorText">Please upload images</p>' ).insertAfter( form.find('.uploadedImages') );
		errors++;
	}
	
	if(form.find('input[name=stp2_delivery_date]').val() == ''){
		$( '<p class="errorText">Please enter job delivered date</p>' ).insertAfter( form.find('input[name=stp2_delivery_date]') );
		errors++;
	}
	return errors;
}
/**
 * CAD Milestone Stage Three Validation Check
 */
function validateCADStageThree(form){
	var errors = 0;	
	$('.errorText').remove();
	
	if(form.find('input[name=multipleimagesHidden]').val() != ''){
		var multipleimagesHiddenVal = JSON.parse(form.find('input[name=multipleimagesHidden]').val());
		
		if(multipleimagesHiddenVal == ''){
			$( '<p class="errorText">Please upload images</p>' ).insertAfter( form.find('.uploadedImages') );
			errors++;
		}
	}else{
		$( '<p class="errorText">Please upload images</p>' ).insertAfter( form.find('.uploadedImages') );
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
	globalElem = elem;
	globalForm = form;
	
	var errors = 0;
	$('.errorText').remove();
	globalElem.attr('disabled', 'disabled');
	if(stage == 1){
		var errors = validateCADStageOne(globalForm);
	} else if(stage == 2){
		var errors = validateCADStageTwo(globalForm);
	} else if(stage == 3){
		var errors = validateCADStageThree(globalForm);
	}
	var saveResponse = 0;
	var dynamicUrl = '/savecaddesign';
	if(errors == 0){
		var url = dynamicUrl;
		var data = globalForm.serialize();
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
			globalElem.removeClass('btnEmpty').addClass('blueBtn2');
			globalElem.text('Marked Step As Complete');
			$('<span class="emailSentIcon"></span>').insertAfter( globalElem );
			
			disableElements(globalForm, globalElem);
			
			if(stage == 1){
				/*$('#CADstageOne').attr('disabled', 'disabled');
				$('#frm_cad_requirement #multipleattachments').attr('disabled', 'disabled');
				$('#CADstageTwo').removeAttr('disabled');
				$('#frm_cad_files #caddesign_table_id').val(saveResponse);
				$('#frm_cad_files #multipleimages').removeAttr('disabled');
				$('.closeImg').show();*/
			} else if(stage == 2){
				/*$('#CADstageTwo').attr('disabled', 'disabled');
				$('.closeImg').hide();
				$('#frm_cad_files #multipleimages').attr('disabled', 'disabled');
				$('#CADstageThree').removeAttr('disabled');
				$('#cad_client_review_image').removeAttr('disabled');
				$('#frm_cad_review #caddesign_table_id').val(saveResponse);
				$('#frm_cad_requirement #multipleattachments').attr('disabled', 'disabled');*/
			} else if(stage == 3){
				/*$('#CADstageThree').attr('disabled', 'disabled');
				$('#frm_cad_requirement #multipleattachments').attr('disabled', 'disabled');
				$('#cad_client_review_image').attr('disabled', 'disabled');
				$('#cadHeaderBlock').addClass("completed");
				$('#cadMainDiv').hide();*/
				
				globalElem.closest('.information').siblings('.title').addClass('completed');
				
				globalElem.closest('.productionStep').next('li.productionStep').addClass('editStep');
				globalElem.closest('.productionStep').next('li.productionStep').find('.title').click();
			}
			return stage;
		} else {
			$( '<span class="errorText">Please complete previous milestone / stage first</span>' ).insertAfter( globalElem );
			globalElem.removeAttr('disabled');
			//return 0;
		}
	} else {
		globalElem.removeAttr('disabled');
	}
}

/**
 * Validate step 1 submit for prototype
 * return count of errors
 */
function validatePrototypeStep1(form){
	var errors = 0;
	$('.errorText').remove();
	//alert(form.find(".addedInform").children('span').length);

	if(form.find(".addedInform").children('span').length == 0){
		$('<p class="errorText">Please select metal type</p>').insertAfter('.hideAdvance');
		errors++;
	}
	if(form.find('input[name=supplier_id]').val() == ''){
		$( '<p class="errorText nowrape">Please select a supplier</p>' ).insertAfter( form.find('input[name=supplier_name]').siblings('input') );
		errors++;
	}	
	
	if(form.find('input[name=exp_delivery_date]').val() == ''){
		$( '<p class="errorText nowrape">Please select a delivery date</p>' ).insertAfter( form.find('input[name=exp_delivery_date]') );
		errors++;
	}

	return errors;
}

/**
 * Make ajax request to store 1st step of PrototypeMile Stone
 */
function savePrototypeStep1(elem, form){
	globalElem = elem;
	globalForm = form;
	
	var url = '/prototypestep1';
	globalElem.attr('disabled', 'disabled');
	var errors = validatePrototypeStep1(globalForm);
	
	if(errors == 0){
		$.ajax({
			url: url,
			type: 'POST',
			async: false,
			data: globalForm.serialize(),
			success: function(response){
				if(response > 0){
					//alert('Prototype Step 1 is completed');
					globalElem.removeClass('btnEmpty').addClass('blueBtn2');
					globalElem.text('Marked Step As Complete');
					$('<span class="emailSentIcon"></span>').insertAfter( globalElem );
					
					disableElements(globalForm, globalElem);
				}else{
					$( '<span class="errorText">Please complete previous milestone / stage first</span>' ).insertAfter( globalElem );
					globalElem.removeAttr('disabled');	
				}
			}
		});
	}else{
		globalElem.removeAttr('disabled');
	}
}

/**
 * Make ajax request to store 2nd step of PrototypeMile Stone
 */
function savePrototypeStep2(elem, form){
	globalElem = elem;
	globalForm = form;
	var errors = 0;
	if(globalForm.find('input[name=date_delivered]').val() == ''){
		$( '<p class="errorText nowrape">Please enter delivered date</p>' ).insertAfter( globalForm.find('input[name=date_delivered]') );
		errors++;
	}
	if(errors > 0){
		return false;
	}
	
	$('.errorText').remove();
	var url = '/prototypestep2';
	globalElem.attr('disabled', 'disabled');
	$.ajax({
		url: url,
		type: 'POST',
		async: false,
		data: globalForm.serialize(),
		success: function(response){
			if(response > 0){
				globalElem.removeClass('btnEmpty').addClass('blueBtn2');
				globalElem.text('Marked Step As Complete');
				$('<span class="emailSentIcon"></span>').insertAfter( globalElem );
				
				$('#divPrototype').hide();
				globalElem.closest('.information').siblings('.title').addClass('completed');
				
				globalElem.closest('.productionStep').next('li.productionStep').addClass('editStep');
				globalElem.closest('.productionStep').next('li.productionStep').find('.title').click();
				
				disableElements(globalForm, globalElem);
			}else{
				$('.errorText').remove();
				$( '<span class="errorText">Please complete previous milestone / stage first</span>' ).insertAfter( globalElem );
				globalElem.removeAttr('disabled');	
			}
		}
	});
}

/**
 * Validate step 1 submit for prototype
 * return count of errors
 */
function validateCastStep1(form){
	var errors = 0;
	$('.errorText').remove();
	
	
    if(form.find(".addedInform").children('span').length == 0){
		$('<p class="errorText">Please select metal type</p>').insertAfter('.hideAdvance');
		errors++;
	}
	
	if(form.find('input[name=supplier_id]').val() == ''){
		$( '<p class="errorText nowrape">Please select a supplier</p>' ).insertAfter( form.find('input[name=supplier_name]').siblings('input') );
		errors++;
	}
	
	if(form.find('input[name=exp_delivery_date]').val() == ''){
		$( '<p class="errorText nowrape">Please select a delivery date</p>' ).insertAfter( form.find('input[name=exp_delivery_date]') );
		errors++;
	}
	if(form.find('input[name=date_delivered]').val() == ''){
		$( '<p class="errorText nowrape">Please enter job delivered date</p>' ).insertAfter( form.find('input[name=date_delivered]') );
		errors++;
	}
	return errors;
	
}

/**
 * Make ajax request to store 1st step of PrototypeMile Stone
 */
function saveCastStep1(elem, form){
	globalElem = elem;
	globalForm = form;
	
	var url = '/caststep1';
	globalElem.attr('disabled', 'disabled');
	var errors = validateCastStep1(globalForm);
	
	if(errors == 0){
		$.ajax({
			url: url,
			type: 'POST',
			async: false,
			data: globalForm.serialize(),
			success: function(response){
				if(response > 0){
					globalElem.removeClass('btnEmpty').addClass('blueBtn2');
					globalElem.text('Marked Step As Complete');
					$('<span class="emailSentIcon"></span>').insertAfter( globalElem );
					
					disableElements(globalForm, globalElem);
				}else{
					$( '<span class="errorText">Please complete previous milestone / stage first</span>' ).insertAfter( globalElem );
					globalElem.removeAttr('disabled');	
				}
			}
		});
	}else{
		globalElem.removeAttr('disabled');
	}
}

/**
 * Make ajax request to store 2nd step of PrototypeMile Stone
 */
function saveCastStep2(elem, form){
	globalElem = elem;
	globalForm = form;
	
	$('.errorText').remove();
	var url = '/caststep2';
	globalElem.attr('disabled', 'disabled');
	$.ajax({
		url: url,
		type: 'POST',
		async: false,
		data: globalForm.serialize(),
		success: function(response){
			if(response > 0){
				globalElem.removeClass('btnEmpty').addClass('blueBtn2');
				globalElem.text('Marked Step As Complete');
				$('<span class="emailSentIcon"></span>').insertAfter( globalElem );
				
				$('#divCast').hide();
				
				globalElem.closest('.information').siblings('.title').addClass('completed');
				
				globalElem.closest('.productionStep').next('li.productionStep').addClass('editStep');
				globalElem.closest('.productionStep').next('li.productionStep').find('.title').click();
				
				disableElements(globalForm, globalElem);
			}else{
				$('.errorText').remove();
				$( '<span class="errorText">Please complete previous milestone / stage first</span>' ).insertAfter( globalElem );
				globalElem.removeAttr('disabled');	
			}
		}
	});
}

/**
 * Add html for metal type
 */
function addMetalType(elem, selectedElem){
	
	if($(selectedElem).find('option:selected').val() == 0){
		alert('Please select a Metal Type');
		return;
	}
	elem.append('<span><input type="hidden" name="metal_types[]" value="'+$(selectedElem).find('option:selected').val()+'"><a href="javascript:;" onclick="deleteMetalType($(this));">-</a>'+$(selectedElem).find('option:selected').text()+'</span>');
	
	$(selectedElem).find('option[value="0"]').attr('selected', 'selected');
	selectedElem.dropkick('refresh');
}

/**
 * Remove html for metal type
 */
function deleteMetalType(elem){
	elem.parent('span').remove();
}

function addJobType(elem, supplierNo){
	if(elem.is(':checked') == true){
		var html = '<div class="typeJobInfo" id="jobtype_'+supplierNo+'_'+elem.val()+'"><a href="javascript:;" class="deleteJob" >'+elem.parent('label').text()+'</a><div class="formRow"><label class="labelControll">Expected Cost</label><div class="inputDiv"><input type="text" name="cost_'+elem.val()+'" class="inputTxt width100" data-numeric="yes"  data-btn-type="noDisable" placeholder="0.00"><div class="fl width100p padT10"><input type="text" name="task_'+elem.val()+'" class="inputTxt width60p taskInput"><input type="button" class="cmnBtn blueBtn" onclick="addSupplierTask($(this), '+elem.val()+');" value="Add Task" /><div class="addedInform"></div></div></div></div></div>';
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


function supplierLookupForWorkshop(gridContainer, recordsPerPage, keyword, job_id, milestone_id, elem){
	globalElem = elem;

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
			{ text: 'Services Offered', type: 'string', datafield: 'service_name', width:'25%' },
		]

	});

	$('#'+gridContainer).bind('cellclick', function(event) {
		//var jobCount = $('#suppliersList .suppliersListCount').length + 1;
		var jobCount = globalElem.closest('li').find('.suppliersListCount').length == 0 ? 1 : parseInt(globalElem.closest('li').find('.suppliersListCount:last-child').attr('data-count')) + 1;
			
		var jobList = [{id: 1, type: 'Engrave'}, {id: 2, type: 'Polish'}, {id: 3, type: 'Setting'}, {id: 4, type: 'Value'}, {id: 5, type: 'Assemble'}, {id: 6, type: 'Other'}];
		
		var jobListHtml='';
		for(i = 0; i < jobList.length; i++){
			jobListHtml = jobListHtml + '<label class="checkinput"><input type="checkbox" name="job_type_id[]" data-typeJob="jobtype_'+jobCount+'_'+jobList[i].id+'" value="'+jobList[i].id+'" onclick="addJobType($(this), '+jobCount+');" />'+jobList[i].type+'</label>';
		}
		
		var current_index = event.args.rowindex;
		var current_column = event.args.column.datafield;
		var datarow = $('#'+gridContainer).jqxGrid('getrowdata', current_index);

		var  html='<div data-count="'+jobCount+'" class="suppliersListCount"><h3 class="subheading"><em class="countSuple"></em>'+datarow.first_name+' '+datarow.last_name+'<a href="javascript:;" class="cmnBtn deleteSupplier deleteBtn fr marT0">Delete</a></h3><form name="frm_workshop_supplier'+jobCount+'" id="frm_workshop_supplier'+jobCount+'"><input type="hidden" name="milestone_id" value="'+milestone_id+'" /><input type="hidden" name="milestone_type_id" value="4" /><input type="hidden" name="job_id" value="'+job_id+'" /><input type="hidden" name="supplier_id" value="'+datarow.id+'" /><div class="commonForm rowSpace padB0"><div class="formRow"><label class="labelControll">Add Job Type</label><div class="inputDiv selectMilestone padTB0 typeJob">'+jobListHtml+'</div></div><div class="typeJobList"></div><div class="formRow "><label class="labelControll">*Expected Delivery Date</label><div class="inputDiv"><div class="datePickInput width150"><input type="text" name="exp_delivery_date" class="datepickerInput" placeholder="DD/MM/YYYY" readonly="readonly" /><span class="datePickCal"></span></div></div></div><div class="formRow"><label class="labelControll">DE Review</label><div class="inputDiv"><button type="button" class="cmnBtn btnEmpty marT0" onclick="saveSupplierTasks($(\'#frm_workshop_supplier'+jobCount+'\'), $(this));">Complete Task</button></div></div></div></form></div>';
		
		//elem.closest('li.productionStep').find('.suppliersList').html();
		globalElem.closest('form').siblings('.suppliersList').append(html);
		
		$('#supplierLookup .closePopup').click();
		$('.datepickerInput').datepicker({
            dateFormat: jsDateFormat,
            changeYear: true,
            yearRange: "-100:+1",
            beforeShow: function () {
                $(this).after($(this).datepicker("widget"));
            }
        });
	});
	
	$('#'+gridContainer).bind("sort", function (event) {
	    $('#'+gridContainer).jqxGrid('updatebounddata', 'filter');
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
function validateWorkshopStep1(form){
	var errors = 0;	
	$('.errorText').remove();
	
	if(form.find('input[name=multipleimagesHidden]').val() != ''){
		var multipleimagesHiddenVal = JSON.parse(form.find('input[name=multipleimagesHidden]').val());
		if(multipleimagesHiddenVal == ''){
			$( '<p class="errorText">Please upload images</p>' ).insertAfter( form.find('.uploadedImages') );
			errors++;
		}
	}else{
		$( '<p class="errorText">Please upload images</p>' ).insertAfter( form.find('.uploadedImages') );
		errors++;
	}
	return errors;
}
/**
 * Make ajax to save step1 for workshop milestone
 */
function saveWorkshopStep1(elem, form){
	globalElem = elem;
	globalForm = form;
	
	var url = '/workshopstep1';
	globalElem.attr('disabled', 'disabled');
	
	var errors = validateWorkshopStep1(globalForm);
	if(errors == 0){
		$.ajax({
			url: url,
			type: 'POST',
			async: false,
			data: globalForm.serialize(),
			success: function(response){
				if(response){
					globalElem.removeClass('btnEmpty').addClass('blueBtn2');
					globalElem.text('Marked Step As Complete');
					$('<span class="emailSentIcon"></span>').insertAfter( globalElem );
					
					disableElements(globalForm, globalElem);
				}else{
					globalElem.removeAttr('disabled');	
				}
			}
		});
	}else{
		globalElem.removeAttr('disabled');
	}
}

/**
 * Add task html for suppliers in workshop
 */
function addSupplierTask(elem, task_id){
	if(elem.siblings('.taskInput').val() != ''){
		elem.siblings('.addedInform').append('<span><input type="hidden" name="taskinfo_'+task_id+'[]" value="'+elem.siblings('.taskInput').val()+'" /><a href="javascript:;" onclick="$(this).parent().remove()" class="anchorDisable">-</a>'+elem.siblings('.taskInput').val()+'</span>');
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
		$( '<p class="errorText nowrape">Please select expected delivery date</p>' ).insertAfter( form.find("input[name=exp_delivery_date]") );
		errors++;
	}
		
	form.find("input[name='job_type_id[]']:checked").each(function () {
		if(form.find("input[name='taskinfo_"+$(this).val()+"[]']").length == 0){
			$( '<p class="errorText">Please add a task</p>' ).insertAfter( form.find("input[name='task_"+$(this).val()+"']").siblings('input') );
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
	globalElem = elem;
	globalForm = form;
	
	var errors = validateWorkshopSuppliers(globalForm);
	
	if(errors == 0){
		var url = '/savesuppliertask';
		globalElem.attr('disabled', 'disabled');
		$.ajax({
			url: url,
			type: 'POST',
			async: false,
			data: globalForm.serialize(),
			success: function(response){
				if(response != 0){
					globalElem.removeClass('btnEmpty').addClass('blueBtn2');
					globalElem.text('Marked Step As Complete');
					$('<span class="emailSentIcon"></span>').insertAfter( globalElem );
					
					disableElements(globalForm, globalElem);
					
					var json_response = JSON.parse(response);
					
					for(i = 0; json_response.tasks.length > i; i++){
						$( '<a class="cmnBtn blueBtn" href="javascript:;" onclick="updateTaskCost('+json_response.id+', '+json_response.milestone_id+', '+json_response.tasks[i]+', $(this).siblings(\'input\').val());">Update Cost</a>' ).insertAfter(globalForm.find('input[name=cost_'+json_response.tasks[i]+']'));
					}
					
				}else{
					$( '<span class="errorText">This stage cannot be completed</span>' ).insertAfter( globalElem );
					globalElem.removeAttr('disabled');	
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
	
	globalElem = elem;
	globalForm = form;
	
	var errors = 0; //validateWorkshopQualityControl();
	
	if(errors == 0){
		var url = '/workshopqualitycontrol';
		globalElem.attr('disabled', 'disabled');
		$.ajax({
			url: url,
			type: 'POST',
			async: false,
			data: globalForm.serialize(),
			success: function(response){
				if(response > 0){
					globalElem.removeClass('btnEmpty').addClass('blueBtn2');
					globalElem.text('Marked Step As Complete');
					$('<span class="emailSentIcon"></span>').insertAfter( globalElem );
					
					globalElem.closest('.information').siblings('.title').addClass('completed');
					
					disableElements(globalForm, globalElem);
				}else{
					$('.errorText').remove();
					$( '<span class="errorText">This stage cannot be completed</span>' ).insertAfter( globalElem );
					globalElem.removeAttr('disabled');	
				}
			}
		});
	}
}

/**
 * Make ajax request to store Final step of Workshop Milestone
 */
function saveWorkshopFinalStepControl(elem, form){
	globalElem = elem;
	globalForm = form;
	
	var url = '/workshopfinalstep';
	globalElem.attr('disabled', 'disabled');
	$.ajax({
		url: url,
		type: 'POST',
		async: false,
		data: globalForm.serialize(),
		success: function(response){
			if(response > 0){
				globalElem.removeClass('btnEmpty').addClass('blueBtn2');
				globalElem.text('Marked Step As Complete');
				$('<span class="emailSentIcon"></span>').insertAfter( globalElem );
				
				$('#divWorkshop').hide();
				
				globalElem.closest('.information').siblings('.title').addClass('completed');
				
				globalElem.closest('.productionStep').next('li.productionStep').addClass('editStep');
				globalElem.closest('.productionStep').next('li.productionStep').find('.title').click();
				
				disableElements(globalForm, globalElem);
			}else{
				$('.errorText').remove();
				$( '<span class="errorText">Please complete previous stage</span>' ).insertAfter( globalElem );
				globalElem.removeAttr('disabled');	
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
	$('.pageLoader').show();
	var url = '/startjobrequest';
	$.ajax({
		url: url,
		type: 'POST',
		async: false,
		data: form.serialize(),
		success: function(response){
			$('.pageLoader').hide();
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
	if(confirm('Are you sure you want to delete this record? This action cannot be undone')){
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

function supplierLookupForMilestone(gridContainer, recordsPerPage, keyword, customerId, form, supplier_id_hidden, supplier_name_view){
	globalForm = form;
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
						//$('#searchResultsHint').text("No matches were found");
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

	$('#'+gridContainer).bind('cellclick', function(event)  {
		var current_index = event.args.rowindex;
		var current_column = event.args.column.datafield;
		var datarow = $('#'+gridContainer).jqxGrid('getrowdata', current_index);
		
		globalForm.find("input[name='"+supplier_id_hidden+"']").val(datarow.id);
		globalForm.find("input[name='"+supplier_name_view+"']").val(datarow.first_name+' '+datarow.last_name);
		
		$('#supplierLookup .closePopup').click();
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

/**
 * Delete file from server
 * filename, elem, container
 */
function deleteFile(filename, elem, container){
	globalElem = elem;
	var data = {'fileFullData': filename, 'order_attachments': 0};
	$.ajax({
		url: '/unlinkfile',
		type: 'POST',
		async: false,
		data: data,
		success: function(response){
			if(response){
				existingJsonValue = JSON.parse(globalElem.val());
				existingJsonValue.splice( $.inArray(filename, existingJsonValue), 1 );
				existingJsonValue = JSON.stringify(existingJsonValue);
				globalElem.val(existingJsonValue);
			}
		}
	});
	
	container.remove();
}

/**
 * Add milestone just below to the current milestone
 * current_conainer = current milestone li
 * current_milestone_id = milestone id
 * job_id
 * milestone_type = type of milestone
 * recordsPerPage = number
 * optionalData = json data to pass additional data to populate html
 */

function addMilestone(current_conainer, current_milestone_id, job_id, milestone_type, recordsPerPage, optionalData){
	$('.errorText').remove();
	if(milestone_type > 0){
		var data = {current_milestone_id: current_milestone_id, job_id: job_id, milestone_type_id: milestone_type};
		$.ajax({
			url: '/addmilestone',
			type: 'POST',
			async: false,
			data: data,
			success: function(response){
				if(response > 0){
					
					current_conainer.next('li.productionStep').find('div.information').hide();
					current_conainer.next('li.productionStep').removeClass('editStep');					
					current_conainer.next('li.productionStep').find('div.title').removeClass('active');
					
					var milestone_id = response;
					if(milestone_type == 1){
						var html = '<li class="productionStep editStep"><div class="title active" id="cadHeaderBlock">CAD Design<button type="button" class="cmnBtn deleteBtn" onclick="deleteMilestone('+milestone_id+', '+milestone_type+', $(this));">Delete</button></div><div class="information" style="display:block;"><h3 class="subheading">1.Create CAD Requirements <span class="hlpInfo"><em>?</em> <span>lipsum is a dummy content</span></span></h3><form name="frm_cad_requirement" enctype="multipart/form-data" action="/uploadmultiplefile"><input type="hidden" name="milestone_id" value="'+milestone_id+'"/><input type="hidden" name="milestone_type_id" value="'+milestone_type+'"/><input type="hidden" name="job_id" value="'+job_id+'"/><input type="hidden" name="steps_completed" value="1"/><input type="hidden" name="supplier_id" value=""/><div class="commonForm rowSpace padB0"><div class="formRow"><label class="labelControll">Supplier Name*</label><div class="inputDiv"><input type="text" class="inputTxt width60p" name="supplier_name" readonly="readonly" value=""><input type="button" class="cmnBtn blueBtn lightBoxClick" data-popup="supplierLookup" onclick="supplierLookupForMilestone(\'supplierLookupGrid\', '+recordsPerPage+', \'\', \'\', $(this).closest(\'form\'), \'supplier_id\', \'supplier_name\');" value="Lookup" /></div></div><div class="formRow"><label class="labelControll">Priority</label><div class="inputDiv selectMilestone padTB0"><label class="checkinput"><input type="checkbox" name="priority" value="1" />Urgent</label></div></div><div class="formRow "><label class="labelControll">*Expected Delivery Date</label><div class="inputDiv"><div class="datePickInput width150"><input type="text" value="" placeholder="DD/MM/YYYY" class="inputTxt" name="exp_delivery_date"  readonly="readonly"><span class="datePickCal"></span></div></div></div><div class="formRow"><label class="labelControll">Project Description</label><div class="inputDiv"><div class="writeCommentBlock marB10"><textarea placeholder="Write a comment..." name="description"></textarea><div class="commentBottom"><div class="attachfile">Attach Files<input type="file" name="multipleattachments[]" multiple="multiple" onchange="uploadImages($(this).closest(\'form\'), $(this).siblings(\'input[name=multipleimagesHidden]\'), \'milestone_attachments\');" /><input type="hidden" name="multipleimagesHidden" value=\'\' /></div></div></div><div class="uploadedImages"></div></div></div><div class="formRow"><label class="labelControll">Dropbox Link</label><div class="inputDiv"><input type="text" class="inputTxt width60p" name="dropbox_link" value=""></div></div><div class="formRow"><label class="labelControll">Job Type</label><div class="inputDiv selectMilestone padTB0"><label class="checkinput"><input type="radio" name="job_type" value="1" />CAD Only</label><label class="checkinput"><input type="radio" name="job_type" value="2" />CAD & Render</label><label class="checkinput"><input type="radio" name="job_type" value="3" />Render Only</label></div></div><div class="formRow"><label class="labelControll">Email Requirements to Supplier</label><div class="inputDiv"><input type="button" class="cmnBtn lightBoxClick" data-popup="composeEmail" data-btn-type="noDisable" onclick="composeMilestoneEmail('+milestone_id+', '+milestone_type+', 1);" id="'+milestone_id+'_1" value="Email" /><div class="emailTimestamp"></div></div></div><div class="formRow"><label class="labelControll">&nbsp;</label><div class="inputDiv"><button class="cmnBtn btnEmpty" type="button" onclick="saveCADRequirements($(this).closest(\'form\'), 1, $(this));">Complete Task</button>&nbsp; </div></div></div></form><h3 class="subheading">2. Upload Approved Files <span class="hlpInfo"><em>?</em> <span>lipsum is a dummy content</span></span></h3><form name="frm_cad_files" action="/uploadmultiplefile" enctype="multipart/form-data"><input type="hidden" name="milestone_id" value="'+milestone_id+'"/><input type="hidden" name="milestone_type_id" value="'+milestone_type+'"/><input type="hidden" name="job_id" value="'+job_id+'"/><input type="hidden" name="steps_completed" value="2"/><div class="commonForm rowSpace padB0"><div class="formRow"><label class="labelControll">Upload Images*</label><div class="inputDiv"><label class="uploadImg"><input type="file" name="multipleimages[]" multiple="multiple" onchange="uploadImages($(this).closest(\'form\'), $(this).siblings(\'input[name=multipleimagesHidden]\'), \'milestone_attachments\');" /><input type="hidden" name="multipleimagesHidden" value=\'\' />Upload Image</label><div class="uploadedImages"></div></div></div><div class="formRow "><label class="labelControll">*Date delivered</label><div class="inputDiv"><div class="datePickInput width150"><input type="text" value="" placeholder="DD/MM/YYYY" class="datepickerInput" name="stp2_delivery_date" readonly="readonly" /><span class="datePickCal"></span></div></div></div><div class="formRow"><label class="labelControll">&nbsp;</label><div class="inputDiv"><button class="cmnBtn btnEmpty" type="button" onclick="saveCADRequirements($(this).closest(\'form\'), 2, $(this));">Complete Task</button>&nbsp; </div></div></div></form><h3 class="subheading">3. Set Client Review <span class="hlpInfo"><em>?</em> <span>lipsum is a dummy content</span></span></h3><form name="frm_cad_review" action="/uploadmultiplefile" enctype="multipart/form-data"><input type="hidden" name="milestone_id" value="'+milestone_id+'"/><input type="hidden" name="milestone_type_id" value="'+milestone_type+'"/><input type="hidden" name="job_id" value="'+job_id+'"/><input type="hidden" name="steps_completed" value="3" /><div class="commonForm rowSpace padB0"><div class="formRow"><label class="labelControll">Email Client approved images</label><div class="inputDiv"><input type="button" class="cmnBtn lightBoxClick" data-popup="composeEmail" data-btn-type="noDisable" onclick="composeMilestoneEmail('+milestone_id+', '+milestone_type+', 3);" id="'+milestone_id+'_3" value="Email" /><div class="emailTimestamp"></div></div></div><div class="formRow"><label class="labelControll">Upload Images*</label><div class="inputDiv"><label class="uploadImg"><input type="file" name="multipleimages[]" multiple="multiple" onchange="uploadImages($(this).closest(\'form\'), $(this).siblings(\'input[name=multipleimagesHidden]\'), \'milestone_attachments\');" /><input type="hidden" name="multipleimagesHidden" value=\'\' />Upload Image</label><div class="uploadedImages"></div></div></div><div class="formRow"><label class="labelControll">&nbsp;</label><div class="inputDiv"><button class="cmnBtn btnEmpty" type="button" onclick="saveCADRequirements($(this).closest(\'form\'), 3, $(this));">Complete Task</button>&nbsp; </div></div><div class="floatL width100p marB10"> <input type="button" class="cmnBtn" onclick=\'addMilestone($(this).closest("li"), '+milestone_id+', '+job_id+', $(this).siblings("div").children("select").find(":selected").val(), '+recordsPerPage+', '+JSON.stringify(optionalData)+');\' value="+ Add MIlestone" data-btn-type="noDisable" /><div class="width150 marL10"  style="display:inline-block"><select class="dropdown" name="addms" onclick="addMilestone($(this).closest(\'li\'), '+milestone_id+', '+job_id+', $(this).siblings(\'div\').children(\'select\').find(\':selected\').val(), '+recordsPerPage+');"><option value="0">Select</option><option value="1">CAD</option><option value="2">Prototype</option><option value="3">Cast Manufacturing</option><option value="4">Workshop</option></select></div></div></div></form></div></li>';
					}else if(milestone_type == 2){
						
						var metalTypes = optionalData.metalTypes;
						var optionsElem = '';
						for(i=0;i<metalTypes.length;i++){
							optionsElem = optionsElem + '<option value="'+metalTypes[i].id+'">'+metalTypes[i].type+'</option>';
						}
						
						var html = '<li class="productionStep editStep"><div class="title active">Prototype<button type="button" class="cmnBtn deleteBtn" onclick="deleteMilestone('+milestone_id+', '+milestone_type+', $(this));">Delete</button></div><div class="information" style="display:block;"><h3 class="subheading">Step 1. Prototype Requirements <span class="hlpInfo"><em>?</em> <span>lipsum is a dummy content</span></span></h3><form name="frm_prototype_requirement" enctype="multipart/form-data" action="/uploadmultiplefile"><input type="hidden" name="milestone_id" value="'+milestone_id+'"/><input type="hidden" name="milestone_type_id" value="'+milestone_type+'"/><input type="hidden" name="job_id" value="'+job_id+'"/><input type="hidden" name="steps_completed" value="1"/><input type="hidden" name="supplier_id" id="supplier_id" value="" /><div class="commonForm rowSpace padB0"><div class="innerTitle">Create New Job Packet</div><div class="formRow"><label class="labelControll">No. Of Parts</label><div class="inputDiv"><select class="dropdown width100" name="no_of_parts"><option value="0">Select</option><option value="1">1</option><option value="2">2</option><option value="3">3</option><option value="4">4</option><option value="5">5</option></select></div></div><div class="formRow"><label class="labelControll">Quantity</label><div class="inputDiv "><select class="dropdown width100" name="quantity"><option value="0">Select</option><option value="1">1</option><option value="2">2</option><option value="3">3</option><option value="4">4</option><option value="5">5</option></select></div></div><div class="formRow"><label class="labelControll">Metal Type</label><div class="inputDiv"><div class="fl width150"><select class="dropdown" name="metal_type_opt">'+optionsElem+'</select></div><input type="button" onclick="addMetalType($(this).siblings(\'.addedInform\'), $(this).closest(\'.inputDiv\').find(\'select\'));" class="cmnBtn blueBtn" value="Add Item" /><div class="addedInform"></div><div class="cb"></div><a href="javascript:;" class="hideAdvance">Hide Advanced</a></div></div><div class="formRow"><label class="labelControll">Priority</label><div class="inputDiv selectMilestone padTB0"><label class="checkinput"><input type="checkbox" name="priority" value="1" />Urgent</label></div></div><div class="formRow "><label class="labelControll">Requirements</label><div class="inputDiv"><div class="writeCommentBlock marB10"><textarea placeholder="Write a comment..." name="requirement_desc"></textarea><div class="commentBottom"><div class="attachfile">Attach Files<input type="file" name="multipleattachments[]" multiple="multiple" onchange="uploadImages($(this).closest(\'form\'), $(this).siblings(\'input[name=multipleimagesHidden]\'), \'milestone_attachments\');" /><input type="hidden" name="multipleimagesHidden" value="" /></div></div></div><div class="uploadedImages"></div></div></div><div class="formRow"><label class="labelControll">Supplier Name*</label><div class="inputDiv"><input type="text" class="inputTxt width60p" name="supplier_name" value="" /><input type="button" class="cmnBtn blueBtn lightBoxClick" data-popup="supplierLookup" onclick="supplierLookupForMilestone(\'supplierLookupGrid\', '+recordsPerPage+', \'\', \'\', $(this).closest(\'form\'), \'supplier_id\', \'supplier_name\');" value="Lookup" /></div></div><div class="formRow"><label class="labelControll">Dropbox Link</label><div class="inputDiv"><input type="text" class="inputTxt" name="dropbox_link" value="" /></div></div><div class="formRow"><label class="labelControll">*Expected Delivery Date</label><div class="inputDiv"><div class="datePickInput width150"><input type="text" name="exp_delivery_date" readonly="readonly" class="datepickerInput" placeholder="DD/MM/YYYY" value="" /><span class="datePickCal"></span></div></div></div><div class="formRow"><label class="labelControll">&nbsp;</label><div class="inputDiv"><button type="button" class="cmnBtn btnEmpty" onclick="savePrototypeStep1($(this), $(this).closest(\'form\'));">Complete Step</button>&nbsp;</div></div><div class="formRow"><label class="labelControll">Email Requirements to Supplier</label><div class="inputDiv"><input type="button" class="cmnBtn lightBoxClick" data-popup="composeEmail" data-btn-type="noDisable" onclick="composeMilestoneEmail('+milestone_id+', '+milestone_type+', 1);" id="'+milestone_id+'_1" value="Email" /><div class="emailTimestamp"></div></div></div></div></form><h3 class="subheading">Step 2. Client Review <span class="hlpInfo"><em>?</em> <span>lipsum is a dummy content</span></span></h3><form name="frm_prototype_review"><input type="hidden" name="milestone_id" value="'+milestone_id+'"/><input type="hidden" name="milestone_type_id" value="'+milestone_type+'"/><input type="hidden" name="job_id" value="'+job_id+'"/><input type="hidden" name="steps_completed" value="2"/><div class="commonForm rowSpace padB0"><div class="formRow"><label class="labelControll">Date Delivered</label><div class="inputDiv"><div class="datePickInput width150"><input type="text" name="date_delivered" readonly="readonly" class="datepickerInput" placeholder="DD/MM/YYYY" value="" /><span class="datePickCal"></span></div></div></div><div class="formRow"><label class="labelControll">&nbsp;</label><div class="inputDiv"><button class="cmnBtn btnEmpty" type="button" onclick="savePrototypeStep2($(this), $(this).closest(\'form\'));">Complete Task</button>&nbsp;</div></div><div class="floatL width100p marB10"> <input type="button" class="cmnBtn" onclick=\'addMilestone($(this).closest("li"), '+milestone_id+', '+job_id+', $(this).siblings("div").children("select").find(":selected").val(), '+recordsPerPage+', '+JSON.stringify(optionalData)+');\' value="+ Add MIlestone" data-btn-type="noDisable" /><div class="width150 marL10" style="display:inline-block"><select class="dropdown" name="addms"><option value="0">Select</option><option value="1">CAD</option><option value="2">Prototype</option><option value="3">Cast Manufacturing</option><option value="4">Workshop</option></select></div></div></div></form></div></li>';
					}else if(milestone_type == 3){
						
						var metalTypes = optionalData.metalTypes;
						var optionsElem = '';
						for(i=0;i<metalTypes.length;i++){
							optionsElem = optionsElem + '<option value="'+metalTypes[i].id+'">'+metalTypes[i].type+'</option>';
						}
						
						var html = '<li class="productionStep editStep"><div class="title active">Cast Manufacturing<button type="button" class="cmnBtn deleteBtn" onclick="deleteMilestone('+milestone_id+', '+milestone_type+', $(this));">Delete</button></div><div class="information" style="display:block;"><h3 class="subheading">Step 1. Create a Manufacturing Job <span class="hlpInfo"><em>?</em> <span>lipsum is a dummy content</span></span></h3><form name="frm_cast_job" enctype="multipart/form-data" action="/uploadmultiplefile"><input type="hidden" name="milestone_id" value="'+milestone_id+'"/><input type="hidden" name="milestone_type_id" value="'+milestone_type+'"/><input type="hidden" name="job_id" value="'+job_id+'"/><input type="hidden" name="steps_completed" value="1"/><input type="hidden" name="supplier_id" id="supplier_id" value="" /><div class="commonForm rowSpace padB0"><div class="innerTitle">Create New Job Packet</div><div class="formRow"><label class="labelControll">No. Of Parts</label><div class="inputDiv"><select class="dropdown width100" name="no_of_parts"><option value="0">Select</option><option value="1">1</option><option value="2">2</option><option value="3">3</option><option value="4">4</option><option value="5">5</option></select></div></div><div class="formRow"><label class="labelControll">Quantity</label><div class="inputDiv "><select class="dropdown width100" name="quantity"><option value="0">Select</option><option value="1">1</option><option value="2">2</option><option value="3">3</option><option value="4">4</option><option value="5">5</option></select></div></div><div class="formRow"><label class="labelControll">Metal Type</label><div class="inputDiv"><div class="fl width150"><select class="dropdown" name="metal_type_opt">'+optionsElem+'</select></div><input type="button" onclick="addMetalType($(this).siblings(\'.addedInform\'), $(this).closest(\'.inputDiv\').find(\'select\'));" class="cmnBtn blueBtn" value="Add Item" /><div class="addedInform"></div><div class="cb"></div><a href="javascript:;" class="hideAdvance">Hide Advanced</a></div></div><div class="formRow"><label class="labelControll">Priority</label><div class="inputDiv selectMilestone padTB0"><label class="checkinput"><input type="checkbox" name="priority" value="1" />Urgent</label></div></div><div class="formRow "><label class="labelControll">Requirements</label><div class="inputDiv"><div class="writeCommentBlock marB10"><textarea placeholder="Write a comment..." name="requirement_desc"></textarea><div class="commentBottom"><div class="attachfile">Attach Files<input type="file" name="multipleattachments[]" multiple="multiple" onchange="uploadImages($(this).closest(\'form\'), $(this).siblings(\'input[name=multipleimagesHidden]\'), \'milestone_attachments\');" /><input type="hidden" name="multipleimagesHidden" value="" /></div></div></div><div class="uploadedImages"></div></div></div><div class="formRow"><label class="labelControll">Supplier Name*</label><div class="inputDiv"><input type="text" class="inputTxt width60p" name="supplier_name" value="" /><input type="button" class="cmnBtn blueBtn lightBoxClick" data-popup="supplierLookup" onclick="supplierLookupForMilestone(\'supplierLookupGrid\', '+recordsPerPage+', \'\', \'\', $(this).closest(\'form\'), \'supplier_id\', \'supplier_name\');" value="Lookup" /></div></div><div class="formRow"><label class="labelControll">CAD Dropbox Link</label><div class="inputDiv"><input type="text" class="inputTxt" name="dropbox_link" value="" /></div></div><div class="formRow"><label class="labelControll">*Expected Delivery Date</label><div class="inputDiv"><div class="datePickInput width150"><input type="text" name="exp_delivery_date" readonly="readonly" class="datepickerInput" placeholder="DD/MM/YYYY" value="" /><span class="datePickCal"></span></div></div></div><div class="formRow"><label class="labelControll">&nbsp;</label><div class="inputDiv"><button type="button" class="cmnBtn btnEmpty" onclick="saveCastStep1($(this), $(this).closest(\'form\'));">Complete Step</button>&nbsp;</div></div><div class="formRow"><label class="labelControll">Date Job Delivered</label><div class="inputDiv"><div class="datePickInput width150"><input type="text" name="date_delivered" readonly="readonly" class="datepickerInput" placeholder="DD/MM/YYYY" value="" /><span class="datePickCal"></span></div></div></div><div class="formRow"><label class="labelControll">Email Requirements to Supplier</label><div class="inputDiv"><input type="button" class="cmnBtn lightBoxClick" data-popup="composeEmail" data-btn-type="noDisable" onclick="composeMilestoneEmail('+milestone_id+', '+milestone_type+', 1);" id="'+milestone_id+'_1" value="Email" /><div class="emailTimestamp"></div></div></div></div></form><h3 class="subheading">Step 2. DE Review <span class="hlpInfo"><em>?</em> <span>lipsum is a dummy content</span></span></h3><form name="frm_cast_review"><input type="hidden" name="milestone_id" value="'+milestone_id+'"/><input type="hidden" name="milestone_type_id" value="'+milestone_type+'"/><input type="hidden" name="job_id" value="'+job_id+'"/><input type="hidden" name="steps_completed" value="2"/><div class="commonForm rowSpace padB0"><div class="formRow"><label class="labelControll">&nbsp;</label><div class="inputDiv"><button class="cmnBtn btnEmpty" type="button" onclick="saveCastStep2($(this), $(this).closest(\'form\'));">Complete Task</button>&nbsp;</div></div><div class="floatL width100p marB10"> <input type="button" class="cmnBtn" onclick=\'addMilestone($(this).closest("li"), '+milestone_id+', '+job_id+', $(this).siblings("div").children("select").find(":selected").val(), '+recordsPerPage+', '+JSON.stringify(optionalData)+');\' value="+ Add MIlestone" data-btn-type="noDisable" /><div class="width150 marL10" style="display:inline-block"><select class="dropdown" name="addms"><option value="0">Select</option><option value="1">CAD</option><option value="2">Prototype</option><option value="3">Cast Manufacturing</option><option value="4">Workshop</option></select></div></div></div></form></div></li>';
						
					}else if(milestone_type == 4){
						
						var reviewedByUser = optionalData.reviewedByUser;
						var optionsElem = '';
						for(i=0;i<reviewedByUser.length;i++){
							optionsElem = optionsElem + '<option value="'+reviewedByUser[i].id+'">'+reviewedByUser[i].name+'</option>';
						}
						
						var html = '<li class="productionStep editStep"><div class="title active">Workshop<button type="button" class="cmnBtn deleteBtn" onclick="deleteMilestone('+milestone_id+', '+milestone_type+', $(this));">Delete</button></div><div class="information workShopeCounter" style="display:block;"><h3 class="subheading"><em class="countSuple"></em> Create Production Line  <span class="hlpInfo"><em>?</em> <span>lipsum is a dummy content</span></span> <a data-popup="printjob" href="javascript:;" onclick="printJob('+milestone_id+');" class="popupLink cmnBtn fr marT0 lightBoxClick">Print Job</a></h3><form name="frm_workshop_production" enctype="multipart/form-data" action="/uploadmultiplefile"><input type="hidden" name="milestone_id" value="'+milestone_id+'"/><input type="hidden" name="milestone_type_id" value="'+milestone_type+'"/><input type="hidden" name="job_id" value="'+job_id+'"/><input type="hidden" name="steps_completed" value="1"/><div class="commonForm rowSpace padB0"><div class="workShopUploadImg"><div class="uploadinn"><label class="uploadImg"><input type="file" name="multipleattachments[]" onchange="uploadImages($(this).closest(\'form\'), $(this).siblings(\'input[name=multipleimagesHidden]\'), \'milestone_attachments\');" /><input type="hidden" name="multipleimagesHidden" value=\'\' />Upload Image</label><div class="uploadedImages"></div></div></div><div class="formRow"><label class="labelControll"></label><div class="inputDiv"><button type="button" class="cmnBtn btnEmpty" onclick="saveWorkshopStep1($(this), $(this).closest(\'form\'));">Complete Step</button>&nbsp; </div></div><div class="formRow"><label class="labelControll">Add Supplier</label><div class="inputDiv"><input type="button" class="cmnBtn blueBtn lightBoxClick" data-popup="supplierLookup" data-btn-type="noDisable" onclick="supplierLookupForWorkshop(\'supplierLookupGrid\', '+recordsPerPage+', \'\', '+job_id+', '+milestone_id+', $(this));" value="Lookup" /></div></div></div></form><div class="suppliersList"></div><h3 class="subheading"><em class="countSuple"></em>DE Quality Control <span class="hlpInfo"><em>?</em> <span>lipsum is a dummy content</span></span></h3><form name="frm_workshop_qc"><input type="hidden" name="milestone_id" value="'+milestone_id+'"/><input type="hidden" name="milestone_type_id" value="'+milestone_type+'"/><input type="hidden" name="job_id" value="'+job_id+'"/><input type="hidden" name="steps_completed" value="2"/><div class="commonForm rowSpace padB0"><div class="formRow"><label class="labelControll">Reviewed By</label><div class="inputDiv"><div class="width150"><select class="dropdown" name="qa_reviewed_by">'+optionsElem+'</select></div></div></div><div class="formRow"><label class="labelControll">&nbsp;</label><div class="inputDiv"><button type="button" class="cmnBtn btnEmpty" onclick="saveWorkshopQualityControl($(this), $(this).closest(\'form\'));">Complete Step</button>&nbsp; </div></div></div></form><h3 class="subheading"><em class="countSuple"></em> Client Review <span class="hlpInfo"><em>?</em> <span>lipsum is a dummy content</span></span></h3><form name="frm_workshop_review"><input type="hidden" name="milestone_id" value="'+milestone_id+'"/><input type="hidden" name="milestone_type_id" value="'+milestone_type+'"/><input type="hidden" name="job_id" value="'+job_id+'"/><input type="hidden" name="steps_completed" value="3"/><div class="commonForm rowSpace padB0"><div class="formRow"><label class="labelControll">&nbsp;</label><div class="inputDiv"><button type="button" class="cmnBtn btnEmpty" onclick="saveWorkshopFinalStepControl($(this), $(this).closest(\'form\'));">Complete Step</button>&nbsp; </div></div><div class="formRow"><label class="labelControll">&nbsp;</label><div class="inputDiv"> <input type="button" class="cmnBtn" onclick=\'addMilestone($(this).closest("li"), '+milestone_id+', '+job_id+', $(this).siblings("div").children("select").find(":selected").val(), '+recordsPerPage+', '+JSON.stringify(optionalData)+');\' value="+ Add MIlestone" data-btn-type="noDisable" /><div class="width150 marL10" style="display:inline-block"><select class="dropdown" name="addms"><option value="0">Select</option><option value="1">CAD</option><option value="2">Prototype</option><option value="3">Cast Manufacturing</option><option value="4">Workshop</option></select></div></div></div></div></form></div></li>';
					}
					$(html).insertAfter(current_conainer);
					
					$('.datePickInput input[type=text]').datepicker({
						dateFormat: 'dd/mm/yy',
						minDate: new Date(),
						changeYear: true,
						beforeShow: function() {
							$(this).after($(this).datepicker("widget"));
						}
					});
					
					jQuery(".dropdown").dropkick({
						mobile: true
					});
				}else{					
					$( '<p class="errorText">Milestone can not be added</p>' ).insertAfter( current_conainer.find('select[name=addms]').closest('div') );
				}
			}
		});
	}
}

/**
 * Upload files
 * form = form element
 * elem = upload button
 * path = folder name to upload files
 */
function uploadImages(form, elem, path){
	globalElem = elem;
	globalForm = form;
	
	globalForm.find('button').attr('disabled', 'disabled');
	
	form.ajaxForm({
		success: function(response){
			
			var existingJsonValue = globalForm.find(globalElem).val();
			
			if(existingJsonValue == ''){
				globalForm.find(globalElem).val(response);
				//Parsing response JSON Data to Javascript Array
				response = JSON.parse(response);
			} else {
				//Parsing existingJsonValue JSON Data to Javascript Array
				existingJsonValue = JSON.parse(existingJsonValue);
				//Parsing response JSON Data to Javascript Array
				response = JSON.parse(response);
				//Merging Two Javascript Array's
				existingJsonValue = existingJsonValue.concat(response);
				//Converting Javascript arrat to JSON Object
				existingJsonValue = JSON.stringify(existingJsonValue);
				globalForm.find(globalElem).val(existingJsonValue);
			}
			
			$.each(response, function( index, value ) {
			  var imgHtml = '<div class="upImg"><img src="/'+path+'/'+value+'" height="98px" width="98px"/><a class="closeImg" onclick="deleteFile(\''+path+'/'+value+'\', \$(this).closest(\'form\').find(\'input[name=multipleimagesHidden]\'), \$(this).closest(\'.upImg\'));" style="cursor:pointer">X</a></div>';
			  $('div.uploadedImages', globalForm).append(imgHtml);
			});			
			
			globalForm.find('button').removeAttr('disabled');
		}
	}).submit();
}

/**
 * Delete milestone
 * milestone_id, milestone_type_id, elem = element which was clicked to delete
 */
function deleteMilestone(milestone_id, milestone_type_id, elem){
	globalElem = elem;
	if(confirm('Are you sure to delete the milestone ?')){
		var data = {milestone_id: milestone_id, milestone_type_id: milestone_type_id};
		$.ajax({
			url: '/deletemilestone',
			type: 'POST',
			async: false,
			data: data,
			success: function(response){
				if(response){
					var nextLi = globalElem.closest('li').next('li.productionStep');
					var previousLi = globalElem.closest('li').prev('li.productionStep');
					
					if(nextLi.length > 0){
						nextLi.addClass('editStep');				
						nextLi.find('div.title').addClass('active');
						nextLi.find('div.information').show();
					}
					
					if(previousLi.length > 0 && nextLi.find('.blueBtn2').length == 0)
						previousLi.find('div.addms').show();
						
					globalElem.closest('li').remove();
				}else{
					alert('Milestone cannot be deleted as Job has been paused');	
				}
			}
		});
	}
}

/**
 * Opens invoice lookup popup
 */
function openInvoiceLookup(){
	$('.errorText').remove();
	if($('#opp_id').val() == ''){
		$( '<p class="errorText">Please select an opportunity</p>' ).insertAfter( $('#orderOpportunityLookup') );
	}else{
		$('#invoicelookupAnchor').click();
		getInvoiceLookup($('#opp_id').val());
	}
}

/**
 * Make ajax call to pause a job
 * job_id
 * status
 */
function changeJobStatus(job_id, status){
	$.ajax({
		url: '/changejobstatus',
		type: 'POST',
		async: false,
		data: {job_id: job_id, status: status},
		success: function(response){
			if(response == 1 && status == 2){ // Pause job
				$('.pausezone').hide();
				$('.restartzone').show();
			}else if(response == 1 && status == 1){ // Restart job
				$('.pausezone').show();
				$('.restartzone').hide();
			}
			
			/*if(response == 1 && status == 1){
				$('ul.manageList').addClass('startprduction');
				$('li.productionStep').each(response, function(index){
					if($(this).find('.blueBtn2').length > 0){
						$(this).addClass('editStep');
					}
					$(this).find('div.information').hide();
				});
			}*/
		}
	});
}

function disableElements(form, elem){
	elem.attr('disabled', 'disabled');
	form.find('input[name=multipleattachments]').attr('disabled', 'disabled');
	$('.closeImg').hide();
	
	disableMilestone(form);
}

/**
 * Upload files
 * form = form element
 * elem = upload button
 * path = folder name to upload files
 */
function uploadFiles(form, elem, path){
	globalElem = elem;
	globalForm = form;
	
	globalForm.find('button').attr('disabled', 'disabled');
	
	form.ajaxForm({
		success: function(response){
			var existingJsonValue = globalForm.find(globalElem).val();
			if(existingJsonValue == ''){
				globalForm.find(globalElem).val(response);
				//Parsing response JSON Data to Javascript Array
				response = JSON.parse(response);
			} else {
				//Parsing existingJsonValue JSON Data to Javascript Array
				existingJsonValue = JSON.parse(existingJsonValue);
				//Parsing response JSON Data to Javascript Array
				response = JSON.parse(response);
				//Merging Two Javascript Array's
				existingJsonValue = existingJsonValue.concat(response);
				//Converting Javascript arrat to JSON Object
				existingJsonValue = JSON.stringify(existingJsonValue);
				globalForm.find(globalElem).val(existingJsonValue);
			}
			
			$.each(response, function( index, value ) {
			  var imgHtml = '<div class="upImg" title="'+value+'"><span>'+value+'</span><a class="closeImg" onclick="deleteFile(\''+path+'/'+value+'\', \$(this).closest(\'form\').find(\'input[name=email_attachment_list]\'), \$(this).closest(\'.upImg\'));" style="cursor:pointer">X</a></div>';
			  $('div.uploadedImages', globalForm).append(imgHtml);
			});
			
			globalForm.find('button').removeAttr('disabled');
		}
	}).submit();
}

/**
 * Validate email form
 */
function validateComposeMilestoneEmail(form){
	var errors = 0;
	$('.errorText').remove();
	
	if(form.find("input[name=subject]").val() == ''){
		$( '<p class="errorText nowrape">Please enter a subject</p>' ).insertAfter( form.find("input[name=subject]") );
		errors++;
	}
		
	if(form.find("textarea[name=message]").val() == ''){
		$( '<p class="errorText nowrape">Please enter email body</p>' ).insertAfter( form.find(".writeCommentBlock") );
		errors++;
	}
	
	return errors;	
}

/**
 * Email milestone
 * milestone_id, milestone_type_id, step which sall be emailed
 */
function emailMilestone(form, elem){
	var emailBtn = $('#'+form.find('input[name=milestone_id]').val()+'_'+form.find('input[name=step]').val());

	var errors = validateComposeMilestoneEmail(form);
	if(errors == 0){
		$.ajax({
			url: '/emailmilestone',
			type: 'POST',
			async: true,
			data: form.serialize(),
			beforeSend: function(){
				form.closest('.lightBoxContent').append('<div class="ajaxLoader" style="position:absolute;left0;top:0;right:0;bottom:0"><div class="pageLoader"></div></div>')
			},
			success: function(response){
				if(response){
					emailBtn.siblings('div.emailTimestamp').html('<span class="emailSentIcon"></span> <span>'+response+'</span>');
				}else{
					alert('Please complete the milestone first');	
				}
				form.closest('.lightBoxContent').find('.ajaxLoader').remove();
				$('.lightBoxTitle .closePopup').click();
			}
		});
	}
	//elem.removeAttr('disabled');
}

/**
 * Email milestone popup
 * milestone_id, milestone_type_id, step
 */
function composeMilestoneEmail(milestone_id, milestone_type_id, step){
	var url = '/composemilestoneemail';
	$.ajax({
		url: url,
		data: {milestone_id: milestone_id, milestone_type_id: milestone_type_id, step: step},
		type: 'post',
		beforeSend: function(){
			$('#composeemailpopup').html('<div class="ajaxLoader"><div class="pageLoader"></div></div>');
			lightboxmid();
		},
		success: function(response){
			if(response == 0){
				$('#composeemailpopup').html('<p class="errorText">Please complete this step first</p>');
			}else{
				$('#composeemailpopup').html(response);
			}
			lightboxmid();
		}
	});
}

/**
 * Grid for milestone emails
 * recordsPerPage, supplier_id, keyword
 */
 
function getMilestoneEmails(recordsPerPage, supplier_id, keyword, gridId){
	
	if(keyword && !supplier_id){
		var url = '/ajaxgetmilestoneemail?keyword='+keyword;
	} else if(!keyword && supplier_id){
		var url = '/ajaxgetmilestoneemail?customer_id='+supplier_id;
	}else if(keyword && supplier_id){
		var url = '/ajaxgetmilestoneemail?keyword='+keyword+'&customer_id='+supplier_id;
	}else if(!keyword && !supplier_id){
		var url = '/ajaxgetmilestoneemail';
	}
	/*InitGrid();
	function InitGrid() {
		$('#'+gridId).remove();
        $('#bindAfterThis').after('<div class="formTable manageMembers" id="'+gridId+'"></div>');
	}*/
	// prepare the data
	var source =
	{
		datatype: "json",
		datafields: [
			{ name: 'id' },
			{ name: 'milestone_id' },
			{ name: 'milestone_type_id' },
			{ name: 'step' },
			{ name: 'supplier_id' },
			{ name: 'subject' },
			{ name: 'message' },
			{ name: 'attachments' },
			{ name: 'created_date' },
			{ name: 'created_time' },
			{ name: 'supplier_name' },
			{ name: 'supplier_email' },
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
			$("#"+gridId).jqxGrid('updatebounddata', 'filter');
		}
	};
	
	var pageable = true, sortable = true;
	var dataAdapter = new $.jqx.dataAdapter(source, {
		downloadComplete: function (data, status, xhr) {},
		loadComplete: function (data) { 
			if($("#"+gridId).find('.jqx-grid-empty-cell').length>0 ){					
					if($("#"+gridId).find('.jqx-grid-empty-cell').parent('div').height()<30){
						$('.jqx-grid-empty-cell').parent('div').addClass('norecordsFoundRow');
						$('.jqx-grid-empty-cell').closest("#"+gridId).addClass('noInfoFound');
					}
					if(keyword){
						$("#"+gridId).find('.jqx-grid-empty-cell >span').text("No matches were found");
						//$('#searchResultsHint').text("No matches were found");
					} else {
						$("#"+gridId).find('.jqx-grid-empty-cell >span').text("No records found");
					}
			}else{
				if($("#"+gridId).hasClass('noInfoFound')){
					$("#"+gridId).removeClass('noInfoFound');
				}
			}
		},
		loadError: function (xhr, status, error) { }
	});
		
	$("#"+gridId).jqxGrid(
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
			{ text: 'Date Created', type: 'string', datafield: 'created_date', width:'15%'},
			{ text: 'Conversation', type: 'string', datafield: 'subject', width:'70%'},
			{ text: 'Date/Time', type: 'string', datafield: 'created_time', width:'15%'},
		]
	});
	
	$('#'+gridId).bind('cellclick', function(event)  {
		var current_index = event.args.rowindex;
		var current_column = event.args.column.datafield;
		var datarow = $('#'+gridId).jqxGrid('getrowdata', current_index);
		var url = '/viewmilestoneemail/'+datarow.id;
		if(current_column != null){
			$(location).attr('href', url);
		}
		$('#gridTypeId').val(datarow.id);
		$('#gridType').val('supplier');
	});
	
	$("#"+gridId).bind("sort", function (event) {
		$("#"+gridId).jqxGrid('updatebounddata', 'filter');
	});
	
	$("#"+gridId).bind("pagesizechanged", function (event) {
		var args = event.args;
		var pagenumber = args.pagenum;
		var pagesize = args.pagesize;
		$('#pageSizeGrabing').val(pagesize);
	});
}

/**
 * Make ajax call to open order form
 * job_id, container = where to place the html
 */
function updateJobForm(job_id, container){
	
	$.ajax({
		url: '/updatejobform',
		type: 'POST',
		data: {job_id: job_id},
		beforeSend: function(){
			container.html('<div class="ajaxLoader"><div class="pageLoader"></div></div>');
			lightboxmid();
		},
		success: function(response){
			container.html(response);
			lightboxmid();
		}
	});
	//elem.removeAttr('disabled');	
}

function getJobPacketsFromGridView(recordsPerPage, columnList, keyword){
	$('#searchResultsHint').text('');
	if(keyword){
		var url = '/ajaxjoblist?keyword='+keyword;
	} else {
		var url = '/ajaxjoblist';
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
			{ name: 'milestones_current_status' },
			{ name: 'milestones_supplier' },
			{ name: 'milestones_activity' },
			{ name: 'owner_name' },
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
			$("#jqxJobpackets").jqxGrid('updatebounddata', 'filter');
		}
	};
	
	var pageable = true, sortable = true;
	var dataAdapter = new $.jqx.dataAdapter(source, {
		downloadComplete: function (data, status, xhr) { },
		loadComplete: function (data) { 
			if($("#jqxJobpackets").find('.jqx-grid-empty-cell').length>0){
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
	
	var colList = columnList.split(',');
	var gridColumnList = new Array();
	if(colList.length>5){
		for(i = 0; i < colList.length; i++){
			if(colList[i] == 'consign_button'){
				gridColumnList.push({ text: 'Consignment', type: 'string', cellsrenderer: consign, width:'10%' });
			}
			if(colList[i] == 'inventory_tracking_status'){
				gridColumnList.push({ text: 'Tracking', type: 'string', datafield: 'inventory_tracking_status', width:'10%' });
			}
			if(colList[i] == 'inventory_tracking_reason'){
				gridColumnList.push({ text: 'Tracking Reason', type: 'string', datafield: 'inventory_tracking_reason', width:'10%' });
			}
			if(colList[i] == 'tracking_id'){
				gridColumnList.push({ text: 'Tracking ID', type: 'string', datafield: 'tracking_id', width:'10%' });
			}
			if(colList[i] == 'job_id'){
				gridColumnList.push({ text: 'Job Number', type: 'string', datafield: 'job_id', width:'10%' });
			}
			if(colList[i] == 'customer_name'){
				gridColumnList.push({ text: 'Customer <br /> Name', type: 'string', datafield: 'customer_name', width:'10%' });
			}
			if(colList[i] == 'milestones_supplier'){
				gridColumnList.push({ text: 'Supplier', type: 'string', datafield: 'milestones_supplier', width:'10%' });
			}
			if(colList[i] == 'due_days'){
				gridColumnList.push({ text: 'Due In (Days)', type: 'string', datafield: 'due_days', width:'10%' });
			}
			if(colList[i] == 'milestone_progress'){
				gridColumnList.push({ text: 'Milestone <br /> Progress', type: 'string', datafield: 'milestone_progress', width:'10%' });
			}
			if(colList[i] == 'milestones_completed_str'){
				gridColumnList.push({ text: 'Milestone', type: 'string', datafield: 'milestones_completed_str',  width:'10%'});
			}
			if(colList[i] == 'milestones_current_status'){
				gridColumnList.push({ text: 'Status', type: 'string', datafield: 'milestones_current_status', width:'10%' });
			}
			if(colList[i] == 'exp_delivery_date'){
				gridColumnList.push({ text: 'Due Date', type: 'string', datafield: 'exp_delivery_date', width:'10%' });
			}
			if(colList[i] == 'milestones_str'){
				gridColumnList.push({ text: 'Milestones <br /> Required', type: 'string', datafield: 'milestones_str', width:'10%' });
			}
			if(colList[i] == 'milestones_activity'){
				gridColumnList.push({ text: 'Activity', type: 'string', datafield: 'milestones_activity', width:'10%' });
			}
		}
		$("#jqxJobpackets").jqxGrid({columns:gridColumnList});
	}else{
		var countData = colList.length;
		var colWidthData = 100/countData;
			
		for(i = 0; i < colList.length; i++){
			if(colList[i] == 'consign_button'){
				gridColumnList.push({ text: 'Consignment', type: 'string', cellsrenderer: consign, width:colWidthData+'%' });
			}
			if(colList[i] == 'inventory_tracking_status'){
				gridColumnList.push({ text: 'Tracking', type: 'string', datafield: 'inventory_tracking_status', width:colWidthData+'%' });
			}
			if(colList[i] == 'inventory_tracking_reason'){
				gridColumnList.push({ text: 'Tracking Reason', type: 'string', datafield: 'inventory_tracking_reason', width:colWidthData+'%' });
			}
			if(colList[i] == 'tracking_id'){
				gridColumnList.push({ text: 'Tracking ID', type: 'string', datafield: 'tracking_id', width:colWidthData+'%' });
			}
			if(colList[i] == 'job_id'){
				gridColumnList.push({ text: 'Job Number', type: 'string', datafield: 'job_id', width:colWidthData+'%' });
			}
			if(colList[i] == 'customer_name'){
				gridColumnList.push({ text: 'Customer <br /> Name', type: 'string', datafield: 'customer_name', width:colWidthData+'%' });
			}
			if(colList[i] == 'milestones_supplier'){
				gridColumnList.push({ text: 'Supplier', type: 'string', datafield: 'milestones_supplier', width:colWidthData+'%' });
			}
			if(colList[i] == 'due_days'){
				gridColumnList.push({ text: 'Due In (Days)', type: 'string', datafield: 'due_days', width:colWidthData+'%' });
			}
			if(colList[i] == 'milestone_progress'){
				gridColumnList.push({ text: 'Milestone <br /> Progress', type: 'string', datafield: 'milestone_progress', width:colWidthData+'%' });
			}
			if(colList[i] == 'milestones_completed_str'){
				gridColumnList.push({ text: 'Milestone', type: 'string', datafield: 'milestones_completed_str',  width:colWidthData+'%'});
			}
			if(colList[i] == 'milestones_current_status'){
				gridColumnList.push({ text: 'Status', type: 'string', datafield: 'milestones_current_status', width:colWidthData+'%' });
			}
			if(colList[i] == 'exp_delivery_date'){
				gridColumnList.push({ text: 'Due Date', type: 'string', datafield: 'exp_delivery_date', width:colWidthData+'%' });
			}
			if(colList[i] == 'milestones_str'){
				gridColumnList.push({ text: 'Milestones <br /> Required', type: 'string', datafield: 'milestones_str', width:colWidthData+'%' });
			}
			if(colList[i] == 'milestones_activity'){
				gridColumnList.push({ text: 'Activity', type: 'string', datafield: 'milestones_activity', width:colWidthData+'%' });
			}
		}
		$("#jqxJobpackets").jqxGrid({columns:gridColumnList});
	}		
		
	if(colList.length == gridColumnList.length){
		$("#jqxJobpackets").jqxGrid(
		{
			width: '100%',
			source: dataAdapter,
			sortable: true,
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
			columns:gridColumnList,
			pagerheight: 50,
			//headerheight: 60,
			virtualmode: true,
			//pagermode: 'simple',
			//pager: '#gridpager',
			//columnsresize: true,
			rendergridrows: function (params) {
				return params.data;
			}
		});
	
		//$("#jqxJobpackets").next('.pagerHTML').html($('#pagerjqxJobpackets'));
		
		$('#jqxJobpackets').bind('cellclick', function(event)  {
			var current_index = event.args.rowindex;
			var current_column = event.args.column.datafield;
			var datarow = $('#jqxJobpackets').jqxGrid('getrowdata', current_index);
			
			var url = '/jobdetails/'+datarow.job_id;
			
			if(current_column != null){
				$(location).attr('href', url);
			}
		});
	}
	
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
 * Print specific milestone
 */
function printJob(milestone_id){
	$.ajax({
		url: '/printjob',
		type: 'POST',
		data: {milestone_id: milestone_id},
		beforeSend: function(){
			$('#printjobhtml').html('<div class="ajaxLoader"><div class="pageLoader"></div></div>');
			lightboxmid();
		},
		success: function(response){
			if(response == 0)
				$('#printjobhtml').html('<p class="errorText">Please complete first step atleast</p>');
			else
				$('#printjobhtml').html(response);
			lightboxmid();
		}
	});	
}

/**
 *
 */
function disableMilestone(form){
	
	if(form.find('.blueBtn2').length > 0){
		form.each(function(){
			var inputs = $(this).find(':input');
			var selects = $(this).find('select.dropdown');
			var buttons = $(this).find(':button');			
			var anchorDisable = $(this).find('a.anchorDisable');
			var deleteJob = $(this).find('a.deleteJob');
			
			inputs.each(function() {
				if($(this).attr('data-btn-type') != 'noDisable')
					$(this).attr('disabled', 'disabled');
			});
			
			selects.each(function() {
				if($(this).attr('name') != 'addms'){
					$(this).attr('disabled', 'disabled');	
					$(this).dropkick('refresh');
				}
			});
			
			anchorDisable.each(function() {
				$(this).removeAttr('onclick');
			});
			
			deleteJob.each(function() {
				$(this).removeClass('deleteJob').addClass('deleteJobInactive');
			});
			/*buttons.each(function() {
				$(this).attr('disabled', 'disabled');
			});*/
		});
	}
}

$(document).on('keydown', '.taskInput', function (e) {
	if(e.keyCode == 13)
		$(this).siblings('input').click();
});

/**
 *
 */
function updateTaskCost(id, milestone_id, task, cost){
	if(isNaN(cost)){
		alert('Please enter decimal value only.');
		return;
	}
	var url = '/updateworkshoptask';
	$.ajax({
		url: url,
		type: 'POST',
		data: {id: id, milestone_id: milestone_id, task: task, cost: cost},
		success: function(response){
			if(response == 1)
				alert('Cost updated successfully');
			else
				alert('Unable to updated cost');
		}
	});
}