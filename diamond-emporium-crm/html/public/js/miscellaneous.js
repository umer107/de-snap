function getMiscellaneous(recordsPerPage, columnList, keyword){
	$after = $('#jqxMiscellaneous').prev('#bindAfterThis');
	if ($after.length == 0) return;

	if(keyword){
		var url = '/ajaxgetmiscellaneous?keyword='+keyword;
	} else {
		var url = '/ajaxgetmiscellaneous';
	}

	$('#jqxMiscellaneous').remove();
    $after.after('<div class="formTable manageMembers" id="jqxMiscellaneous"></div>');

	// prepare the data
	var source =
	{
		datatype: "json",
		datafields: [
			{ name: 'id' },
			{ name: 'stock_code' },
			{ name: 'title' },
			{ name: 'cad_code' },
			{ name: 'description' },
			{ name: 'price' },
			{ name: 'created_date' },
			{ name: 'company_name' },
			{ name: 'supplier_name' },
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
		sortcolumn: 'id',
		sortdirection: 'desc',
		beforeprocessing: function (data) {
			source.totalrecords = data.TotalRows;
		},
		filter: function () {
			// update the grid and send a request to the server.
			$("#jqxMiscellaneous").jqxGrid('updatebounddata', 'filter');
		}
	};
	
	var pageable = true, sortable = true;
	var dataAdapter = new $.jqx.dataAdapter(source, {
		downloadComplete: function (data, status, xhr) {},
		loadComplete: function (data) { 
			if($("#jqxMiscellaneous").find('.jqx-grid-empty-cell').length>0 ){					
					if($("#jqxMiscellaneous").find('.jqx-grid-empty-cell').parent('div').height()<30){
						$('.jqx-grid-empty-cell').parent('div').addClass('norecordsFoundRow');
						$('.jqx-grid-empty-cell').closest("#jqxMiscellaneous").addClass('noInfoFound');
					}
					if(keyword){
						$("#jqxMiscellaneous").find('.jqx-grid-empty-cell >span').text("No matches were found");
						//$('#searchResultsHint').text("No matches were found");
					} else {
						$("#jqxMiscellaneous").find('.jqx-grid-empty-cell >span').text("No records found");
					}

			}else{
				if($("#jqxMiscellaneous").hasClass('noInfoFound')){
					$("#jqxMiscellaneous").removeClass('noInfoFound');
				}
			}
		},
		loadError: function (xhr, status, error) { }
	});
	
	var consign = function(row, columnfield, value, defaulthtml, columnproperties){
		var datarow = $('#jqxMiscellaneous').jqxGrid('getrowdata', row);
		var html = '';
		if(datarow)
			html = '<a href="javascript:;" data-popup="consignItem" class="cmnBtn lightBoxClick" onclick="openConsignForm('+datarow.id+', \'miscellaneous\')">Consign</a>';
		return html;
	};
	
	if(columnList == '' || columnList == undefined){
		/* Get from hidden input if it's there */
		if ($('#miscellaneous_columnList').length) {
			columnList = $('#miscellaneous_columnList').val();
		} else {
			/* Default columns */
			columnList = 'consign_button,inventory_status_name,inventory_status_reason,stock_code,title,description,price,supplier_name,inventory_type,inventory_tracking_status,inventory_tracking_reason,reserve_time,reserve_notes';
		}
	}
	$('#miscellaneous_columnList').val(columnList);

	var colList = columnList.split(',');
	var gridColumnList = new Array();
	if(colList.length>5){
		for(i = 0; i < colList.length; i++){
			if(colList[i] == 'consign_button'){
				gridColumnList.push({ text: 'Consignment', type: 'string', cellsrenderer: consign, width:'10%' });
			}
			if(colList[i] == 'inventory_status_name'){
				gridColumnList.push({ text: 'Inventory Status', type: 'string', datafield: 'inventory_status_name', width:'10%' });
			}
			if(colList[i] == 'inventory_status_reason'){
				gridColumnList.push({ text: 'Reason', type: 'string', datafield: 'inventory_status_reason', width:'10%' });
			}
			if(colList[i] == 'reserve_time'){
				gridColumnList.push({ text: 'Reserve Time', type: 'string', datafield: 'reserve_time', width:'10%' });
			}
			if(colList[i] == 'reserve_notes'){
				gridColumnList.push({ text: 'Reserve Note', type: 'string', datafield: 'reserve_notes', width:'10%' });
			}
			if(colList[i] == 'inventory_type'){
				gridColumnList.push({ text: 'Inventory Type', type: 'string', datafield: 'inventory_type', width:'10%' });
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
			if(colList[i] == 'stock_code'){
				gridColumnList.push({ text: 'Stock Code', type: 'string', datafield: 'stock_code',  width:'10%'});
			}
			if(colList[i] == 'supplier_name'){
				gridColumnList.push({ text: 'Supplier Name', type: 'string', datafield: 'supplier_name', width:'10%' });
			}
			if(colList[i] == 'title'){
				gridColumnList.push({ text: 'Title', type: 'string', datafield: 'title', width:'10%' });
			}
			if(colList[i] == 'description'){
				gridColumnList.push({ text: 'Description', type: 'string', datafield: 'description', width:'10%' });
			}
			if(colList[i] == 'price'){
				gridColumnList.push({ text: 'AUD RRP', type: 'string', datafield: 'price', width:'10%' });
			}
			if(colList[i] == 'owner_name'){
				gridColumnList.push({ text: 'Record Owner', type: 'string', datafield: 'owner_name', width:'10%' });
			}
		}
	}else{
		var countData = colList.length;
		var colWidthData = 100/countData;
			
		for(i = 0; i < colList.length; i++){
			if(colList[i] == 'consign_button'){
				gridColumnList.push({ text: 'Consignment', type: 'string', cellsrenderer: consign, width:colWidthData+'%'});
			}
			if(colList[i] == 'inventory_status_name'){
				gridColumnList.push({ text: 'Inventory Status', type: 'string', datafield: 'inventory_status_name', width:colWidthData+'%'});
			}
			if(colList[i] == 'inventory_status_reason'){
				gridColumnList.push({ text: 'Reason', type: 'string', datafield: 'inventory_status_reason', width:colWidthData+'%'});
			}
			if(colList[i] == 'reserve_time'){
				gridColumnList.push({ text: 'Reserve Time', type: 'string', datafield: 'reserve_time', width:colWidthData+'%'});
			}
			if(colList[i] == 'reserve_notes'){
				gridColumnList.push({ text: 'Reserve Note', type: 'string', datafield: 'reserve_notes', width:colWidthData+'%'});
			}
			if(colList[i] == 'inventory_type'){
				gridColumnList.push({ text: 'Inventory Type', type: 'string', datafield: 'inventory_type', width:colWidthData+'%'});
			}
			if(colList[i] == 'inventory_tracking_status'){
				gridColumnList.push({ text: 'Tracking', type: 'string', datafield: 'inventory_tracking_status', width:colWidthData+'%'});
			}
			if(colList[i] == 'inventory_tracking_reason'){
				gridColumnList.push({ text: 'Tracking Reason', type: 'string', datafield: 'inventory_tracking_reason', width:colWidthData+'%'});
			}
			if(colList[i] == 'tracking_id'){
				gridColumnList.push({ text: 'Tracking ID', type: 'string', datafield: 'tracking_id', width:colWidthData+'%'});
			}
			if(colList[i] == 'stock_code'){
				gridColumnList.push({ text: 'Stock Code', type: 'string', datafield: 'stock_code', width:colWidthData+'%'});
			}
			if(colList[i] == 'supplier_name'){
				gridColumnList.push({ text: 'Supplier Name', type: 'string', datafield: 'supplier_name', width:colWidthData+'%'});
			}
			if(colList[i] == 'title'){
				gridColumnList.push({ text: 'Title', type: 'string', datafield: 'title', width:colWidthData+'%'});
			}
			if(colList[i] == 'description'){
				gridColumnList.push({ text: 'Description', type: 'string', datafield: 'description', width:colWidthData+'%'});
			}
			if(colList[i] == 'price'){
				gridColumnList.push({ text: 'AUD RRP', type: 'string', datafield: 'price', width:colWidthData+'%'});
			}
			if(colList[i] == 'owner_name'){
				gridColumnList.push({ text: 'Record Owner', type: 'string', datafield: 'owner_name', width:colWidthData+'%'});
			}
		}
	}

		
	$("#jqxMiscellaneous").jqxGrid(
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
		columns: gridColumnList
	});

	/* See if we have an override cellclick binding */
	if ($('#miscellaneous_cellClick').length) {
		$('#jqxMiscellaneous').bind('cellclick', window[$('#miscellaneous_cellClick').val()]);
	} else {
		$('#jqxMiscellaneous').bind('cellclick', function(event)  {
			var current_index = event.args.rowindex;
			var current_column = event.args.column.datafield;
			var datarow = $('#jqxMiscellaneous').jqxGrid('getrowdata', current_index);
			var url = '/miscellaneousdetails/'+datarow.id;
			//alert(current_column);
			if(current_column != null){
				$(location).attr('href', url);
			}
			$('#gridTypeId').val(datarow.id);
			$('#gridType').val('miscellaneous');
		});
	}
	
	$("#jqxMiscellaneous").bind("sort", function (event) {
    	$("#jqxMiscellaneous").jqxGrid('updatebounddata', 'filter');
	});
	
	$("#jqxMiscellaneous").bind("pagesizechanged", function (event) {
		var args = event.args;
		var pagenumber = args.pagenum;
		var pagesize = args.pagesize;
		$('#pageSizeGrabing').val(pagesize);
	});
}

function getMiscellaneousFromGridView(recordsPerPage, columnList, keyword){
	$('#searchResultsHint').text('');
	if(keyword){
		var url = '/ajaxgetmiscellaneous?keyword='+keyword;
	} else {
		var url = '/ajaxgetmiscellaneous';
	}
	
	// prepare the data
	var source =
	{
		datatype: "json",
		datafields: [
			{ name: 'id' },
			{ name: 'stock_code' },
			{ name: 'title' },
			{ name: 'description' },
			{ name: 'price' },
			{ name: 'company_name' },
			{ name: 'supplier_name' },
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
			$("#jqxMiscellaneous").jqxGrid('updatebounddata', 'filter');
		}
	};
	
	var pageable = true, sortable = true;
	var dataAdapter = new $.jqx.dataAdapter(source, {
		downloadComplete: function (data, status, xhr) { },
		loadComplete: function (data) { 
			if($("#jqxMiscellaneous").find('.jqx-grid-empty-cell').length>0){
					if($("#jqxMiscellaneous").find('.jqx-grid-empty-cell').parent('div').height()<30){
						$('.jqx-grid-empty-cell').parent('div').addClass('norecordsFoundRow');
						$('.jqx-grid-empty-cell').closest("#jqxMiscellaneous").addClass('noInfoFound');
					}
					if(keyword){
						$("#jqxMiscellaneous").find('.jqx-grid-empty-cell >span').text("No matches were found");
						//$('#searchResultsHint').text("No matches were found");
					} else {
						$("#jqxMiscellaneous").find('.jqx-grid-empty-cell >span').text("No records found");
					}
			}else{
				if($("#jqxMiscellaneous").hasClass('noInfoFound')){
					$("#jqxMiscellaneous").removeClass('noInfoFound');
				}
			}
		},
		loadError: function (xhr, status, error) { }
	});
	
	var consign = function(row, columnfield, value, defaulthtml, columnproperties){
		var datarow = $('#jqxMiscellaneous').jqxGrid('getrowdata', row);
		var html = '';
		if(datarow)
			html = '<a href="javascript:;" data-popup="consignItem" class="cmnBtn lightBoxClick" onclick="openConsignForm('+datarow.id+', \'miscellaneous\')">Consign</a>';
		return html;
	};
	
		
		
	if(colList.length == gridColumnList.length){
		$("#jqxMiscellaneous").jqxGrid(
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
	
		//$("#jqxMiscellaneous").next('.pagerHTML').html($('#pagerjqxMiscellaneous'));
		
		$('#jqxMiscellaneous').bind('cellclick', function(event)  {
			var current_index = event.args.rowindex;
			var current_column = event.args.column.datafield;
			var datarow = $('#jqxMiscellaneous').jqxGrid('getrowdata', current_index);
			var url = '/miscellaneousdetails/'+datarow.id;
			
			if(current_column != 'consign_button'){
				$(location).attr('href', url);
			}
			$('#gridTypeId').val(datarow.id);
			$('#gridType').val('miscellaneous');
			// Use datarow for display of data in div outside of grid
		});
	}
	
	$("#jqxMiscellaneous").bind("sort", function (event) {
	   /* var sortinformation = event.args.sortinformation;
	    
	    var sortdatafield = sortinformation.sortcolumn;
	    
	    var sortorder = sortinformation.sortdirection.ascending ? "asc" : "desc";
	    
	    var sortUrl = '/ajaxgetdiamonds?sortdatafield='+sortdatafield+'&sortorder='+sortorder;*/
	    
	   //$.get(sortUrl, function(source){
	    	//$("#jqxMiscellaneous").jqxGrid('databind', source);
	    	$("#jqxMiscellaneous").jqxGrid('updatebounddata', 'filter');
	   //});
	});
	
	$("#jqxMiscellaneous").bind("pagesizechanged", function (event) {
		var args = event.args;
		var pagenumber = args.pagenum;
		var pagesize = args.pagesize;
		$('#pageSizeGrabing').val(pagesize);
	});
}

function validateMiscellaneous(){
	var errors = 0;	
	/*$('.errorText').remove();
	if($('#frm_supplier #mobile').val() == ''){
		$( '<p class="errorText">Please select mobile</p>' ).insertAfter( '#frm_supplier #oppmobilelookup' );
		errors++;
	}
	
	if($('#frm_supplier #supplier_type option:selected').val() == ''){
		$( '<p class="errorText">Please select supplier type</p>' ).insertAfter( '#frm_supplier #supplier_type' );
		errors++;
	}
	
	if($('#frm_supplier #lead_source option:selected').val() == ''){
		$( '<p class="errorText">Please select lead source</p>' ).insertAfter( '#frm_supplier #lead_source' );
		errors++;
	}
	
	if($('#frm_supplier #looking_for').val() == ''){
		$( '<p class="errorText">Please enter what are they looking for?</p>' ).insertAfter( '#frm_supplier #looking_for' );
		errors++;
	}
	
	if($('#frm_supplier #product option:selected').val() == 0){
		$( '<p class="errorText">Please select product</p>' ).insertAfter( '#frm_supplier #product' );
		errors++;
	}
	
	if($('#frm_supplier #preferred_contact option:selected').val() == ''){
		$( '<p class="errorText">Please select preferred method of contact</p>' ).insertAfter( '#frm_supplier #preferred_contact' );
		errors++;
	}
	
	if($('#frm_supplier #progress_of_supplier option:selected').val() == ''){
		$( '<p class="errorText">Please select progress of supplier</p>' ).insertAfter( '#frm_supplier #progress_of_supplier' );
		errors++;
	}
	
	if($('#frm_supplier #urgency option:selected').val() == ''){
		$( '<p class="errorText">Please select urgency</p>' ).insertAfter( '#frm_supplier #urgency' );
		errors++;
	}
	
	if($('#frm_supplier #budget').val() == ''){
		$( '<p class="errorText">Please enter budget</p>' ).insertAfter( '#frm_supplier #budget' );
		errors++;
	}

	if($('#frm_supplier #rating option:selected').val() == ''){
		$( '<p class="errorText">Please select rating</p>' ).insertAfter( '#frm_supplier #rating' );
		errors++;
	}
	
	if($('#frm_supplier #probability option:selected').val() == ''){
		$( '<p class="errorText">Please select probability</p>' ).insertAfter( '#frm_supplier #probability' );
		errors++;
	}
	
	if($('#frm_supplier .datepickerInput').val() == ''){
		$( '<p class="errorText">Please select est. close date</p>' ).insertAfter( '#frm_supplier .datepickerInput' );
		errors++;
	}*/
	
	return errors;
}

function validateUpdateSupplier(){
	var errors = 0;	
	/*$('.errorText').remove();
	if($('#frm_supplier #mobile').val() == ''){
		$( '<p class="errorText">Please select mobile</p>' ).insertAfter( '#frm_supplier #oppmobilelookup' );
		errors++;
	}
	
	if($('#frm_supplier #supplier_type option:selected').val() == ''){
		$( '<p class="errorText">Please select supplier type</p>' ).insertAfter( '#frm_supplier #supplier_type' );
		errors++;
	}
	
	if($('#frm_supplier #lead_source option:selected').val() == ''){
		$( '<p class="errorText">Please select lead source</p>' ).insertAfter( '#frm_supplier #lead_source' );
		errors++;
	}
	
	if($('#frm_supplier #looking_for').val() == ''){
		$( '<p class="errorText">Please enter what are they looking for?</p>' ).insertAfter( '#frm_supplier #looking_for' );
		errors++;
	}
	
	if($('#frm_supplier #product option:selected').val() == 0){
		$( '<p class="errorText">Please select product</p>' ).insertAfter( '#frm_supplier #product' );
		errors++;
	}
	
	if($('#frm_supplier #preferred_contact option:selected').val() == ''){
		$( '<p class="errorText">Please select preferred method of contact</p>' ).insertAfter( '#frm_supplier #preferred_contact' );
		errors++;
	}
	
	if($('#frm_supplier #progress_of_supplier option:selected').val() == ''){
		$( '<p class="errorText">Please select progress of supplier</p>' ).insertAfter( '#frm_supplier #progress_of_supplier' );
		errors++;
	}
	
	if($('#frm_supplier #urgency option:selected').val() == ''){
		$( '<p class="errorText">Please select urgency</p>' ).insertAfter( '#frm_supplier #urgency' );
		errors++;
	}
	
	if($('#frm_supplier #budget').val() == ''){
		$( '<p class="errorText">Please enter budget</p>' ).insertAfter( '#frm_supplier #budget' );
		errors++;
	}

	if($('#frm_supplier #rating option:selected').val() == ''){
		$( '<p class="errorText">Please select rating</p>' ).insertAfter( '#frm_supplier #rating' );
		errors++;
	}
	
	if($('#frm_supplier #probability option:selected').val() == ''){
		$( '<p class="errorText">Please select probability</p>' ).insertAfter( '#frm_supplier #probability' );
		errors++;
	}
	
	if($('#frm_supplier .datepickerInput').val() == ''){
		$( '<p class="errorText">Please select est. close date</p>' ).insertAfter( '#frm_supplier .datepickerInput' );
		errors++;
	}*/
	
	return errors;
}

function saveMiscellaneous(form){
		var errors = validateMiscellaneous();
		if(errors == 0){
			//$('#miscellaneous_save').attr('disabled','disabled');
			var url = '/savemiscellaneous';
			var data = $(form).serialize();
			$.post(url, data, function(response){
				if(response > 0){
					$(form)[0].reset();
					$('#addItem .closePopup').click();
					getCount('inventory');
					$('#jqxMiscellaneous').jqxGrid('updatebounddata');
				}
			});
		}else{
			//$('#miscellaneous_save').removeAttr('disabled');
		}
}

function updateMiscellaneous(form){
	if($('#saveMiscellaneousButton').text() === "Edit"){
		$('#saveMiscellaneousButton').text('Save');
		$('#frm_miscellaneous .displayHide').show();
		$('#frm_miscellaneous .hiddenUnqValues').hide();
	} else if($('#saveMiscellaneousButton').text() === "Save"){
		var errors = validateUpdateSupplier();
		if(errors == 0){
			var url = '/savemiscellaneous';
			var data = $(form).serialize();
			$.post(url, data, function(response){
				if(response > 0){
					alert('Miscellaneous details updated successfully');
					$('#title_miscellaneous').text($('#title').val());
					$('#cad_code_miscellaneous').text($('#cad_code').val());
					$('#description_miscellaneous').text($('#description').val());
					$('#supplier_name_miscellaneous').text($('#supplier_name').val());
					$('#price_miscellaneous').text($('#price').val());
					$('#saveMiscellaneousButton').text('Edit');
					$('#frm_miscellaneous .displayHide').hide();
					$('#frm_miscellaneous .hiddenUnqValues').show();
					//location.reload();
				}
			});
		}
	}
}

function deleteMiscellaneous(id){
	if (confirm("Are you sure you want to delete")) {
        var url = '/deletemiscellaneous';
		var data = {'id':id};
		$.post(url, data, function(response){
			if(response == 1){
				window.location.href = '/inventory-miscellaneous';
			}
		});
    } else {
		return false;
	}
}