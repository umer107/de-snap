function getEngagementrings(recordsPerPage, columnList, keyword){
	$after = $('#jqxEngagementring').prev('#bindAfterThis');
	if ($after.length == 0) return;

	$('#jqxEngagementring').remove();
    $after.after('<div class="formTable manageMembers" id="jqxEngagementring"></div>');

	if(keyword){
		var url = '/ajaxgetengagementrings?keyword='+keyword;
	} else {
		var url = '/ajaxgetengagementrings';
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
			{ name: 'halo_width' },
			{ name: 'halo_thickness' },
			{ name: 'head_title' },
			{ name: 'claw_title' },
			{ name: 'setting_height' },
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
			$("#jqxEngagementring").jqxGrid('updatebounddata', 'filter');
		}
	};
	
	var pageable = true, sortable = true;
	var dataAdapter = new $.jqx.dataAdapter(source, {
		downloadComplete: function (data, status, xhr) {},
		loadComplete: function (data) { 
			if($("#jqxEngagementring").find('.jqx-grid-empty-cell').length>0 ){					
					if($("#jqxEngagementring").find('.jqx-grid-empty-cell').parent('div').height()<30){
						$('.jqx-grid-empty-cell').parent('div').addClass('norecordsFoundRow');
						$('.jqx-grid-empty-cell').closest("#jqxEngagementring").addClass('noInfoFound');
					}
					if(keyword){
						$("#jqxEngagementring").find('.jqx-grid-empty-cell >span').text("No matches were found");
						//$('#searchResultsHint').text("No matches were found");
					} else {
						$("#jqxEngagementring").find('.jqx-grid-empty-cell >span').text("No records found");
					}
			}else{
				if($("#jqxEngagementring").hasClass('noInfoFound')){
					$("#jqxEngagementring").removeClass('noInfoFound');
				}
			}
		},
		loadError: function (xhr, status, error) { }
	});
	
	var consign = function(row, columnfield, value, defaulthtml, columnproperties){
		var datarow = $('#jqxEngagementring').jqxGrid('getrowdata', row);
		var html = '';
		if(datarow)
			html = '<a href="javascript:;" data-popup="consignItem" class="cmnBtn lightBoxClick" onclick="openConsignForm('+datarow.id+', \'engagementring\')">Consign</a>';
		return html;
	};

	if(columnList == '' || columnList == undefined){
		/* Get from hidden input if it's there */
		if ($('#engagementring_columnList').length) {
			columnList = $('#engagementring_columnList').val();
		} else {
			/* Default columns */
			columnList = 'consign_button,inventory_status_name,inventory_status_reason,stock_code,ring_type,cad_code,metal_type,price,supplier_name,inventory_type,inventory_tracking_status,inventory_tracking_reason,reserve_time,reserve_notes';
		}
	}
	$('#engagementring_columnList').val(columnList);
	
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
			if(colList[i] == 'ring_type'){
				gridColumnList.push({ text: 'Ring Type', type: 'string', datafield: 'ring_type', width:'10%' });
			}
			if(colList[i] == 'cad_code'){
				gridColumnList.push({ text: 'CAD Code', type: 'string', datafield: 'cad_code', width:'10%' });
			}
			if(colList[i] == 'metal_type'){
				gridColumnList.push({ text: 'Metal Type', type: 'string', datafield: 'metal_type', width:'10%' });
			}
			if(colList[i] == 'profile'){
				gridColumnList.push({ text: 'Profile', type: 'string', datafield: 'profile', width:'10%' });
			}
			if(colList[i] == 'band_width'){
				gridColumnList.push({ text: 'Band Width', type: 'string', datafield: 'band_width', width:'10%' });
			}
			if(colList[i] == 'band_thickness'){
				gridColumnList.push({ text: 'Band Thickness', type: 'string', datafield: 'band_thickness', width:'10%' });
			}
			if(colList[i] == 'halo_width'){
				gridColumnList.push({ text: 'Halo Width', type: 'string', datafield: 'halo_width', width:'10%' });
			}
			if(colList[i] == 'halo_thickness'){
				gridColumnList.push({ text: 'Halo Thickness', type: 'string', datafield: 'halo_thickness', width:'10%' });
			}
			if(colList[i] == 'head_title'){
				gridColumnList.push({ text: 'Head Settings', type: 'string', datafield: 'head_title', width:'10%' });
			}
			if(colList[i] == 'claw_title'){
				gridColumnList.push({ text: 'Claw Termination', type: 'string', datafield: 'claw_title', width:'10%' });
			}
			if(colList[i] == 'setting_height'){
				gridColumnList.push({ text: 'Setting Height', type: 'string', datafield: 'setting_height', width:'10%' });
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
			if(colList[i] == 'halo_width'){
				gridColumnList.push({ text: 'Halo Width', type: 'string', datafield: 'halo_width', width:colWidthData+'%'});
			}
			if(colList[i] == 'halo_thickness'){
				gridColumnList.push({ text: 'Halo Thickness', type: 'string', datafield: 'halo_thickness', width:colWidthData+'%'});
			}
			if(colList[i] == 'head_title'){
				gridColumnList.push({ text: 'Head Settings', type: 'string', datafield: 'head_title', width:colWidthData+'%'});
			}
			if(colList[i] == 'claw_title'){
				gridColumnList.push({ text: 'Claw Termination', type: 'string', datafield: 'claw_title', width:colWidthData+'%'});
			}
			if(colList[i] == 'setting_height'){
				gridColumnList.push({ text: 'Setting Height', type: 'string', datafield: 'setting_height', width:colWidthData+'%'});
			}
			if(colList[i] == 'price'){
				gridColumnList.push({ text: 'AUD RRP', type: 'string', datafield: 'price', width:colWidthData+'%'});
			}
			if(colList[i] == 'owner_name'){
				gridColumnList.push({ text: 'Record Owner', type: 'string', datafield: 'owner_name', width:colWidthData+'%'});
			}
		}
	}

	$("#jqxEngagementring").jqxGrid(
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
		columns: gridColumnList,		
	});

	/* See if we have an override cellclick binding */
	if ($('#diamond_cellClick').length) {
		$('#jqxEngagementring').bind('cellclick', window[$('#engagementring_cellClick').val()]);
	} else {
		$('#jqxEngagementring').bind('cellclick', function(event)  {
			var current_index = event.args.rowindex;
			var current_column = event.args.column.datafield;
			var datarow = $('#jqxEngagementring').jqxGrid('getrowdata', current_index);
			var url = '/engagementringdetails/'+datarow.id;
			//alert(current_column);
			if(current_column != null){
				$(location).attr('href', url);
			}
			$('#gridTypeId').val(datarow.id);
			$('#gridType').val('engagementring');
		});
	}
	
	$("#jqxEngagementring").bind("sort", function (event) {
		$("#jqxEngagementring").jqxGrid('updatebounddata', 'filter');
	});
	
	$("#jqxEngagementring").bind("pagesizechanged", function (event) {
		var args = event.args;
		var pagenumber = args.pagenum;
		var pagesize = args.pagesize;
		$('#pageSizeGrabing').val(pagesize);
	});
}

function validateEngagementring(){
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

function saveEngagementring(form){
		var errors = validateEngagementring();
		if(errors == 0){
			//$('#engagementring_save').attr('disabled','disabled');
			var url = '/saveengagementring';
			var data = $(form).serialize();
			$.post(url, data, function(response){
				if(response > 0){
					$(form)[0].reset();
					$('#addItem .closePopup').click();
					getCount('inventory');
					$('#jqxEngagementring').jqxGrid('updatebounddata');
				}
			});
		}else{
			//$('#engagementring_save').removeAttr('disabled');
		}
}

function updateEngagementring(form){
	if($('#saveEngagementRingButton').text() === "Edit"){
		$('#saveEngagementRingButton').text('Save');
		$('#frm_engagementring .displayHide').show();
		$('#frm_engagementring .hiddenUnqValues').hide();
	} else if($('#saveEngagementRingButton').text() === "Save"){
		var errors = validateUpdateSupplier();
		if(errors == 0){
			var url = '/saveengagementring';
			var data = $(form).serialize();
			$.post(url, data, function(response){
				if(response > 0){
					alert('Engagement Ring details updated successfully');
					
					var ring_type_engagering = $('#ring_type option:selected').val() > 0 ? $('#ring_type option:selected').text() : '';
					$('#ring_type_engagering').text(ring_type_engagering);
					
					$('#cad_code_engagering').text($('#cad_code').val());
					
					var metal_type_engagering = $('#metal_type option:selected').val() > 0  ? $('#metal_type option:selected').text() : '';
					$('#metal_type_engagering').text(metal_type_engagering);
					
					var profile_engagering = $('#profile option:selected').val() > 0 ? $('#profile option:selected').text() : '';
					$('#profile_engagering').text();
					
					$('#band_width_engagering').text($('#band_width').val());
					$('#band_thickness_engagering').text($('#band_thickness').val());
					$('#halo_width_engagering').text($('#halo_width').val());
					$('#halo_thickness_engagering').text($('#halo_thickness').val());
					
					var image_engagering = $('#image').val() ? '<img src="/inventory_images/'+$('#image').val()+'" height="98px" width="234px">' : '';
					$('#image_engagering').html(image_engagering);
					
					$('#description_engagering').text($('#description').val());
					
					var head_settings_engagering = $('#head_settings option:selected').val() > 0 ? $('#head_settings option:selected').text() : '';
					$('#head_settings_engagering').text(head_settings_engagering);
					
					var claw_termination_engagering = $('#claw_termination option:selected').val() > 0 ? $('#claw_termination option:selected').text() : '';
					$('#claw_termination_engagering').text(claw_termination_engagering);
					
					$('#setting_height_engagering').text($('#setting_height').val());
					$('#supplier_name_engagering').text($('#supplier_name').val());
					$('#price_engagering').text($('#price').val());
					getAdditionalList($('#engagementRingId').val(), 'engagementring');
					$('#saveEngagementRingButton').text('Edit');
					$('#frm_engagementring .displayHide').hide();
					$('#frm_engagementring .hiddenUnqValues').show();
					//location.reload();
				}
			});
		}
	}
}

function deleteEngagementRing(id){
	if (confirm("Are you sure you want to delete")) {
        var url = '/deleteengagementring';
		var data = {'id':id};
		$.post(url, data, function(response){
			if(response == 1){
				window.location.href = '/inventory-engagementring';
			}
		});
    } else {
		return false;
	}
}