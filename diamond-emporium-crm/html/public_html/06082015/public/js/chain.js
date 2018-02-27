function getChain(recordsPerPage, keyword){
	if(keyword){
		var url = '/ajaxgetchain?keyword='+keyword;
	} else {
		var url = '/ajaxgetchain';
	}
	InitGrid();
	function InitGrid() {
		$('#jqxChain').remove();
        $('#bindAfterThis').after('<div class="formTable manageMembers" id="jqxChain"></div>');
	}
	// prepare the data
	var source =
	{
		datatype: "json",
		datafields: [
			{ name: 'id' },
			{ name: 'stock_code' },
			{ name: 'style' },
			{ name: 'length' },
			{ name: 'thickness' },
			{ name: 'metal_type' },
			{ name: 'metal_weight' },
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
			$("#jqxChain").jqxGrid('updatebounddata', 'filter');
		}
	};	
	
	var pageable = true, sortable = true;
	var dataAdapter = new $.jqx.dataAdapter(source, {
		downloadComplete: function (data, status, xhr) {},
		loadComplete: function (data) { 
			if($("#jqxChain").find('.jqx-grid-empty-cell').length>0 ){					
					if($("#jqxChain").find('.jqx-grid-empty-cell').parent('div').height()<30){
						$('.jqx-grid-empty-cell').parent('div').addClass('norecordsFoundRow');
						$('.jqx-grid-empty-cell').closest("#jqxChain").addClass('noInfoFound');
					}
					if(keyword){
						$("#jqxChain").find('.jqx-grid-empty-cell >span').text("No matches were found");
						//$('#searchResultsHint').text("No matches were found");
					} else {
						$("#jqxChain").find('.jqx-grid-empty-cell >span').text("No records found");
					}
			}else{
				if($("#jqxChain").hasClass('noInfoFound')){
					$("#jqxChain").removeClass('noInfoFound');
				}
			}
		},
		loadError: function (xhr, status, error) { }
	});
	
	var consign = function(row, columnfield, value, defaulthtml, columnproperties){
		var datarow = $('#jqxChain').jqxGrid('getrowdata', row);
		var html = '';
		if(datarow)
			html = '<a href="javascript:;" data-popup="consignItem" class="cmnBtn lightBoxClick" onclick="openConsignForm('+datarow.id+', \'chain\')">Consign</a>';
		return html;
	};
		
	$("#jqxChain").jqxGrid(
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
			{ text: 'Consignment', type: 'string', cellsrenderer: consign, width:'10%' },
			{ text: 'Inventory Status', type: 'string', datafield: 'inventory_status_name', width:'10%' },
			{ text: 'Reason', type: 'string', datafield: 'inventory_status_reason', width:'10%' },
			{ text: 'Reserve Time', type: 'string', datafield: 'reserve_time', width:'10%' },
			{ text: 'Reserve Note', type: 'string', datafield: 'reserve_notes', width:'10%' },
			{ text: 'Inventory Type', type: 'string', datafield: 'inventory_type', width:'10%' },
			{ text: 'Tracking', type: 'string', datafield: 'inventory_tracking_status', width:'10%' },
			{ text: 'Tracking Reason', type: 'string', datafield: 'inventory_tracking_reason', width:'10%' },
			{ text: 'Tracking ID', type: 'string', datafield: 'tracking_id', width:'10%' },
			{ text: 'Stock Code', type: 'string', datafield: 'stock_code', width:'10%'},
			{ text: 'Supplier Name', type: 'string', datafield: 'supplier_name', width:'10%' },
			{ text: 'Style', type: 'string', datafield: 'style', width:'10%' },
			{ text: 'Length', type: 'string', datafield: 'length', width:'10%' },
			{ text: 'Thickness', type: 'string', datafield: 'thickness', width:'10%' },
			{ text: 'Metal Type', type: 'string', datafield: 'metal_type', width:'10%' },
			{ text: 'Metal Weight', type: 'string', datafield: 'metal_weight', width:'10%' },
			{ text: 'Price', type: 'string', datafield: 'price', width:'10%' },
			{ text: 'Record Owner', type: 'string', datafield: 'owner_name', width:'10%' },
		]

	});

	
	$('#jqxChain').bind('cellclick', function(event)  {
		var current_index = event.args.rowindex;
		var current_column = event.args.column.datafield;
		var datarow = $('#jqxChain').jqxGrid('getrowdata', current_index);
		var url = '/chaindetails/'+datarow.id;
		//alert(current_column);
		if(current_column != null){
			$(location).attr('href', url);
		}
		$('#gridTypeId').val(datarow.id);
		$('#gridType').val('chain');
	});
	
	$("#jqxChain").bind("sort", function (event) {
	    /*var sortinformation = event.args.sortinformation;
	    
	    var sortdatafield = sortinformation.sortcolumn;
	    
	    var sortorder = sortinformation.sortdirection.ascending ? "asc" : "desc";
	    
	    var sortUrl = '/ajaxgetdiamonds?sortdatafield='+sortdatafield+'&sortorder='+sortorder;*/
	    
	   //$.get(sortUrl, function(source){
	    	//$("#jqxChain").jqxGrid('databind', source);
	    	$("#jqxChain").jqxGrid('updatebounddata', 'filter');
	   //});
	});
	
	$("#jqxChain").bind("pagesizechanged", function (event) {
		var args = event.args;
		var pagenumber = args.pagenum;
		var pagesize = args.pagesize;
		$('#pageSizeGrabing').val(pagesize);
	});
}

function getChainFromGridView(recordsPerPage, columnList, keyword){
	$('#searchResultsHint').text('');
	if(keyword){
		var url = '/ajaxgetchain?keyword='+keyword;
	} else {
		var url = '/ajaxgetchain';
	}
	
	// prepare the data
	var source =
	{
		datatype: "json",
		datafields: [
			{ name: 'id' },
			{ name: 'stock_code' },
			{ name: 'style' },
			{ name: 'length' },
			{ name: 'thickness' },
			{ name: 'metal_type' },
			{ name: 'metal_weight' },
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
			$("#jqxChain").jqxGrid('updatebounddata', 'filter');
		}
	};
	
	var pageable = true, sortable = true;
	var dataAdapter = new $.jqx.dataAdapter(source, {
		downloadComplete: function (data, status, xhr) { },
		loadComplete: function (data) { 
			if($("#jqxChain").find('.jqx-grid-empty-cell').length>0){
					if($("#jqxChain").find('.jqx-grid-empty-cell').parent('div').height()<30){
						$('.jqx-grid-empty-cell').parent('div').addClass('norecordsFoundRow');
						$('.jqx-grid-empty-cell').closest("#jqxChain").addClass('noInfoFound');
					}
					if(keyword){
						$("#jqxChain").find('.jqx-grid-empty-cell >span').text("No matches were found");
						//$('#searchResultsHint').text("No matches were found");
					} else {
						$("#jqxChain").find('.jqx-grid-empty-cell >span').text("No records found");
					}
			}else{
				if($("#jqxChain").hasClass('noInfoFound')){
					$("#jqxChain").removeClass('noInfoFound');
				}
			}
		},
		loadError: function (xhr, status, error) { }
	});
	
	var consign = function(row, columnfield, value, defaulthtml, columnproperties){
		var datarow = $('#jqxChain').jqxGrid('getrowdata', row);
		var html = '';
		if(datarow)
			html = '<a href="javascript:;" data-popup="consignItem" class="cmnBtn lightBoxClick" onclick="openConsignForm('+datarow.id+', \'chain\')">Consign</a>';
		return html;
	};
	
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
			if(colList[i] == 'style'){
				gridColumnList.push({ text: 'Style', type: 'string', datafield: 'style', width:'10%' });
			}
			if(colList[i] == 'length'){
				gridColumnList.push({ text: 'Length', type: 'string', datafield: 'length', width:'10%' });
			}
			if(colList[i] == 'thickness'){
				gridColumnList.push({ text: 'Thickness', type: 'string', datafield: 'thickness', width:'10%' });
			}
			if(colList[i] == 'metal_type'){
				gridColumnList.push({ text: 'Metal Type', type: 'string', datafield: 'metal_type', width:'10%' });
			}
			if(colList[i] == 'metal_weight'){
				gridColumnList.push({ text: 'Metal Weight', type: 'string', datafield: 'metal_weight', width:'10%' });
			}
			if(colList[i] == 'price'){
				gridColumnList.push({ text: 'Price', type: 'string', datafield: 'price', width:'10%' });
			}
			if(colList[i] == 'owner_name'){
				gridColumnList.push({ text: 'Record Owner', type: 'string', datafield: 'owner_name', width:'10%' });
			}
		}
		$("#jqxChain").jqxGrid({columns:gridColumnList});
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
			if(colList[i] == 'style'){
				gridColumnList.push({ text: 'Style', type: 'string', datafield: 'style', width:colWidthData+'%'});
			}
			if(colList[i] == 'length'){
				gridColumnList.push({ text: 'Length', type: 'string', datafield: 'length', width:colWidthData+'%'});
			}
			if(colList[i] == 'thickness'){
				gridColumnList.push({ text: 'Thickness', type: 'string', datafield: 'thickness', width:colWidthData+'%'});
			}
			if(colList[i] == 'metal_type'){
				gridColumnList.push({ text: 'Metal Type', type: 'string', datafield: 'metal_type', width:colWidthData+'%'});
			}
			if(colList[i] == 'metal_weight'){
				gridColumnList.push({ text: 'Metal Weight', type: 'string', datafield: 'metal_weight', width:colWidthData+'%'});
			}
			if(colList[i] == 'price'){
				gridColumnList.push({ text: 'Price', type: 'string', datafield: 'price', width:colWidthData+'%'});
			}
			if(colList[i] == 'owner_name'){
				gridColumnList.push({ text: 'Record Owner', type: 'string', datafield: 'owner_name', width:colWidthData+'%'});
			}
		}
		$("#jqxChain").jqxGrid({columns:gridColumnList});
	}		
		
	if(colList.length == gridColumnList.length){
		$("#jqxChain").jqxGrid(
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
	
		//$("#jqxChain").next('.pagerHTML').html($('#pagerjqxChain'));
		
		$('#jqxChain').bind('cellclick', function(event)  {
			var current_index = event.args.rowindex;
			var current_column = event.args.column.datafield;
			var datarow = $('#jqxChain').jqxGrid('getrowdata', current_index);
			var url = '/chaindetails/'+datarow.id;
			
			if(current_column != 'consign_button'){
				$(location).attr('href', url);
			}
			$('#gridTypeId').val(datarow.id);
			$('#gridType').val('chain');
			// Use datarow for display of data in div outside of grid
		});
	}
	
	$("#jqxChain").bind("sort", function (event) {
	   /* var sortinformation = event.args.sortinformation;
	    
	    var sortdatafield = sortinformation.sortcolumn;
	    
	    var sortorder = sortinformation.sortdirection.ascending ? "asc" : "desc";
	    
	    var sortUrl = '/ajaxgetdiamonds?sortdatafield='+sortdatafield+'&sortorder='+sortorder;*/
	    
	   //$.get(sortUrl, function(source){
	    	//$("#jqxChain").jqxGrid('databind', source);
	    	$("#jqxChain").jqxGrid('updatebounddata', 'filter');
	   //});
	});
	
	$("#jqxChain").bind("pagesizechanged", function (event) {
		var args = event.args;
		var pagenumber = args.pagenum;
		var pagesize = args.pagesize;
		$('#pageSizeGrabing').val(pagesize);
	});
}

function validateChain(){
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

function saveChain(form){
		var errors = validateChain();
		if(errors == 0){
			//$('#chain_save').attr('disabled','disabled');
			var url = '/savechain';
			var data = $(form).serialize();
			$.post(url, data, function(response){
				if(response > 0){
					$(form)[0].reset();
					$('#addItem .closePopup').click();
					getCount('inventory');
					$('#jqxChain').jqxGrid('updatebounddata');
				}
			});
		}else{
			//$('#chain_save').removeAttr('disabled');
		}
}

function updateChain(form){
	if($('#saveChainButton').text() === "Edit"){
		$('#saveChainButton').text('Save');
		$('#frm_chain .displayHide').show();
		$('#frm_chain .hiddenUnqValues').hide();
	} else if($('#saveChainButton').text() === "Save"){
		var errors = validateUpdateSupplier();
		if(errors == 0){
			var url = '/savechain';
			var data = $(form).serialize();
			$.post(url, data, function(response){
				if(response > 0){
					alert('Chain details updated successfully');
					
					var style_chain = $('#style option:selected').val() > 0 ? $('#style option:selected').text() : '';
					$('#style_chain').text(style_chain);
					
					if($('#style option:selected').text() == "Other"){
						$('#other_style_chain').text($('#other_style').val());
						$('#other_style_div').show();
					} else {
						$('#other_style').val('');
						$('#other_style_chain').text('');
						$('#other_style_div').hide();
					}
					$('#length_chain').text($('#length').val());
					$('#thickness_chain').text($('#thickness').val());
					
					var metal_type_chian = $('#metal_type option:selected').val() > 0 ? $('#metal_type option:selected').text() : '';
					$('#metal_type_chian').text(metal_type_chian);
					
					$('#metal_weight_chain').text($('#metal_weight').val());
					$('#description_chain').text($('#description').val());
					$('#supplier_name_chain').text($('#supplier_name').val());
					$('#price_chain').text($('#price').val());
					
					var image_chain = $('#image').val() ? '<img src="/inventory_images/'+$('#image').val()+'" height="98px" width="234px">' : '';
					$('#image_chain').html(image_chain);
					
					$('#saveChainButton').text('Edit');
					$('#frm_chain .displayHide').hide();
					$('#frm_chain .hiddenUnqValues').show();
					//location.reload();
				}
			});
		}
	}
}

function deleteChain(id){
	if (confirm("Are you sure you want to delete")) {
        var url = '/deletechain';
		var data = {'id':id};
		$.post(url, data, function(response){
			if(response == 1){
				window.location.href = '/inventory-chain';
			}
		});
    } else {
		return false;
	}
}