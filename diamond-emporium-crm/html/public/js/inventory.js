function getDiamonds(recordsPerPage, columnList, keyword, searchParams){
	$('#searchResultsHint').text('');
	
	if(keyword && !searchParams){
		var url = '/ajaxgetdiamonds?keyword='+keyword;
	} else if(!keyword && searchParams){
		var url = '/ajaxgetdiamonds?'+searchParams;
	}else if(keyword && searchParams){
		var url = '/ajaxgetdiamonds?keyword='+keyword+'&'+searchParams;
	}else if(!keyword && !searchParams){
		var url = '/ajaxgetdiamonds';
	}

	$('#jqxDianonds').remove();
    $('#bindAfterThis').after('<div class="formTable manageMembers" id="jqxDianonds"></div>');

    // prepare the data
	var source =
	{
		datatype: "json",
		datafields: [
			{ name: 'id' },
			{ name: 'stock_code' },
			{ name: 'cert_no' },
			{ name: 'cert_url' },
			{ name: 'video_url' },
			{ name: 'cut' },
			{ name: 'carat' },
			{ name: 'clarity' },
			{ name: 'depth' },
			{ name: 'table' },
			{ name: 'flurosence' },
			{ name: 'measurement' },
			{ name: 'description' },
			{ name: 'price' },
			{ name: 'created_date' },
			{ name: 'diamond_type' },
			{ name: 'color' },
			{ name: 'shape' },
			{ name: 'polish' },
			{ name: 'symmetry' },
			{ name: 'intensity' },
			{ name: 'overtone' },
			{ name: 'lab' },
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
			$("#jqxDianonds").jqxGrid('updatebounddata', 'filter');
		}
	};
	
	var pageable = true, sortable = true;
	var dataAdapter = new $.jqx.dataAdapter(source, {
		downloadComplete: function (data, status, xhr) {},
		loadComplete: function (data) { 
			if($("#jqxDianonds").find('.jqx-grid-empty-cell').length>0 ){					
					if($("#jqxDianonds").find('.jqx-grid-empty-cell').parent('div').height()<30){
						$('.jqx-grid-empty-cell').parent('div').addClass('norecordsFoundRow');
						$('.jqx-grid-empty-cell').closest("#jqxDianonds").addClass('noInfoFound');
					}
					if(keyword){
						$("#jqxDianonds").find('.jqx-grid-empty-cell >span').text("No matches were found");
						//$('#searchResultsHint').text("No matches were found");
					} else {
						$("#jqxDianonds").find('.jqx-grid-empty-cell >span').text("No records found");
					}
			}else{
				if($("#jqxDianonds").hasClass('noInfoFound')){
					$("#jqxDianonds").removeClass('noInfoFound');
				}
			}
		},
		loadError: function (xhr, status, error) { }
	});
	
	var renderConsign = function(row, columnfield, value, defaulthtml, columnproperties){
		var datarow = $('#jqxDianonds').jqxGrid('getrowdata', row);
		var html = '';
		if(datarow) {
			html = '<a href="javascript:;" data-popup="consignItem" class="cmnBtn lightBoxClick fl" onclick="openConsignForm('+datarow.id+', \'diamond\')">Consign</a>';
		}
		return html;
	};
	
	var renderDetails = function(row, columnfield, value, defaulthtml, columnproperties){
		var datarow = $('#jqxDianonds').jqxGrid('getrowdata', row);
		var html = '';
		if(datarow) {
			var details =
				'Reserve time: ' + datarow.reserve_time + '\n' +
				'Reserve note: ' + datarow.reserve_note +'\n' + 
				'Tracking: ' + datarow.inventory_tracking_status + '\n' +
				'Tracking reason: ' + datarow.inventory_status_reason + '\n' +
				'Tracking ID: ' + datarow.tracking_id + '\n' +
				'Stock code: ' + datarow.stock_code + '\n' +
				'Inventory type: ' + datarow.inventory_type + '\n' +
				'Owner: ' + datarow.owner_name;
			html = '<a href="javascript:;" class="cmnBtn fl" title="' + details + '">+</a>';
		}
		return html;
	};

	if(columnList == '' || columnList == undefined){
		/* Get from hidden input if it's there */
		if ($('#diamond_columnList').length) {
			columnList = $('#diamond_columnList').val();
		} else {
			/* Default columns */
			columnList =
				'consign_button,additional_details,inventory_status_name,inventory_status_reason,supplier_name,shape,carat,' +
				'color,clarity,cut,polish,symmetry,flurosence,lab,measurement,price';
		}
	}
	$('#diamond_columnList').val(columnList);
	
	var colList = columnList.split(',');
	var gridColumnList = new Array();
	if(colList.length > 6){
		for(i = 0; i < colList.length; i++){
			if(colList[i] == 'consign_button'){
				gridColumnList.push({ text: 'Consignment', type: 'string', cellsrenderer: renderConsign,  width:'10%'});
			}
			if(colList[i] == 'additional_details'){
				gridColumnList.push({ text: 'Details', type: 'string', cellsrenderer: renderDetails,  width:'5%'});
			}
			if(colList[i] == 'inventory_status_name'){
				gridColumnList.push({ text: 'Inventory <br /> Status', type: 'string', datafield: 'inventory_status_name',  width:'8%'});
			}
			if(colList[i] == 'inventory_status_reason'){
				gridColumnList.push({ text: 'Reason', type: 'string', datafield: 'inventory_status_reason',  width:'7%'});
			}
			if(colList[i] == 'lab'){
				gridColumnList.push({ text: 'Lab', type: 'string', datafield: 'lab', width:'5%'});
			}
			if(colList[i] == 'price'){
				gridColumnList.push({ text: 'AUD RRP', type: 'string', datafield: 'price', width:'5%'});
			}
			if(colList[i] == 'reserve_time'){
				gridColumnList.push({ text: 'Reserve <br /> Time', type: 'string', datafield: 'reserve_time',  width:'10%'});
			}
			if(colList[i] == 'reserve_notes'){
				gridColumnList.push({ text: 'Reserve Note', type: 'string', datafield: 'reserve_notes',  width:'15%'});
			}
			if(colList[i] == 'inventory_type'){
				gridColumnList.push({ text: 'Inventory <br /> Type', type: 'string', datafield: 'inventory_type',  width:'12%'});
			}
			if(colList[i] == 'inventory_tracking_status'){
				gridColumnList.push({ text: 'Tracking', type: 'string', datafield: 'inventory_tracking_status',  width:'10%'});
			}
			if(colList[i] == 'inventory_tracking_reason'){
				gridColumnList.push({ text: 'Tracking <br /> Reason', type: 'string', datafield: 'inventory_tracking_reason',  width:'10%'});
			}
			if(colList[i] == 'tracking_id'){
				gridColumnList.push({ text: 'Tracking ID', type: 'string', datafield: 'tracking_id',  width:'13%'});
			}			
			if(colList[i] == 'stock_code'){
				gridColumnList.push({ text: 'Stock Code', type: 'string', datafield: 'stock_code',  width:'10%'});
			}
			if(colList[i] == 'supplier_name'){
				gridColumnList.push({ text: 'Supplier <br /> Name', type: 'string', datafield: 'supplier_name',  width:'10%'});
			}
			if(colList[i] == 'cert_no'){
				gridColumnList.push({ text: 'Certificate <br /> Number', type: 'string', datafield: 'cert_no', width:'11%'});
			}
			if(colList[i] == 'shape'){
				gridColumnList.push({ text: 'Shape', type: 'string', datafield: 'shape',  width:'7%'});
			}
			if(colList[i] == 'color'){
				gridColumnList.push({ text: 'Colour', type: 'string', datafield: 'color', width:'7%'});
			}
			if(colList[i] == 'measurement'){
				gridColumnList.push({ text: 'Measurements', type: 'string', datafield: 'measurement', width:'12%'});
			}
			if(colList[i] == 'cut'){
				gridColumnList.push({ text: 'Cut', type: 'string', datafield: 'cut', width:'7%'});
			}
			if(colList[i] == 'carat'){
				gridColumnList.push({ text: 'Carat', type: 'string', datafield: 'carat', width:'7%'});
			}
			if(colList[i] == 'clarity'){
				gridColumnList.push({ text: 'Clarity', type: 'string', datafield: 'clarity', width:'7%'});
			}
			if(colList[i] == 'polish'){
				gridColumnList.push({ text: 'Polish', type: 'string', datafield: 'polish', width:'7%'});
			}
			if(colList[i] == 'symmetry'){
				gridColumnList.push({ text: 'Symmetry', type: 'string', datafield: 'symmetry', width:'8%'});
			}
			if(colList[i] == 'flurosence'){
				gridColumnList.push({ text: 'Fluro', type: 'string', datafield: 'flurosence', width:'7%'});
			}
			if(colList[i] == 'owner_name'){
				gridColumnList.push({ text: 'Owner <br /> Name', type: 'string', datafield: 'owner_name', width:'15%'});
			}
		}
	}else{
		var countData = colList.length;
		var colWidthData = 100/countData;
			
		for(i = 0; i < colList.length; i++){
			if(colList[i] == 'consign_button'){
				gridColumnList.push({ text: 'Consignment', type: 'string', cellsrenderer: renderConsign, width:colWidthData+'%'});
			}
			if(colList[i] == 'additional_details'){
				gridColumnList.push({ text: 'Details', type: 'string', cellsrenderer: renderDetails,  width:colWidthData+'%'});
			}
			if(colList[i] == 'inventory_status_name'){
				gridColumnList.push({ text: 'Inventory Status', type: 'string', datafield: 'inventory_status_name', width:colWidthData+'%'});
			}
			if(colList[i] == 'inventory_status_reason'){
				gridColumnList.push({ text: 'Reason', type: 'string', datafield: 'inventory_status_reason', width:colWidthData+'%'});
			}
			if(colList[i] == 'lab'){
				gridColumnList.push({ text: 'Lab', type: 'string', datafield: 'lab', width:colWidthData+'%'});
			}
			if(colList[i] == 'price'){
				gridColumnList.push({ text: 'AUD RRP', type: 'string', datafield: 'price', width:colWidthData+'%'});
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
			if(colList[i] == 'cert_no'){
				gridColumnList.push({ text: 'Certificate Number', type: 'string', datafield: 'cert_no', width:colWidthData+'%'});
			}
			if(colList[i] == 'shape'){
				gridColumnList.push({ text: 'Shape', type: 'string', datafield: 'shape', width:colWidthData+'%'});
			}
			if(colList[i] == 'color'){
				gridColumnList.push({ text: 'Colour', type: 'string', datafield: 'color', width:colWidthData+'%' });
			}
			if(colList[i] == 'measurement'){
				gridColumnList.push({ text: 'Measurements', type: 'string', datafield: 'measurement', width:colWidthData+'%' });
			}
			if(colList[i] == 'cut'){
				gridColumnList.push({ text: 'Cut', type: 'string', datafield: 'cut', width:colWidthData+'%'});
			}
			if(colList[i] == 'carat'){
				gridColumnList.push({ text: 'Carat', type: 'string', datafield: 'carat', width:colWidthData+'%'});
			}
			if(colList[i] == 'clarity'){
				gridColumnList.push({ text: 'Clarity', type: 'string', datafield: 'clarity', width:colWidthData+'%'});
			}
			if(colList[i] == 'polish'){
				gridColumnList.push({ text: 'Polish', type: 'string', datafield: 'polish', width:colWidthData+'%'});
			}
			if(colList[i] == 'symmetry'){
				gridColumnList.push({ text: 'Symmetry', type: 'string', datafield: 'symmetry', width:colWidthData+'%'});
			}
			if(colList[i] == 'flurosence'){
				gridColumnList.push({ text: 'Fluro', type: 'string', datafield: 'flurosence', width:colWidthData+'%'});
			}
			if(colList[i] == 'owner_name'){
				gridColumnList.push({ text: 'Owner <br /> Name', type: 'string', datafield: 'owner_name', width:colWidthData+'%'});
			}
		}
	}
		
	$("#jqxDianonds").jqxGrid(
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
	if ($('#diamond_cellClick').length) {
		$('#jqxDianonds').bind('cellclick', window[$('#diamond_cellClick').val()]);
	} else {
		$('#jqxDianonds').bind('cellclick', function(event)  {
			var current_index = event.args.rowindex;
			var current_column = event.args.column.datafield;
			var datarow = $('#jqxDianonds').jqxGrid('getrowdata', current_index);
			var url = '/diamonddetails/'+datarow.id;
			if(current_column != null){
				$(location).attr('href', url);
			}
			$('#gridTypeId').val(datarow.id);
			$('#gridType').val('supplier');
		});
	}
	
	$("#jqxDianonds").bind("sort", function (event) {
    	$("#jqxDianonds").jqxGrid('updatebounddata', 'filter');
	});
	
	$("#jqxDianonds").bind("pagesizechanged", function (event) {
		var args = event.args;
		var pagenumber = args.pagenum;
		var pagesize = args.pagesize;
		$('#pageSizeGrabing').val(pagesize);
	});
}

function suppliersLookup(recordsPerPage, selectedSupplierId, selectedSupplierName, keyword){
	if(keyword)
		var url = '/ajaxsupplierslookup?keyword='+keyword;
	else
		var url = '/ajaxsupplierslookup';
	
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
			$("#jqxSuppliersLookup").jqxGrid('updatebounddata', 'filter');
		}
	};
	
	var dataAdapter = new $.jqx.dataAdapter(source, {
		downloadComplete: function (data, status, xhr) { },
		loadComplete: function (data) { 
			if($("#jqxSuppliersLookup").find('.jqx-grid-empty-cell').length>0){
					if($("#jqxSuppliersLookup").find('.jqx-grid-empty-cell').parent('div').height()<30){
						$('.jqx-grid-empty-cell').parent('div').addClass('norecordsFoundRow');
						$('.jqx-grid-empty-cell').closest("#jqxSuppliersLookup").addClass('noInfoFound');
					}
					if(keyword){
						$("#jqxSuppliersLookup").find('.jqx-grid-empty-cell >span').text("No matches were found");
					} else {
						$("#jqxSuppliersLookup").find('.jqx-grid-empty-cell >span').text("No records found");
					}
			}else{
				if($("#jqxSuppliersLookup").hasClass('noInfoFound')){
					$("#jqxSuppliersLookup").removeClass('noInfoFound');
				}
			}
		},
		loadError: function (xhr, status, error) { }
	});
	
	$("#jqxSuppliersLookup").jqxGrid(
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
		//pagermode: 'simple',
		pagerheight: 50,
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
	
	$("#jqxSuppliersLookup").next('.pagerHTML').html($('#pagerjqxSuppliers'));
	
	$('#jqxSuppliersLookup').jqxGrid('clearselection');
	
	$('#jqxSuppliersLookup').bind('rowselect', function(event)  {
		var current_index = event.args.rowindex;
		var datarow = $('#jqxSuppliersLookup').jqxGrid('getrowdata', current_index);
		
		$('#'+selectedSupplierId).val(datarow.id);
		$('#'+selectedSupplierName).val(datarow.first_name + ' ' + datarow.last_name);
		
		$('#supplierlookup .closePopup').click();
		
		// Use datarow for display of data in div outside of grid
	});
	
	$("#jqxSuppliersLookup").bind("sort", function (event) {
	    /*var sortinformation = event.args.sortinformation;
	    
	    var sortdatafield = sortinformation.sortcolumn;
	    
	    var sortorder = sortinformation.sortdirection.ascending ? "asc" : "desc";
	    
	    var sortUrl = '/ajaxcustomerslookup?sortdatafield='+sortdatafield+'&sortorder='+sortorder;*/
	    
	   //$.get(sortUrl, function(source){
	    	//$("#jqxSuppliersLookup").jqxGrid('databind', source);
	    	$("#jqxSuppliersLookup").jqxGrid('updatebounddata', 'filter');
	   //});
	});
}

function validateDiamond(){
	var errors = 0;	
	$('.errorText').remove();
	
	if ($('#diamond_type').val() == 0) {
		$('<span class="errorText">Please select a Type</span>').insertAfter('#diamond_type');
		errors++;
	}

	if ($('#shape').val() == 0) {
		$('<span class="errorText">Please select a Shape</span>').insertAfter('#shape');
		errors++;
	}
	
	if ($('#carat').val() == '') {
		$('<span class="errorText">Please enter Carat</span>').insertAfter('#carat');
		errors++;
	}

	if ($('#diamond_type').val() == 1 /* White */ && $('select#white_type').val() == 0) {
		$('<span class="errorText">Please select a Colour</span>').insertAfter('select#white_type');
		errors++;
	}

	if ($('#diamond_type').val() == 2 /* Coloured*/ && $('select#color_type').val() == 0) {
		$('<span class="errorText">Please select a Colour</span>').insertAfter('select#color_type');
		errors++;
	}

	if ($('#clarity').val() == 0) {
		$('<span class="errorText">Please select Clarity</span>').insertAfter('#clarity');
		errors++;
	}

	if ($('#cut').val() == 0) {
		$('<span class="errorText">Please select Cut</span>').insertAfter('#cut');
		errors++;
	}

	if ($('#polish').val() == 0) {
		$('<span class="errorText">Please select Polish</span>').insertAfter('#polish');
		errors++;
	}

	if ($('#symmetry').val() == 0) {
		$('<span class="errorText">Please select Symmetry</span>').insertAfter('#symmetry');
		errors++;
	}

	if ($('#lab').val() == 0) {
		$('<span class="errorText">Please select a Lab</span>').insertAfter('#lab');
		errors++;
	}

	if($("#cert_url").val() != ''){
		if(!isUrlValid($("#cert_url").val())){
			$('<span class="errorText">Please enter valid Cert url</span>').insertAfter('#cert_url');
			errors++;
		}
	}
	
	if($("#video_url").val() != ''){
		if(!isUrlValid($("#video_url").val())){
			$('<span class="errorText">Please enter valid Video url</span>').insertAfter('#video_url');
			errors++;
		}
	}
		
	if ($('#flurosence').val() == 0) {
		$('<span class="errorText">Please select Flurosence</span>').insertAfter('#flurosence');
		errors++;
	}
	
	if ($('#measureD1').val() == '' || $('#measureD2').val() == '' || $('#measureD3').val() == '') {
		$('<span class="errorText">Please enter all Measurements</span>').insertAfter('#measurement');
		errors++;
	}

	return errors;
}

function consignNewDiamond() {
	if (validateDiamond() == 0) {
		openLightBox("consignItem")
		openConsignForm(0, 'diamond');
	}
}

function saveAndConsignDiamond() {
	if (validateDiamond() == 0 && validateConsign($('#frm_consign')) == 0) {
		var url = '/saveandconsigndiamond';
		var data = {
				'frm_diamond': $('#frm_diamond').serialize(),
				'frm_consign': $('#frm_consign').serialize()		
		}
		$.post(url, data, function(response){
			if(response > 0){
				$('#frm_consign')[0].reset();
				$('#frm_diamond')[0].reset();
				$('#consignItem .closePopup').click();
				$('#addItem .closePopup').click();
			}
		});
		
	}
}

function validateConsign(form){
	var errors = 0;	
	$('.errorText').remove();
	if(!$('#frm_consign #accept').attr('checked')){
		$( '<p class="errorText">Please agree to Terms of Consignment</p>' ).insertAfter( '#frm_consign #acceptParent' );
		errors++;
	}
	
	var url = '/validateowner';
	var user_id = $('#frm_consign #owner_id').val();
	var password = md5($('#frm_consign #password').val());	
	if(user_id == 0 || user_id ==''){		
		$( '<span class="errorText" style="white-space: nowrap;">Please select record owner</span>' ).insertAfter($('#frm_consign').find('#jobOwner'));
		
		errors++;
	}
	
	if(errors == 0){
		$.ajax({
			type: 'POST',
			url: url,
			async: false,
			data: {user_id: user_id, password: password},
			success: function(response){
				if(response == 0){
					$( '<p class="errorText">Please provide the correct managers password</p>' ).insertAfter( '#frm_consign #password' );
					errors++;
				}
			}
		});
	}
		
	return errors;
}

function openConsignForm(item_id, type, mode){
	$.ajax({
		type: 'POST',
		url: '/consignform',
		//async: false,
		data: {item_id: item_id, jewel_type:type, mode:mode},
		beforeSend:function(){
			$('#consign_form_content').html('<div class="ajaxLoader"><div class="pageLoader"></div></div>');
		},
		success: function(response){
			$('#consign_form_content').html(response);
			if (item_id == 0) {
				/* This is for a new stone, modify the 'consign' button */
				$('#consign_save').attr('onclick', 'saveAndConsignDiamond()');
				$('#consign_save').html('Save and Consign');
			}
			lightboxmid();
		}
	});
}

function saveConsign(form, type, mode){
	var errors = validateConsign(form);
	//alert(errors);
	if(errors == 0){
		$('#validateConsign').attr('disabled','disabled');
		var url = '/saveconsign';
		var data = $(form).serialize();
		$.post(url, data, function(response){
			if(response > 0){
				$(form)[0].reset();
				$('#consignItem .closePopup').click();
				if(mode == "edit"){
					if(type == "weddingring"){
						getConsignData($('#item_id').val(), 'weddingring');
					} else if(type == "diamond"){
						getConsignData($('#item_id').val(), 'diamond');
					} else if(type == "engagementring"){
						getConsignData($('#item_id').val(), 'engagementring');
					} else if(type == "chains"){
						getConsignData($('#item_id').val(), 'chain');
					} else if(type == "earring"){
						getConsignData($('#item_id').val(), 'earring');
					} else if(type == "pendant"){
						getConsignData($('#item_id').val(), 'pendant');
					} else if(type == "miscellaneous"){
						getConsignData($('#item_id').val(), 'miscellaneous');
					} else if(type == "chain"){
						getConsignData($('#item_id').val(), 'chain');
					} else if(type == "job"){
						getConsignData($('#item_id').val(), 'job');
					}
				} else {
					if(type == "diamond"){
						$('#jqxDianonds').jqxGrid('updatebounddata');
					} else if(type == "weddingring"){
						$('#jqxWeddingring').jqxGrid('updatebounddata');
					} else if(type == "engagementring"){
						$('#jqxEngagementring').jqxGrid('updatebounddata');
					} else if(type == "chains"){
						$('#jqxChains').jqxGrid('updatebounddata');
					} else if(type == "earring"){
						$('#jqxEarring').jqxGrid('updatebounddata');
					} else if(type == "pendant"){
						$('#jqxPendant').jqxGrid('updatebounddata');
					} else if(type == "miscellaneous"){
						$('#jqxMiscellaneous').jqxGrid('updatebounddata');
					} else if(type == "chain"){
						$('#jqxChain').jqxGrid('updatebounddata');
					} else if(type == "job"){
						$('#jqxJobpackets').jqxGrid('updatebounddata');
					}
				}
			}
			$(form)[0].reset();
		});
	}else{
		$('#validateConsign').removeAttr('disabled');
	}
}

function filterDiamondData(){
	var url = '/editgridview';
	var viewId = $('#diamond_selectGridView option:selected').val();
	var data = 'id='+viewId;
	var keyword = $('#diamond_searchInput').val();
	var formData = $('#frm_filter_fiamond').serialize();
	if(viewId){
		$.post(url, data, function(response){
			var setting = JSON.parse(response);
			var columnList = setting.columns_list;
			getDiamonds($('#pageSizeGrabing').val(), columnList, keyword, formData);
		});
	}else{
		getDiamonds($('#pageSizeGrabing').val(), '', keyword, formData);
	}
}

function getConsignData(id, type){
	var url = '/getconsigndetails';
	var data = {'id':id, 'type':type};
	$.post(url, data, function(response){
		var additionalData = JSON.parse(response);
		for (var key in additionalData) {
			if (additionalData.hasOwnProperty(key) && additionalData[key] == null) {
				additionalData[key] = '';
			}
		}
		$('#inventory_status_name_weddring').text(additionalData.inventory_status_name);
		$('#inventory_status_reason_weddring').text(additionalData.inventory_status_reason);
		$('#inventory_type_weddring').text(additionalData.inventory_type);
		$('#owner_name_weddring').text(additionalData.owner_name);
		$('#reserve_time_weddring').text(additionalData.reserve_time);
		$('#reserve_notes_weddring').text(additionalData.reserve_notes);
		$('#inventory_tracking_status_weddring').text(additionalData.inventory_tracking_status);
		$('#inventory_tracking_reason_weddring').text(additionalData.inventory_tracking_reason);
		$('#tracking_id_weddring').text(additionalData.tracking_id);
		/*var date = new Date(
			Date.parse(
				additionalData.reserve_time.replace('-','/','g')
			)
		);*/
		$('#reserve_time_weddring').text(additionalData.reserve_time);
	});
}

function updateDiamond(form){
	if($('#saveDiamondButton').text() === "Edit"){
		$('#saveDiamondButton').text('Save');
		$('#frm_diamond .displayHide').show();
		$('#frm_diamond .hiddenUnqValues').hide();
		splitDiamondMeasure('#measurement');
		
		if($('#diamond_type option:selected').text() == 'Coloured'){
			$("#color_type").show();
			$("#white_type").hide();
		}else{
			$("#color_type").hide();
			$("#white_type").show();
		}
		
	} else if($('#saveDiamondButton').text() === "Save"){
		var errors = validateDiamond();
		if(errors == 0){
			var url = '/savediamond';
			var data = $(form).serialize();
			$.post(url, data, function(response){
				if(response > 0){
					alert('Diamond details updated successfully');
					
					var diamond_type_diamond = $('#diamond_type option:selected').val() > 0 ? $('#diamond_type option:selected').text() : '';
					$('#diamond_type_diamond').text(diamond_type_diamond);
					
					if($('#diamond_type option:selected').text() != "Coloured"){
						$('#intensity_diamond').text('');
						$('#overtone_diamond').text('');
						$('#intensity, #overtone').val(0);
						$("#intensity, #overtone").attr("disabled", "disabled");
						$("#intensity, #overtone").dropkick('refresh');
					} else {
						var intensity_diamond = $('#intensity option:selected').val() > 0 ? $('#intensity option:selected').text() : '';
						$('#intensity_diamond').text(intensity_diamond);
						
						var overtone_diamond = $('#overtone option:selected').val() > 0 ? $('#overtone option:selected').text() : '';
						$('#').text(overtone_diamond);
						
						$("#intensity, #overtone").removeAttr("disabled");
						$("#intensity, #overtone").dropkick('refresh');
					}
					$('#cert_no_diamond').text($('#cert_no').val());
					$('#cert_url_diamond').text($('#cert_url').val());
					var video_link = '<a href="' + $('#video_url').val() + '" target="_blank">' + $('#video_url').val() + '</a>';
					$('#video_url_diamond').html(video_link);
					
					var shape_diamond = $('#shape option:selected').val() > 0 ? $('#shape option:selected').text() : '';
					$('#shape_diamond').text(shape_diamond);
					
					var polish_diamond = $('#polish option:selected').val() > 0 ? $('#polish option:selected').text() : '';
					$('#polish_diamond').text();
					
					var symmetry_diamond = $('#symmetry option:selected').val() > 0 ? $('#symmetry option:selected').text() : '';
					$('#symmetry_diamond').text(symmetry_diamond);
					
					var cut_diamond = $('#cut option:selected').val() > 0 ? $('#cut option:selected').text() : '';
					$('#cut_diamond').text(cut_diamond);
					
					var lab_diamond = $('#lab option:selected').val() > 0 ? $('#lab option:selected').text() : '';
					$('#lab_diamond').text(lab_diamond);
					
					$('#depth_diamond').text($('#depth').val());
					$('#table_diamond').text($('#table').val());
					$('#flurosence_diamond').text($('#flurosence').val());
					$('#measurement_diamond').text($('#measurement').val());
					$('#description_diamond').text($('#description').val());
					
					var image_diamond = $('#image').val() ? '<a href=/inventory_images/'+$('#image').val()+'" target="_blank"><img src="/inventory_images/'+$('#image').val()+'" max-height="90%" max-width="100%"></a>' : '';
					$('#image_diamond').html(image_diamond);
					
					var invoice_diamond = $('#invoice').val() ? '<a href="/invoice/'+$('#invoice').val()+'" target="_blank">Invoice</a>' : '';
					$('#invoice_diamond').html(invoice_diamond);
					
					$('#supplier_name_diamond').text($('#supplier_name').val());
					$('#price_diamond').text($('#price').val());
					$('#saveDiamondButton').text('Edit');
					$('#frm_diamond .displayHide').hide();
					$('#frm_diamond .hiddenUnqValues').show();
					
					var diamondTypeSelection = $("#diamond_type option[value="+$('#diamond_type option:selected').val()+"]").text();
					if(diamondTypeSelection == "Coloured"){
						$('#color_type_text').text($("#color option[value="+$('#color option:selected').val()+"]").text());
						$('#white_type_text').text('');
						$('#white_type_text').hide();
					}else{
						$('#color_type_text').text('');
						$('#color_type_text').hide();
						
						var white_type_text = $('#white_type option:selected').val() > 0 ? $('#white_type option:selected').text() : '';
						$('#white_type_text').text(white_type_text);
					}
					
					//location.reload();
				}
			});
		}
	}
}

function deleteDiamond(id){
	if (confirm("Are you sure you want to delete")) {
        var url = '/deletediamond';
		var data = {'id':id};
		$.post(url, data, function(response){
			if(response == 1){
				window.location.href = '/inventory';
			}
		});
    } else {
		return false;
	}
}

function combineDiamondsMeasure() {
	$('#measurement').val($('#measureD1').val() + ' x ' + $('#measureD2').val() + ' x ' + $('#measureD3').val());
}

function splitDiamondMeasure() {
	$('#measurement').hide();
	$('#measurement').before('<input type="text" id="measureD1" class="inputTxt input30">');
	$('#measurement').before('<input type="text" id="measureD2" class="inputTxt input30">');
	$('#measurement').before('<input type="text" id="measureD3" class="inputTxt input30">');
	
	var vals = $('#measurement').val().split('x');
	if (vals.length == 3) {
		$('#measureD1').val(vals[0].trim());
		$('#measureD2').val(vals[1].trim());
		$('#measureD3').val(vals[2].trim());
	}

	$('#measureD1').change(combineDiamondsMeasure);
	$('#measureD2').change(combineDiamondsMeasure);
	$('#measureD3').change(combineDiamondsMeasure);
}

function sourceCurrencyChange(name) {
	var val = $('#' + name + '_source_ccy').val();
	if (val == 'USD') {
		$('#' + name + '_usd').prop('disabled', false);
		$('#' + name + '_aud').prop('disabled', true);
	} else if (val == 'AUD') {
		$('#' + name + '_usd').prop('disabled', true);
		$('#' + name + '_aud').prop('disabled', false);
	}
}

function sourceCurrencyInit() {
	$('#rapnet_price_source_ccy').dropkick({change:function(){sourceCurrencyChange('rapnet_price');}});
	$('#price_source_ccy').dropkick({change:function(){sourceCurrencyChange('price');}});
	sourceCurrencyChange('rapnet_price');
	sourceCurrencyChange('price');
}

function rapnetFeedChange() {
	var disabled = true;
	if ($('#add_to_rapnet').is(':checked')) { disabled = false; }
	$('#rapnet_pct_discount').prop('disabled', disabled);
	$('#rapnet_cost_per_carat').prop('disabled', disabled);
	$('#rapnet_price_source_ccy').prop('disabled', disabled);
	$('#rapnet_price_source_ccy').dropkick('refresh');
	if (!disabled) {
		sourceCurrencyChange('rapnet_price');
	} else {
		$('#rapnet_price_usd').prop('disabled', disabled);
		$('#rapnet_price_aud').prop('disabled', disabled);
	}
}

function rapnetFeedInit() {
	$('#add_to_rapnet').change(function(){rapnetFeedChange();});
	rapnetFeedChange();
}