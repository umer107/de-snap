function getWeddingrings(recordsPerPage, columnList, keyword){
	$after = $('#jqxWeddingring').prev('#bindAfterThis');
	if ($after.length == 0) return;

	$('#jqxWeddingring').remove();
    $after.after('<div class="formTable manageMembers" id="jqxWeddingring"></div>');

    if(keyword){
		var url = '/ajaxgetweddingrings?keyword='+keyword;
	} else {
		var url = '/ajaxgetweddingrings';
	}
	// prepare the data
	var source =
	{
		datatype: "json",
		datafields: [
			{ name: 'id' },
			{ name: 'stock_code' },
			{ name: 'cad_code' },
			{ name: 'band_width' },
			{ name: 'band_thickness' },
			{ name: 'description' },
			{ name: 'invoice' },
			{ name: 'price' },
			{ name: 'created_date' },
			{ name: 'ring_type' },
			{ name: 'metal_type' },
			{ name: 'profile' },
			{ name: 'finish' },
			{ name: 'fit_option' },
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
			$("#jqxWeddingring").jqxGrid('updatebounddata', 'filter');
		}
	};
	
	var pageable = true, sortable = true;
	var dataAdapter = new $.jqx.dataAdapter(source, {
		downloadComplete: function (data, status, xhr) {},
		loadComplete: function (data) { 
			if($("#jqxWeddingring").find('.jqx-grid-empty-cell').length>0 ){					
					if($("#jqxWeddingring").find('.jqx-grid-empty-cell').parent('div').height()<30){
						$('.jqx-grid-empty-cell').parent('div').addClass('norecordsFoundRow');
						$('.jqx-grid-empty-cell').closest("#jqxWeddingring").addClass('noInfoFound');
					}
					if(keyword){
						$("#jqxWeddingring").find('.jqx-grid-empty-cell >span').text("No matches were found");
						//$('#searchResultsHint').text("No matches were found");
					} else {
						$("#jqxWeddingring").find('.jqx-grid-empty-cell >span').text("No records found");
					}
			}else{
				if($("#jqxWeddingring").hasClass('noInfoFound')){
					$("#jqxWeddingring").removeClass('noInfoFound');
				}
			}
		},
		loadError: function (xhr, status, error) { }
	});
	
	var consign = function(row, columnfield, value, defaulthtml, columnproperties){
		var datarow = $('#jqxWeddingring').jqxGrid('getrowdata', row);
		var html = '';
		if(datarow)
			var html = '<a href="javascript:;" data-popup="consignItem" class="cmnBtn lightBoxClick" onclick="openConsignForm('+datarow.id+', \'weddingring\')">Consign</a>';
		return html;
	};

	if(columnList == '' || columnList == undefined){
		/* Get from hidden input if it's there */
		if ($('#weddingring_columnList').length) {
			columnList = $('#weddingring_columnList').val();
		} else {
			/* Default columns */
			columnList = 'consign_button,inventory_status_name,inventory_status_reason,stock_code,ring_type,cad_code,metal_type,price,supplier_name,inventory_type,inventory_tracking_status,inventory_tracking_reason,reserve_time,reserve_notes';
		}
	}
	$('#weddingring_columnList').val(columnList);

	var colList = columnList.split(',');
	var gridColumnList = new Array();
	if(colList.length>6){
		for(i = 0; i < colList.length; i++){
			if(colList[i] == 'consign_button'){
				gridColumnList.push({ text: 'Consignment', type: 'string', cellsrenderer: consign, width:'10%'});

			}
			if(colList[i] == 'inventory_status_name'){
				gridColumnList.push({ text: 'Inventory <br /> Status', type: 'string', datafield: 'inventory_status_name', width:'10%'});
			}
			if(colList[i] == 'inventory_status_reason'){
				gridColumnList.push({ text: 'Reason', type: 'string', datafield: 'inventory_status_reason', width:'12%'});
			}
			if(colList[i] == 'reserve_time'){
				gridColumnList.push({ text: 'Reserve <br /> Time', type: 'string', datafield: 'reserve_time', width:'10%'});
			}
			if(colList[i] == 'reserve_notes'){
				gridColumnList.push({ text: 'Reserve Note', type: 'string', datafield: 'reserve_notes', width:'15%'});
			}
			if(colList[i] == 'inventory_type'){
				gridColumnList.push({ text: 'Inventory <br /> Type', type: 'string', datafield: 'inventory_type', width:'10%'});
			}
			if(colList[i] == 'inventory_tracking_status'){
				gridColumnList.push({ text: 'Tracking', type: 'string', datafield: 'inventory_tracking_status', width:'10%'});
			}
			if(colList[i] == 'inventory_tracking_reason'){
				gridColumnList.push({ text: 'Tracking Reason', type: 'string', datafield: 'inventory_tracking_reason', width:'15%'});
			}
			if(colList[i] == 'tracking_id'){
				gridColumnList.push({ text: 'Tracking ID', type: 'string', datafield: 'tracking_id', width:'12%'});
			}
			if(colList[i] == 'stock_code'){
				gridColumnList.push({ text: 'Stock Code', type: 'string', datafield: 'stock_code',  width:'10%'});
			}
			if(colList[i] == 'supplier_name'){
				gridColumnList.push({ text: 'Supplier <br /> Name', type: 'string', datafield: 'supplier_name', width:'10%'});
			}
			if(colList[i] == 'ring_type'){
				gridColumnList.push({ text: 'Ring Type', type: 'string', datafield: 'ring_type', width:'11%'});
			}
			if(colList[i] == 'cad_code'){
				gridColumnList.push({ text: 'CAD<br /> Code', type: 'string', datafield: 'cad_code', width:'7%'});
			}
			if(colList[i] == 'metal_type'){
				gridColumnList.push({ text: 'Metal <br /> Type', type: 'string', datafield: 'metal_type', width:'8%'});
			}
			if(colList[i] == 'profile'){
				gridColumnList.push({ text: 'Profile', type: 'string', datafield: 'profile', width:'10%'});
			}
			if(colList[i] == 'band_width'){
				gridColumnList.push({ text: 'Band <br /> Width', type: 'string', datafield: 'band_width', width:'8%'});
			}
			if(colList[i] == 'band_thickness'){
				gridColumnList.push({ text: 'Band <br /> Thickness', type: 'string', datafield: 'band_thickness', width:'10%'});
			}
			if(colList[i] == 'finish'){
				gridColumnList.push({ text: 'Finish', type: 'string', datafield: 'finish', width:'8%'});
			}
			if(colList[i] == 'fit_option'){
				gridColumnList.push({ text: 'Fit Option', type: 'string', datafield: 'fit_option', width:'10%'});
			}
			if(colList[i] == 'price'){
				gridColumnList.push({ text: 'Price', type: 'string', datafield: 'price', width:'8%'});
			}
			if(colList[i] == 'owner_name'){
				gridColumnList.push({ text: 'Record Owner', type: 'string', datafield: 'owner_name', width:'13%'});
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
			if(colList[i] == 'ring_type'){
				gridColumnList.push({ text: 'Ring Type', type: 'string', datafield: 'ring_type', width:colWidthData+'%'});
			}
			if(colList[i] == 'cad_code'){
				gridColumnList.push({ text: 'CAD Code', type: 'string', datafield: 'cad_code', width:colWidthData+'%'});
			}
			if(colList[i] == 'metal_type'){
				gridColumnList.push({ text: 'Metal Type', type: 'string', datafield: 'metal_type', width:colWidthData+'%'});
			}
			if(colList[i] == 'profile'){
				gridColumnList.push({ text: 'Profile', type: 'string', datafield: 'profile', width:colWidthData+'%'});
			}
			if(colList[i] == 'band_width'){
				gridColumnList.push({ text: 'Band Width', type: 'string', datafield: 'band_width', width:colWidthData+'%'});
			}
			if(colList[i] == 'band_thickness'){
				gridColumnList.push({ text: 'Band Thickness', type: 'string', datafield: 'band_thickness', width:colWidthData+'%'});
			}
			if(colList[i] == 'finish'){
				gridColumnList.push({ text: 'Finish', type: 'string', datafield: 'finish', width:colWidthData+'%'});
			}
			if(colList[i] == 'fit_option'){
				gridColumnList.push({ text: 'Fit Option', type: 'string', datafield: 'fit_option', width:colWidthData+'%'});
			}
			if(colList[i] == 'price'){
				gridColumnList.push({ text: 'Price', type: 'string', datafield: 'price', width:colWidthData+'%'});
			}
			if(colList[i] == 'owner_name'){
				gridColumnList.push({ text: 'Record Owner', type: 'string', datafield: 'owner_name', width:colWidthData+'%'});
			}
		}
	}

	$("#jqxWeddingring").jqxGrid(
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

	
	/* See if we have an override cellclick binding */
	if ($('#weddingring_cellClick').length) {
		$('#jqxWeddingring').bind('cellclick', window[$('#weddingring_cellClick').val()]);
	} else {
		$('#jqxWeddingring').bind('cellclick', function(event)  {
			var current_index = event.args.rowindex;
			var current_column = event.args.column.datafield;
			var datarow = $('#jqxWeddingring').jqxGrid('getrowdata', current_index);
			var url = '/weddingringdetails/'+datarow.id;
			//alert(current_column);
			if(current_column != null){
				$(location).attr('href', url);
			}
			$('#gridTypeId').val(datarow.id);
			$('#gridType').val('weddingring');
		});
	}
	
	$("#jqxWeddingring").bind("sort", function (event) {
    	$("#jqxWeddingring").jqxGrid('updatebounddata', 'filter');
	});
	
	$("#jqxWeddingring").bind("pagesizechanged", function (event) {
		var args = event.args;
		var pagenumber = args.pagenum;
		var pagesize = args.pagesize;
		$('#pageSizeGrabing').val(pagesize);
	});
}

function validateWeddingring(){
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

function saveWeddingring(form){
		var errors = validateWeddingring();
		if(errors == 0){
			//$('#weddingring_save').attr('disabled','disabled');
			var url = '/saveweddingring';
			var data = $(form).serialize();
			$.post(url, data, function(response){
				if(response > 0){
					$(form)[0].reset();
					$('#addItem .closePopup').click();
					getCount('inventory');
					$('#jqxWeddingring').jqxGrid('updatebounddata');
				}
			});
		}else{
			//$('#weddingring_save').removeAttr('disabled');
		}
}

function updateWeddingring(form){
	if($('#saveWeddingRingButton').text() === "Edit"){
		$('#saveWeddingRingButton').text('Save');
		$('#frm_weddingring .displayHide').show();
		$('#frm_weddingring .hiddenUnqValues').hide();
	} else if($('#saveWeddingRingButton').text() === "Save"){
		var errors = validateUpdateSupplier();
		if(errors == 0){
			var url = '/saveweddingring';
			var data = $(form).serialize();
			$.post(url, data, function(response){
				if(response > 0){
					alert('Wedding Ring details updated successfully');
					var ring_type_weddring = $('#ring_type option:selected').val() > 0  ? $('#ring_type option:selected').text() : '';
					$('#ring_type_weddring').text(ring_type_weddring);
					
					$('#cad_code_weddring').text($('#cad_code').val());
					
					var metal_type_weddring = $('#metal_type option:selected').val() > 0  ? $('#metal_type option:selected').text() : '';
					$('#metal_type_weddring').text(metal_type_weddring);
					
					var profile_weddring = $('#profile option:selected').val() > 0  ? $('#profile option:selected').text() : '';
					$('#profile_weddring').text(profile_weddring);
					
					$('#band_width_weddring').text($('#band_width').val());
					$('#band_thickness_weddring').text($('#band_thickness').val());
					
					var finish_weddring = $('#finish option:selected').val() > 0  ? $('#finish option:selected').text() : '';
					$('#finish_weddring').text(finish_weddring);
					
					var fit_options_weddring = $('#fit_options option:selected').val() > 0  ? $('#fit_options option:selected').text() : '';
					$('#fit_options_weddring').text(fit_options_weddring);
					
					var image_weddring = $('#image').val() ? '<img src="/inventory_images/'+$('#image').val()+'" height="98px" width="234px">' : '';
					$('#image_weddring').html(image_weddring);
					
					var invoice_weddring = $('#invoice').val() ? '<a href="/invoice/'+$('#invoice').val()+'" target="_blank">Invoice</a>' : '';
					$('#invoice_weddring').html(invoice_weddring);
					$('#description_weddring').text($('#description').val());
					$('#supplier_name_weddring').text($('#supplier_name').val());
					$('#price_weddring').text($('#price').val());
					getAdditionalList($('#weddingRingId').val(), 'weddingring');
					$('#saveWeddingRingButton').text('Edit');
					$('#frm_weddingring .displayHide').hide();
					$('#frm_weddingring .hiddenUnqValues').show();
					//location.reload();
				}
			});
		}
	}
}

function getAdditionalList(id, type){
	var url = '/getadditionallist';
	var data = {'id':id, 'type':type};
	$.post(url, data, function(response){
		var additionalData = JSON.parse(response);
		$('.hideTableStaticData').remove();
		$.each( additionalData, function( key, value ) {
		  var setting_style = value.setting_style != null ? value.setting_style : '';
		  var shape = value.shape != null ? value.shape : '';
		  var gem_type = value.gem_type != null ? value.gem_type : '';
		  var setting_style = value.setting_style != null ? value.setting_style : '';
		  var qty = value.qty != null ? value.qty : '';
		  var size = value.size != null ? value.size : '';
		  var total_carat = value.total_carat != null ? value.total_carat : '';
		  $("#tbodyAdditional").append('<tr id="staticData'+key+'" class="hiddenUnqValues hideTableStaticData"><td>'+setting_style+'</td><td>'+shape+'</td><td>'+gem_type+'</td><td>'+qty+'</td><td>'+size+'</td><td>'+total_carat+'</td></tr>');
		});
	});
}

function deleteWeddingRing(id){
	if (confirm("Are you sure you want to delete")) {
        var url = '/deleteweddingring';
		var data = {'id':id};
		$.post(url, data, function(response){
			if(response == 1){
				window.location.href = '/inventory-weddingring';
			}
		});
    } else {
		return false;
	}
}