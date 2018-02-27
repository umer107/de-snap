function getEarrings(recordsPerPage, columnList, keyword){
	$after = $('#jqxEarring').prev('#bindAfterThis');
	if ($after.length == 0) return;

	if(keyword){
		var url = '/ajaxgetearrings?keyword='+keyword;
	} else {
		var url = '/ajaxgetearrings';
	}
	
	$('#jqxEarring').remove();
    $after.after('<div class="formTable manageMembers" id="jqxEarring"></div>');

    // prepare the data
	var source =
	{
		datatype: "json",
		datafields: [
			{ name: 'id' },
			{ name: 'stock_code' },
			{ name: 'cad_code' },
			{ name: 'description' },
			{ name: 'invoice' },
			{ name: 'price' },
			{ name: 'created_date' },
			{ name: 'ring_style' },
			{ name: 'metal_type' },
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
			$("#jqxEarring").jqxGrid('updatebounddata', 'filter');
		}
	};	
	
	var pageable = true, sortable = true;
	var dataAdapter = new $.jqx.dataAdapter(source, {
		downloadComplete: function (data, status, xhr) {},
		loadComplete: function (data) { 
			if($("#jqxEarring").find('.jqx-grid-empty-cell').length>0 ){					
					if($("#jqxEarring").find('.jqx-grid-empty-cell').parent('div').height()<30){
						$('.jqx-grid-empty-cell').parent('div').addClass('norecordsFoundRow');
						$('.jqx-grid-empty-cell').closest("#jqxEarring").addClass('noInfoFound');
					}
					if(keyword){
						$("#jqxEarring").find('.jqx-grid-empty-cell >span').text("No matches were found");
						//$('#searchResultsHint').text("No matches were found");
					} else {
						$("#jqxEarring").find('.jqx-grid-empty-cell >span').text("No records found");
					}
			}else{
				if($("#jqxEarring").hasClass('noInfoFound')){
					$("#jqxEarring").removeClass('noInfoFound');
				}
			}
		},
		loadError: function (xhr, status, error) { }
	});
	
	var consign = function(row, columnfield, value, defaulthtml, columnproperties){
		var datarow = $('#jqxEarring').jqxGrid('getrowdata', row);
		var html = '';
		if(datarow)
			html = '<a href="javascript:;" data-popup="consignItem" class="cmnBtn lightBoxClick" onclick="openConsignForm('+datarow.id+', \'earring\')">Consign</a>';
		return html;
	};

	if(columnList == '' || columnList == undefined){
		/* Get from hidden input if it's there */
		if ($('#earring_columnList').length) {
			columnList = $('#earring_columnList').val();
		} else {
			/* Default columns */
			columnList = 'consign_button,inventory_status_name,inventory_status_reason,stock_code,ring_style,cad_code,metal_type,price,supplier_name,inventory_type,inventory_tracking_status,inventory_tracking_reason,reserve_time,reserve_notes';
		}
	}
	$('#earring_columnList').val(columnList);
	
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
			if(colList[i] == 'ring_style'){
				gridColumnList.push({ text: 'Style', type: 'string', datafield: 'ring_style', width:'10%' });
			}
			if(colList[i] == 'cad_code'){
				gridColumnList.push({ text: 'CAD Code', type: 'string', datafield: 'cad_code', width:'10%' });
			}
			if(colList[i] == 'metal_type'){
				gridColumnList.push({ text: 'Metal Type', type: 'string', datafield: 'metal_type', width:'10%' });
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
			if(colList[i] == 'ring_style'){
				gridColumnList.push({ text: 'Style', type: 'string', datafield: 'ring_style', width:colWidthData+'%'});
			}
			if(colList[i] == 'cad_code'){
				gridColumnList.push({ text: 'CAD Code', type: 'string', datafield: 'cad_code', width:colWidthData+'%'});
			}
			if(colList[i] == 'metal_type'){
				gridColumnList.push({ text: 'Metal Type', type: 'string', datafield: 'metal_type', width:colWidthData+'%'});
			}
			if(colList[i] == 'price'){
				gridColumnList.push({ text: 'AUD RRP', type: 'string', datafield: 'price', width:colWidthData+'%'});
			}
			if(colList[i] == 'owner_name'){
				gridColumnList.push({ text: 'Record Owner', type: 'string', datafield: 'owner_name', width:colWidthData+'%'});
			}
		}
	}

	$("#jqxEarring").jqxGrid(
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
	if ($('#earring_cellClick').length) {
		$('#jqxEarring').bind('cellclick', window[$('#earring_cellClick').val()]);
	} else {
		$('#jqxEarring').bind('cellclick', function(event)  {
			var current_index = event.args.rowindex;
			var current_column = event.args.column.datafield;
			var datarow = $('#jqxEarring').jqxGrid('getrowdata', current_index);
			var url = '/earringdetails/'+datarow.id;
			//alert(current_column);
			if(current_column != null){
				$(location).attr('href', url);
			}
			$('#gridTypeId').val(datarow.id);
			$('#gridType').val('earring');
		});
	}
	
	$("#jqxEarring").bind("sort", function (event) {
		$("#jqxEarring").jqxGrid('updatebounddata', 'filter');
	});
	
	$("#jqxEarring").bind("pagesizechanged", function (event) {
		var args = event.args;
		var pagenumber = args.pagenum;
		var pagesize = args.pagesize;
		$('#pageSizeGrabing').val(pagesize);
	});
}

function validateEarring(){
	return 0;
}

function validateUpdateSupplier(){
	return 0;
}

function saveEarring(form){
		var errors = validateEarring();
		if(errors == 0){
			//$('#earring_save').attr('disabled','disabled');
			var url = '/saveearring';
			var data = $(form).serialize();
			$.post(url, data, function(response){
				if(response > 0){
					$(form)[0].reset();
					$('#addItem .closePopup').click();
					getCount('inventory');
					$('#jqxEarring').jqxGrid('updatebounddata');
				}
			});
		}else{
			//$('#earring_save').removeAttr('disabled');
		}
}

function updateEarring(form){
	if($('#saveEarRingButton').text() === "Edit"){
		$('#saveEarRingButton').text('Save');
		$('#frm_earring .displayHide').show();
		$('#frm_earring .hiddenUnqValues').hide();
	} else if($('#saveEarRingButton').text() === "Save"){
		var errors = validateUpdateSupplier();
		if(errors == 0){
			var url = '/saveearring';
			var data = $(form).serialize();
			$.post(url, data, function(response){
				if(response > 0){
					alert('Ear Ring details updated successfully');
					
					var ring_style_earring = $('#ring_style option:selected').val() > 0 ? $('#ring_style option:selected').text() : '';
					$('#ring_style_earring').text(ring_style_earring);
					
					if($("#ring_style option[value="+$('#ring_style option:selected').val()+"]").text() == "Other"){
						$('#other_ring_style_earring').text($('#other_ring_style').val());
						$('#other_ring_style_div').show();
					} else {
						$('#other_ring_style').val('');
						$('#other_ring_style_earring').text('');
						$('#other_ring_style_div').hide();
					}
					$('#cad_code_earring').text($('#cad_code').val());
					
					var metal_type_earring = $('#metal_type option:selected').val() > 0 ? $('#metal_type option:selected').text() : '';
					$('#metal_type_earring').text(metal_type_earring);
					
					var profile_earring = $('#profile option:selected').val() > 0 ? $('#profile option:selected').text() : '';
					$('#profile_earring').text(profile_earring);
					
					$('#metal_weight_earring').text($('#metal_weight').val());
					$('#band_width_earring').text($('#band_width').val());
					$('#band_thickness_earring').text($('#band_thickness').val());
					
					var image_earring = $('#image').val() ? '<img src="/inventory_images/'+$('#image').val()+'" height="98px" width="234px">' : '';
					$('#image_earring').html(image_earring);
					
					$('#description_earring').text($('#description').val());
					$('#supplier_name_earring').text($('#supplier_name').val());
					$('#price_earring').text($('#price').val());
					getAdditionalList($('#earRingId').val(), 'earring');
					$('#saveEarRingButton').text('Edit');
					$('#frm_earring .displayHide').hide();
					$('#frm_earring .hiddenUnqValues').show();
					//location.reload();
				}
			});
		}
	}
}

function deleteEarRing(id){
	if (confirm("Are you sure you want to delete")) {
        var url = '/deleteearring';
		var data = {'id':id};
		$.post(url, data, function(response){
			if(response == 1){
				window.location.href = '/inventory-earring';
			}
		});
    } else {
		return false;
	}
}