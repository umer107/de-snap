function getSuppliers(recordsPerPage, columnList, keyword, searchParams){
	$('#searchResultsHint').text('');
	if(keyword && !searchParams){
		var url = '/ajaxgetsuppliers?keyword='+keyword;
	} else if(!keyword && searchParams){
		var url = '/ajaxgetsuppliers?'+searchParams;
	}else if(keyword && searchParams){
		var url = '/ajaxgetsuppliers?keyword='+keyword+'&'+searchParams;
	}else if(!keyword && !searchParams){
		var url = '/ajaxgetsuppliers';
	}
	
	$('#jqxSuppliers').remove();
    $('#bindAfterThis').after('<div class="formTable manageMembers" id="jqxSuppliers"></div>');

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
			$("#jqxSuppliers").jqxGrid('updatebounddata', 'filter');
		}
	};
	
	var pageable = true, sortable = true;
	var dataAdapter = new $.jqx.dataAdapter(source, {
		downloadComplete: function (data, status, xhr) { },
		loadComplete: function (data) { 
			if($("#jqxSuppliers").find('.jqx-grid-empty-cell').length>0){
					if($("#jqxSuppliers").find('.jqx-grid-empty-cell').parent('div').height()<30){
						$('.jqx-grid-empty-cell').parent('div').addClass('norecordsFoundRow');
						$('.jqx-grid-empty-cell').closest("#jqxSuppliers").addClass('noInfoFound');
					}
					if(keyword){
						$("#jqxSuppliers").find('.jqx-grid-empty-cell >span').text("No matches were found");
						//$('#searchResultsHint').text("No matches were found");
					} else {
						$("#jqxSuppliers").find('.jqx-grid-empty-cell >span').text("No records found");
					}
			}else{
				if($("#jqxSuppliers").hasClass('noInfoFound')){
					$("#jqxSuppliers").removeClass('noInfoFound');
				}
			}
		},
		loadError: function (xhr, status, error) { }
	});
	
	if(columnList == '' || columnList == undefined){
		/* Default columns */
		columnList = 'created_date,company_name,first_name,last_name,email,phone,mobile,service_name';
	}

	var colList = columnList.split(',');
	var gridColumnList = new Array();
	if(colList.length>6){
		for(i = 0; i < colList.length; i++){
			if(colList[i] == 'created_date'){
				gridColumnList.push({ text: 'Date<br>Created', type: 'string', datafield: 'created_date',  width:'10%'});
			}
			if(colList[i] == 'company_name'){
				gridColumnList.push({ text: 'Company<br>Name', type: 'string', datafield: 'company_name', width:'12%'});
			}
			if(colList[i] == 'first_name'){
				gridColumnList.push({ text: 'First Name', type: 'string', datafield: 'first_name',  width:'13%'});
			}
			if(colList[i] == 'last_name'){
				gridColumnList.push({ text: 'Last Name', type: 'string', datafield: 'last_name', width:'13%' });
			}
			if(colList[i] == 'email'){
				gridColumnList.push({ text: 'Email', type: 'string', datafield: 'email', width:'17%'});
			}
			if(colList[i] == 'phone'){
				gridColumnList.push({ text: 'Number', type: 'string', datafield: 'phone', width:'11%'});
			}
			if(colList[i] == 'mobile'){
				gridColumnList.push({ text: 'Mobile No.', type: 'string', datafield: 'mobile', width:'10%'});
			}
			if(colList[i] == 'service_name'){
				gridColumnList.push({ text: 'Services<br>Offered', type: 'string', datafield: 'service_name', width:'25%'});
			}
		}
	}else{
		var countData = colList.length;
		var colWidthData = 100/countData;
				
		for(i = 0; i < colList.length; i++){
			if(colList[i] == 'created_date'){
				gridColumnList.push({ text: 'Date<br>Created', type: 'string', datafield: 'created_date', width:colWidthData+'%'});
			}
			if(colList[i] == 'company_name'){
				gridColumnList.push({ text: 'Company<br>Name', type: 'string', datafield: 'company_name', width:colWidthData+'%'});
			}
			if(colList[i] == 'first_name'){
				gridColumnList.push({ text: 'First Name', type: 'string', datafield: 'first_name', width:colWidthData+'%'});
			}
			if(colList[i] == 'last_name'){
				gridColumnList.push({ text: 'Last Name', type: 'string', datafield: 'last_name', width:colWidthData+'%'});
			}
			if(colList[i] == 'email'){
				gridColumnList.push({ text: 'Email', type: 'string', datafield: 'email', width:colWidthData+'%'});
			}
			if(colList[i] == 'phone'){
				gridColumnList.push({ text: 'Number', type: 'string', datafield: 'phone', width:colWidthData+'%'});
			}
			if(colList[i] == 'mobile'){
				gridColumnList.push({ text: 'Mobile No.', type: 'string', datafield: 'mobile', width:colWidthData+'%'});
			}
			if(colList[i] == 'service_name'){
				gridColumnList.push({ text: 'Services<br>Offered', type: 'string', datafield: 'service_name', width:colWidthData+'%'});
			}
		}
	}
		
	$("#jqxSuppliers").jqxGrid(
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

	$('#jqxSuppliers').bind('cellclick', function(event)  {
		var current_index = event.args.rowindex;
		var current_column = event.args.column.datafield;
		var datarow = $('#jqxSuppliers').jqxGrid('getrowdata', current_index);
		var url = '/supplierdetails/'+datarow.id;
		$(location).attr('href', url);
		/*if(current_column != 'note_id'){
			$(location).attr('href', url);
		}*/
		$('#gridTypeId').val(datarow.id);
		$('#gridType').val('supplier');
		// Use datarow for display of data in div outside of grid
	});
	
	$("#jqxSuppliers").bind("sort", function (event) {
	    	$("#jqxSuppliers").jqxGrid('updatebounddata', 'filter');
	});
	
	$("#jqxSuppliers").bind("pagesizechanged", function (event) {
		var args = event.args;
		var pagenumber = args.pagenum;
		var pagesize = args.pagesize;
		$('#pageSizeGrabing').val(pagesize);
	});
}

function validateSupplier(){
	var errors = 0;	
	$('.errorText').remove();
	
	if($('#frm_supplier #company_name').val() == ''){
		$( '<span class="errorText">Please enter company name</span>' ).insertAfter( '#frm_supplier #company_name' );
		errors++;
	}
	
	if($('#frm_supplier #first_name').val() == ''){
		$( '<span class="errorText">Please enter first name</span>' ).insertAfter( '#frm_supplier #first_name' );
		errors++;
	}
	
	if($('#frm_supplier #last_name').val() == ''){
		$( '<span class="errorText">Please enter last name</span>' ).insertAfter( '#frm_supplier #last_name' );
		errors++;
	}
	
	/*if($('#frm_supplier #address').val() == ''){
		$( '<span class="errorText">Please enter address</span>' ).insertAfter( '#frm_supplier #address' );
		errors++;
	}*/
	
	/*if($.trim($('#frm_supplier #email').val()) == ''){
		$('<span class="errorText">Please enter email</span>').insertAfter('#frm_supplier #email');
		errors++;		
	}else*/ if($.trim($('#frm_supplier #email').val()) != '' && !validateEmail($('#frm_supplier #email').val())){
		$('<span class="errorText">Please enter valid email</span>').insertAfter('#frm_supplier #email');
		errors++;
	}
	
	/*if($('#frm_supplier #mobile').val() == ''){
		$('<span class="errorText">Please enter mobile</span>').insertAfter('#frm_supplier #mobile');
		errors++;
	}*/
	
	if($('#frm_supplier #phone').val() == ''){
		$('<span class="errorText">Please enter phone</span>').insertAfter('#frm_supplier #phone');
		errors++;
	}
	
	if($("#frm_supplier .isUrlValid").val() != ''){
		if(!isUrlValid($("#frm_supplier .isUrlValid").val())){
			$('<span class="errorText">Please enter valid Url</span>').insertAfter('#frm_supplier .isUrlValid');
			errors++;
		}
	}
	
	/*if($('#frm_supplier #supplier_type option:selected').val() == ''){
		$( '<span class="errorText">Please select supplier type</span>' ).insertAfter( '#frm_supplier #supplier_type' );
		errors++;
	}
	
	if($('input[type=checkbox]:checked').length == 0){
   	 $( '<span class="errorText">Please select at least one service</span>' ).insertAfter( '#frm_supplier #selectServiceError' );
    	valid = false;
	}*/
	
	return errors;
}

function validateUpdateSupplier(){
	var errors = 0;
	$('.errorText').remove();
	if($('#frm_supplier #company_name').val() == ''){
		$( '<span class="errorText">Please enter company name</span>' ).insertAfter( '#frm_supplier #company_name' );
		errors++;
	}
	
	if($('#frm_supplier #first_name').val() == ''){
		$( '<span class="errorText">Please enter first name</span>' ).insertAfter( '#frm_supplier #first_name' );
		errors++;
	}
	
	if($('#frm_supplier #last_name').val() == ''){
		$( '<span class="errorText">Please enter last name</span>' ).insertAfter( '#frm_supplier #last_name' );
		errors++;
	}
	
	if($('#frm_supplier #address').val() == ''){
		$( '<span class="errorText">Please enter address</span>' ).insertAfter( '#frm_supplier #address' );
		errors++;
	}
	
	if($.trim($('#frm_supplier #email').val()) == ''){
		$('<span class="errorText">Please enter email</span>').insertAfter('#frm_supplier #email');
		errors++;		
	}else if($.trim($('#frm_supplier #email').val()) != '' && !validateEmail($('#frm_supplier #email').val())){
		$('<span class="errorText">Please enter valid email</span>').insertAfter('#frm_supplier #email');
		errors++;
	}
	
	if($('#frm_supplier #mobile').val() == ''){
		$('<span class="errorText">Please enter mobile</span>').insertAfter('#frm_supplier #mobile');
		errors++;
	}
	
	if($('#frm_supplier #phone').val() == ''){
		$('<span class="errorText">Please enter phone</span>').insertAfter('#frm_supplier #phone');
		errors++;
	}
	
	if($("#frm_supplier .isUrlValid").val() != ''){
		if(!isUrlValid($("#frm_supplier .isUrlValid").val())){
			$('<span class="errorText">Please enter valid Url</span>').insertAfter('#frm_supplier .isUrlValid');
			errors++;
		}
	}
	
	if($('#frm_supplier #supplier_type option:selected').val() == ''){
		$( '<span class="errorText">Please select supplier type</span>' ).insertAfter( '#frm_supplier #supplier_type' );
		errors++;
	}
	
	if($('input[type=checkbox]:checked').length == 0){
   	 $( '<span class="errorText">Please select at least one service</span>' ).insertAfter( '#frm_supplier #service' );
    	valid = false;
	}
	
	return errors;
}

function saveSupplier(form){
		var errors = validateSupplier();
		if(errors == 0){
			$('#saveOpp').attr('disabled','disabled');
			var url = '/suppliers';
			var data = $(form).serialize();
			$.post(url, data, function(response){
				if(response > 0){
					if ($('#newSuppliers #lookupSupplierId').length) {
						/* This is from a lookup we need to save the value and close lightboxes */
						var selectedSupplierId = $('#newSuppliers #lookupSupplierId').val();
						var selectedSupplierName = $('#newSuppliers #lookupSupplierName').val();
						$('#'+selectedSupplierId).val(response);
						$('#'+selectedSupplierName).val($('#frm_supplier #first_name').val() + ' ' + $('#frm_supplier #last_name').val());
						cancelButtonProperty('frm_supplier', 'newSuppliers');
						$('#supplierlookup .closePopup').click();
					} else if(!$('#supplierId').length) {
						/* This is a new supplier, just view it */
						window.location.href = '/supplierdetails/'+response;
					}
				}
			});
		}else{
			$('#saveOpp').removeAttr('disabled');
		}
}

function updateSupplier(form){
	if($('#saveSupplierButton').text() === "Edit"){
		$('#saveSupplierButton').text('Save');
		$('#frm_supplier .displayHide').show();
		$('#frm_supplier .hiddenUnqValues').hide();
	} else if($('#saveSupplierButton').text() === "Save"){
		var errors = validateSupplier();
		if(errors == 0){
			var url = '/suppliers';
			var data = $(form).serialize();
			$.post(url, data, function(response){
				if(response > 0){
					alert('Supplier details updated successfully');
					//location.reload();
					$('#company_name_heading').html($('#company_name').val());
					$('#company_phone_heading').html("Ph: "+$('#phone').val());
					$('#company_email_heading').html("E: "+$('#email').val());
					$('#company_name_sup').text($('#company_name').val());
					$('#first_name_sup').text($('#first_name').val());
					$('#last_name_sup').text($('#last_name').val());
					$('#address1_sup').text($('#address1').val());
					$('#address2_sup').text($('#address2').val());
					$('#postcode_sup').text($('#postcode').val());
					$('#state_id_sup').text($("#state_id option[value="+$('#state_id option:selected').val()+"]").text());
					$('#country_id_sup').text($("#country_id option[value="+$('#country_id option:selected').val()+"]").text());
					$('#mobile_sup').text($('#mobile').val());
					$('#phone_sup').text($('#phone').val());
					/* Add http:// if missing */
					/* TODO: this happens in the backend too - a bit wasteful */
					var website = $('#website').val();
					if (website.length > 0) {
						if(!/^(http|https):\/\//i.test(website)){
							website = 'http://' + website;
						}
						$('#website_sup').html('<a href="' + website + '" target="_blank">' + website + '</a>');
					} else {
						$('#website_sup').text('');
					}
					$('#supplier_type_sup').text($("#supplier_type option[value="+$('#supplier_type option:selected').val()+"]").text());
					$('#rap_id_sup').text($('#rap_id').val());
					$('#comment_sup').text($('#comment').val());
					var chkvalList = new Array();
					$('.chkVals:checked').each(function(){
					   chkvalList.push($(this).attr("id"));
					});
					var chkvalListString = chkvalList.join(', ');
					if(chkvalListString)
						$('#service_sup').html(chkvalListString.toString());
					else
						$('#service_sup').html('No services are added');
					$('#frm_supplier .displayHide').hide();
					$('#frm_supplier .hiddenUnqValues').show();
					$('#saveSupplierButton').text('Edit');
				}
			});
		}
	}
}

function deleteSupplier(id){
	if (confirm("Are you sure you want to delete supplier")) {
        var url = '/deletesupplier/'+id;
		var data = 'id='+id;
		$.post(url, data, function(response){
			if(response == 1){
				window.location.href = '/suppliers';
			}
		});
    } else {
		return false;
	}
}

function supplierLookup(gridContainer, recordsPerPage, keyword, customerId, form_name, supplier_id_hidden, supplier_name_view){
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

		$('#'+form_name+' #'+supplier_id_hidden).val(datarow.id);
		$('#'+form_name+' #'+supplier_name_view).val(datarow.first_name+' '+datarow.last_name);
		$('#supplierLookup .closePopup').click();
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

function newSupplier() {
	var url = '/newsuppliersform';
	
	$.post(url, null, function(response){
		$('#newSuppliersForm').html(response);
		jQuery( ".dropdown1" ).dropkick({
	        mobile: true
		});
		lightboxmid();		
	});
}
/*
 * Same as above, but modify the 'Create' action button.
 * TODO: merge these two with param.
 */ 
function newSupplierFromLookup() {
	var url = '/newsuppliersform';
	
	$.post(url, null, function(response){
		$('#newSuppliersForm').html(response);
		jQuery( ".dropdown1" ).dropkick({
	        mobile: true
		});
		lightboxmid();
		$('#newSuppliersForm #saveOpp').attr('onclick', "saveSupplierFromLookup($('#frm_supplier'));");
	});
}