/*
 * Generic click function
 */
function invoice_item_cellclick(event, grid, type) {
	var current_index = event.args.rowindex;
	var current_column = event.args.column.datafield;
	var datarow = $(grid).jqxGrid('getrowdata', current_index);
	
	appInvoiceItems('invoice_items_body', type, datarow);
	
	$('.lightBoxTitle .closePopup').click();
}

function diamond_cellclick(event) {
	invoice_item_cellclick(event, "#jqxDianonds", 'diamond');
}

function getDiamondLookup(){
    var recordsPerPage = $('#pageSizeGrabing').val();
    /* TODO: 'price' should be in this list. This could also be in hidden div */
	var columnList = 'stock_code,shape,color,measurement,cut,polish,symmetry,flurosence';
	
	getDiamonds(recordsPerPage, columnList, '', $('#frm_filter_fiamond').serialize());
}

function weddingring_cellclick(event) {
	invoice_item_cellclick(event, "#jqxWeddingring", 'weddingring');
}

function getWeddRingLookup(){
    var recordsPerPage = $('#pageSizeGrabing').val();
	var columnList = 'stock_code,cad_code,metal_type,profile,band_width,band_thickness,finish,fit_option,metal_thickness,price';
	getWeddingrings(recordsPerPage, columnList, '');
}

function engagementring_cellclick(event) {
	invoice_item_cellclick(event, "#jqxEngagementring", 'engring');
}

function getEngRingLookup(){
    var recordsPerPage = $('#pageSizeGrabing').val();
	var columnList = 'stock_code,supplier_name,ring_type,cad_code,metal_type,profile,band_width,band_thickness,halo_width,halo_thickness,head_title,claw_title,setting_height,metal_thickness';
	getEngagementrings(recordsPerPage, columnList, '');
}

function earring_cellclick(event) {
	invoice_item_cellclick(event, "#jqxEarring", 'earring');
}

function getEarringLookup(gridId, recordsPerPage, keyword){
    var recordsPerPage = $('#pageSizeGrabing').val();
	var columnList = 'stock_code,supplier_name,ring_style,cad_code,metal_type,profile,metal_weight,band_width,band_thickness,price';
	getEarrings(recordsPerPage, columnList, '');
}

function pendant_cellclick(event) {
	invoice_item_cellclick(event, "#jqxPendant", 'pendant');
}

function getPendantLookup(gridId, recordsPerPage, keyword){
    var recordsPerPage = $('#pageSizeGrabing').val();
	var columnList = 'stock_code,supplier_name,ring_style,metal_type,profile,metal_weight,head_settings,setting_style,halo_width,halo_thickness,price,owner_name';
	getPendants(recordsPerPage, columnList, '');
}

function chain_cellclick(event) {
	invoice_item_cellclick(event, "#jqxChain", 'chain');
}

function getChainLookup(gridId, recordsPerPage, keyword){
    var recordsPerPage = $('#pageSizeGrabing').val();
	var columnList = 'stock_code,supplier_name,style,length,thickness,metal_type,metal_weight,price,owner_name';
	getChain(recordsPerPage, columnList, '');
}

function miscellaneous_cellclick(event) {
	invoice_item_cellclick(event, "#jqxMiscellaneous", 'misc');
}

function getMiscellaneousLookup(gridId, recordsPerPage, keyword){
    var recordsPerPage = $('#pageSizeGrabing').val();
	var columnList = 'stock_code,title,description,price,owner_name';
	getMiscellaneous(recordsPerPage, columnList, '');
}

$(document).on('click', '.deleteInvoiceRow', function(){
	//alert();
	//alert($('#showInvoiceItem #invoice_items_body tr').length)
	if($('#showInvoiceItem #invoice_items_body tr').length==0){
		$('#showInvoiceItem').hide();
	}
});

/**
 * Creace html listing form items
 * containerId - div id
 * type - item type
 * data - item object
 */
function appInvoiceItems(containerId, type, data){
	//alert('#item_type_'+type+'_'+data.id);
	if($('#item_type_'+type+'_'+data.id).length > 0){
		var quantity = parseInt($('#qty_'+type+'_'+data.id).val()) + 1;
		var total_price = quantity * parseFloat(data.price).toFixed(2);
		
		$('#qty_'+type+'_'+data.id).val(quantity);		
		$("#total_price_"+type+'_'+data.id).text('$'+total_price);
	}else{
		if(data.price == '-')
		data.price = 0;
		
		$('#showInvoiceItem').show();
		var html = '<tr id="item_tr_'+type+'_'+data.id+'"><td><input type="hidden" name="item_type_'+type+'_'+data.id+'" id="item_type_'+type+'_'+data.id+'" value="'+type+'"><input type="hidden" name="item_id_'+type+'_'+data.id+'" id="item_id_'+type+'_'+data.id+'" value="'+data.id+'"></td><td>';
		var description = '';
		
		if(type == 'diamond'){
			
			description = data.carat + ' Carat ' + data.shape + ' Cut Diamond, ' + data.color + ' Color, ' + data.clarity + ' Clarity, Lab Certified: ' + data.lab + ' ' + data.cert_no;
			
			html = html + 'Diamond</td><td><textarea name="item_desc_'+type+'_'+data.id+'" id="item_desc_'+type+'_'+data.id+'" class="cmntextarea textareaGlobalMaxLength">'+description+'</textarea>';
		}else if(type == 'weddingring'){
			
			description = 'Stock Code: '+data.stock_code+', Cad Code: '+data.cad_code+', Band Width: '+data.band_width+', Band Thickness: '+data.band_thickness+', Metal Thickness: '+data.cmetal_thicknessut+', Description: '+data.description+', Metal Type: '+data.metal_type+', Profile: '+data.profile+', Finish: '+data.finish+', Fit Option: '+data.fit_option+', Supplier Name: '+data.supplier_name;
			
			html = html + 'Wedding Ring</td><td><textarea name="item_desc_'+type+'_'+data.id+'" id="item_desc_'+type+'_'+data.id+'" class="cmntextarea textareaGlobalMaxLength">'+description+'</textarea>';
		}else if(type == 'engring'){
			
			description = 'Stock Code: '+data.stock_code+', Cad Code: '+data.cad_code+', Band Width: '+data.band_width+', Band Thickness: '+data.band_thickness+', Metal Thickness: '+data.cmetal_thicknessut+', Description: '+data.description+', Ring Type: '+data.ring_type+', Metal Type: '+data.metal_type+', Profile: '+data.profile+', Halo Width: '+data.halo_width+', Halo Thickness: '+data.halo_thickness+', Head Title: '+data.head_title+', Claw Title: '+data.claw_title+', Setting Height: '+data.setting_height+', Supplier Name: '+data.supplier_name;
			
			html = html + 'Engagement Ring</td><td><textarea name="item_desc_'+type+'_'+data.id+'" id="item_desc_'+type+'_'+data.id+'" class="cmntextarea textareaGlobalMaxLength">'+description+'</textarea>';
		}else if(type == 'earring'){
			
			description = 'Stock Code: '+data.stock_code+', Cad Code: '+data.cad_code+', Band Width: '+data.band_width+', Band Thickness: '+data.band_thickness+', Description: '+data.description+', Ring Type: '+data.ring_type+', Ring Type: '+data.ring_type+', Metal Type: '+data.metal_type+', Profile: '+data.profile+', Metal Weight: '+data.metal_weight+', Supplier Name: '+data.supplier_name;
			
			html = html + 'Ear Ring</td><td><textarea name="item_desc_'+type+'_'+data.id+'" id="item_desc_'+type+'_'+data.id+'" class="cmntextarea textareaGlobalMaxLength">'+description+'</textarea>';
		}else if(type == 'pendant'){
			
			description = 'Stock Code: '+data.stock_code+', Head Settings: '+data.head_settings+', Setting Style: '+data.setting_style+', Halo Width: '+data.halo_width+', Halo Thickness: '+data.halo_thickness+', Description: '+data.description+', Ring Style: '+data.ring_style+', Metal Type: '+data.metal_type+', Profile: '+data.profile+', Metal Weight: '+data.metal_weight+', Supplier Name: '+data.supplier_name;
			
			html = html + 'Pendant</td><td><textarea name="item_desc_'+type+'_'+data.id+'" id="item_desc_'+type+'_'+data.id+'" class="cmntextarea textareaGlobalMaxLength">'+description+'</textarea>';
		}else if(type == 'chain'){
			
			description = 'Stock Code: '+data.stock_code+', Style: '+data.style+', Length: '+data.length+', Thickness: '+data.thickness+', Metal Type: '+data.metal_type+', Metal Weight: '+data.metal_weight+', Supplier Name: '+data.supplier_name;
			
			html = html + 'Chain</td><td><textarea name="item_desc_'+type+'_'+data.id+'" id="item_desc_'+type+'_'+data.id+'" class="cmntextarea textareaGlobalMaxLength">'+description+'</textarea>';
		}else if(type == 'misc'){
			description = 'Stock Code: '+data.cad_code+', Title: '+data.title+', Description: '+data.description;
			html = html + 'Miscellaneous</td><td><textarea name="item_desc_'+type+'_'+data.id+'" id="item_desc_'+type+'_'+data.id+'" class="cmntextarea textareaGlobalMaxLength">'+description+'</textarea>';
		}
		
		if (xeroAccounts.length > 0) {
			var accountSelectHtml = '<select name="acc_'+type+'_'+data.id+'" id="acc_'+type+'_'+data.id+'" class="width100p dropdown">';
			for(i=0;i<xeroAccounts.length;i++){
				if(xeroAccounts[i].Status == 'ACTIVE')
					accountSelectHtml = accountSelectHtml+'<option value="'+xeroAccounts[i].Code+'">'+xeroAccounts[i].Name+'</option>';
			}
			accountSelectHtml = accountSelectHtml+'</select>';
		}

		if (xeroSalesPersons.length > 0) {
			var salesPersonsSelectHtml = '<select name="sps_'+type+'_'+data.id+'" id="sps_'+type+'_'+data.id+'" class="width100p dropdown">';
			for(i=0;i<xeroSalesPersons.length;i++){
				if(xeroSalesPersons[i].Status == 'ACTIVE')
					salesPersonsSelectHtml  = salesPersonsSelectHtml +'<option value="'+xeroSalesPersons[i].Name+'">'+xeroSalesPersons[i].Name+'</option>';
			}
			salesPersonsSelectHtml  = salesPersonsSelectHtml +'</select>';
		}
		
		var html = html + '</td><td><input type="text" onkeyup="validateInteger(this);" onblur="itemTotal('+data.id+', \''+type+'\');" name="qty_'+type+'_'+data.id+'" id="qty_'+type+'_'+data.id+'" class="cmnInput" value="1" maxlength="2" /></td><td><input type="text" data-numeric="yes" onkeyup="validateFloat(this);" onblur="itemTotal('+data.id+', \''+type+'\');" class="cmnInput" name="unit_price_'+type+'_'+data.id+'" id="unit_price_'+type+'_'+data.id+'" value="'+parseFloat(data.price).toFixed(2)+'" /></td><td><input type="text" data-numeric="yes" onkeyup="validateFloat(this);" onblur="itemTotal('+data.id+', \''+type+'\');" class="cmnInput" name="discount_'+type+'_'+data.id+'" id="discount_'+type+'_'+data.id+'" /></td><td>'+accountSelectHtml+'</td><td>'+salesPersonsSelectHtml+'</td><td id="total_price_'+type+'_'+data.id+'">$'+parseFloat(data.price).toFixed(2)+'</td><td><a href="javascript:;" onclick="$(\'#item_tr_'+type+'_'+data.id+'\').remove();" class="cmnBtn redBtn" style="margin-top:0;">Delete</a></td></tr>';
		
		$('#'+containerId).append(html);
		globalTextAreaMaxLength('textareaGlobalMaxLength');
		globalTextboxMaxLength('textboxGlobalMaxLength');
	}
	
	jQuery(".dropdown").dropkick({
		mobile: true
	});
}

/**
 * Creace summarized html for quotes
 */
function getQuotesSummary(){
	var html = '';
	var subTotal = 0;
	var total = 0;
	var qty = 0;
	var unit_price = 0;
	$('#invoice_items_body > tr').each(function(){
		html = html + '<tr><td></td>';
		$(this).find('td').each (function(index) {
			if(index == 0){
				html += "<input type='hidden' name='item_id[]' value='"+$(this).children(':first-child').next().val()+"' /><input type='hidden' name='item_type[]' value='"+$(this).children(':first-child').val()+"' />";
			}
			if(index == 1){
				html = html + '<td>'+$(this).text()+'</td>';
                //html += "<input type='hidden' name='item_type[]' value='"+$(this).text()+"' />";
			}else if(index == 2){
				html = html + '<td>'+$(this).find('textarea').val()+'</td>';
                html += "<input type='hidden' name='item_desc[]' value='"+$(this).find('textarea').val()+"' />";
			}else if(index == 3){
				qty = parseInt($(this).find('input').val());
                html += "<input type='hidden' name='qty[]' value='"+$(this).find('input').val()+"' />";
				html = html + '<td>'+$(this).find('input').val()+'</td>';
			}else if(index == 4){
				unit_price = parseFloat($(this).find('input').val()).toFixed(2);
				html = html + '<td>'+unit_price+'</td>';
                html += "<input type='hidden' name='sub[]' value='"+unit_price+"' />";
			}else if(index == 5){
				html = html + '<td>'+$(this).find('input').val()+'</td>';
                html += "<input type='hidden' name='discount[]' value='"+$(this).find('input').val()+"' />";
			}else if(index == 6){
				html = html + '<td>'+$(this).find('select option:selected').text()+'</td>';
                html += "<input type='hidden' name='account[]' value='"+$(this).find('select option:selected').val()+"' />";
			}else if(index == 7){
				html = html + '<td>'+$(this).find('select option:selected').text()+'</td>';
                html += "<input type='hidden' name='salesperson[]' value='"+$(this).find('select option:selected').val()+"' />";
			}else if(index == 8){
				total = total + parseFloat($(this).text().replace('$', ''));
				html = html + '<td>'+$(this).text()+'</td>';
                html += "<input type='hidden' name='total[]' value='"+$(this).text()+"' />";
			}
		});
		html = html + '</tr>';
	});	
	
	$('#quote_summary_body').html(html);
	$('#sub_total').html('$'+parseFloat(total).toFixed(2));
	$('#total').html('$'+parseFloat(total).toFixed(2));
}

/**
 * validate inter value
 */
function validateInteger(elem){
	$(elem).val($(elem).val().replace(/([^0-9])+/g, ""));
}

/**
 * validate flote value
 */
function validateFloat(elem){
	/*var disc = $(elem).val().replace(/([^0-9\.])/g, "");
	if(disc != '')
		 disc = parseFloat(disc).toFixed(2);
	$(elem).val(disc);*/
	
	return false;
}

/**
 * calculate total value
 */
function itemTotal(item_id, type){
	var disc = $('#discount_'+type+'_'+item_id).val();
	var qty = $('#qty_'+type+'_'+item_id).val();
	var unit_price = $('#unit_price_'+type+'_'+item_id).val();
	var container = $('#total_price_'+type+'_'+item_id);	
	var total = (unit_price * qty) - (unit_price * qty * disc / 100);
	total = parseFloat(total).toFixed(2);
	container.html('$'+total);
}

/**
 * Generate grid for quites
 * gridId - div id
 * recordsPerPage - records per page
 * keyword - search keyword
 */
function getQuotes(gridId, recordsPerPage, keyword){
	var keyword = $("#quote_search").val();
	if(keyword != '' && keyword != undefined){
		var url = '/ajax-quote?keyword='+keyword;
	} else {
		var url = '/ajax-quote';
	}
	
	// prepare the data
	var source =
	{
		datatype: "json",
		datafields: [
			{ name: 'id' },
			{ name: 'created_date' },
			{ name: 'customer_name' },
			{ name: 'email' },
			//{ name: 'payment_made' },
            { name: 'date_due' },
			{ name: 'email_date' },
			//{ name: 'options' },			
		],
		//localdata: data,
		cache: false,
		url: url,
		root: 'Rows',
		sortcolumn: 'created_date',
		sortdirection: 'desc',
		beforeprocessing: function (data) {
			source.totalrecords = data.TotalRows;
			
			return data.Rows;	
		},
		loadComplete:function(data){
			 
		},
		filter: function () {
			// update the grid and send a request to the server.
			$("#"+gridId).jqxGrid('updatebounddata', 'filter');
		},
		bindingComplete:function(){
			
		}
	};
	
	var pageable = true, sortable = true;
	var dataAdapter = new $.jqx.dataAdapter(source, {
		downloadComplete: function (data, status, xhr) { },
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
	
	var selectOptions = function(row, columnfield, value, defaulthtml, columnproperties){
		var datarow = $("#"+gridId).jqxGrid('getrowdata', row);
		var html = '';
		if(datarow)
			html = '<a href="javascript:;" data-delete="/deletequote/'+datarow.id+'" data-duplicate="/duplicatequote/'+datarow.id+'" data-copy="/copytoinvoice/'+datarow.id+'" class="editOptions quoteOptions"></a>';
		
		return html;		
	}
	
	var emailStatus = function(row, columnfield, value, defaulthtml, columnproperties){
		var datarow = $("#"+gridId).jqxGrid('getrowdata', row);
		var html = '';
		if(datarow){
			if(datarow.email_date == null)
				html = '<div style="margin-top:12px;text-align:left"><a data-popup="composeEmail" class="cmnBtn vm marT0 lightBoxClick" href="javascript:;" onclick="composeInvoiceEmail('+datarow.id+', 1);">Send</a></div>';
			else
				html = '<div style="margin-top:12px; text-align:left"><a data-popup="composeEmail" class="cmnBtn vm marT0 popupLink lightBoxClick" href="javascript:;" onclick="composeInvoiceEmail('+datarow.id+', 1);">Send</a> &nbsp; <span class="emailSentIcon"></span> &nbsp; '+datarow.email_date+'</div>';
		}
			
		return html;		
	}

	$("#"+gridId).jqxGrid(
	{
		width: '100%',
		source: dataAdapter,
		sorttogglestates:1,
		//showfilterrow: true,
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
		//pagermode: 'simple',
		//  pager: '#gridpager',
		//columnsresize: true,
		rendergridrows: function (params) {
                 
			return params.data;
		},
		columns: [
			{ text: 'Created', type: 'string', datafield: 'created_date', width:150},
			{ text: 'Customer <br /> Name', type: 'string', datafield: 'customer_name', width:170},
			{ text: 'Email', type: 'string', datafield: 'email', width:250},
			//{ text: 'Payment <br /> Made', type: 'string', datafield: 'payment_mode', width:120},
            { text: 'Date Due', type: 'string', datafield: 'date_due', width:170},
			{ text: 'Send <br /> email', type: 'string', cellsrenderer: emailStatus, width:200},
			{ text: 'Options', type: 'string', cellsrenderer: selectOptions, width:150},			
		]
	});
	
	$("#"+gridId).bind('cellclick', function(event) {
		var current_index = event.args.rowindex;
		var current_column = event.args.column.datafield;
		var datarow = $("#"+gridId).jqxGrid('getrowdata', current_index);
		
		if(current_column)
			window.location.href = '/editinvoicequotes/'+datarow.id;
		
		// Use datarow for display of data in div outside of grid
	});
	
	$("#"+gridId).bind("sort", function (event) {
	    /*var sortinformation = event.args.sortinformation;
	    
	    var sortdatafield = sortinformation.sortcolumn;
	    
	    var sortorder = sortinformation.sortdirection.ascending ? "asc" : "desc";
	    
	    var sortUrl = '/ajaxgetdiamonds?sortdatafield='+sortdatafield+'&sortorder='+sortorder;*/
	    
	   //$.get(sortUrl, function(source){
	    	//$("#jqxDianonds").jqxGrid('databind', source);
	    	$("#"+gridId).jqxGrid('updatebounddata', 'filter');
	   //});
	});
	
	$("#"+gridId).bind("pagesizechanged", function (event) {
		var args = event.args;
		var pagenumber = args.pagenum;
		var pagesize = args.pagesize;
		$('#pageSizeGrabing').val(pagesize);
	});
}

$(document).on('click', '.invoiceOptions', function(){
	$('.editoptiosBlock').remove();
	
	var deleteLink = $(this).attr('data-delete');
	var duplicateLink = $(this).attr('data-duplicate');
	var createOrder = $(this).attr('data-create-order');
	
	var thisleftPos = $(this).offset().left-400;
	var thistopPos = ($(this).offset().top + $('section.rightCol').scrollTop())-20;
	
	$('section.rightCol').append('<ul style="left:'+thisleftPos+'px;top:'+thistopPos+'px;" class="showWithAimat editoptiosBlock"><li><a href="javascript: confirmDelete(\''+deleteLink+'\');">Delete</a></li><li><a href="'+duplicateLink+'">Duplicate</a></li><li><a href="'+createOrder+'">Create Order</a></li></ul>');	
});

$(document).on('click','.quoteOptions', function(){
	$('.editoptiosBlock').remove();
	
	var deleteLink = $(this).attr('data-delete');
	var duplicateLink = $(this).attr('data-duplicate');
	var copyLink = $(this).attr('data-copy');
	
	var thisleftPos = $(this).offset().left-400;
	var thistopPos = ($(this).offset().top + $('section.rightCol').scrollTop())-20;
	
	$('section.rightCol').append('<ul style="left:'+thisleftPos+'px;top:'+thistopPos+'px;" class="showWithAimat editoptiosBlock"><li><a href="javascript: confirmDelete(\''+deleteLink+'\');">Delete</a></li><li><a href="'+duplicateLink+'">Duplicate</a></li><li><a href="'+copyLink+'">Move Items to Invoice</a></li></ul>');
});

function confirmDelete(deleteLink){
	if(confirm('Are you sure to delete ?'))
		window.location.href = deleteLink;
}

$(document).on('click','body', function (event) {
	var $target = jQuery(event.target);
	if (!$target.parents().is(".editoptiosBlock") && !$target.is(".editOptions")) {
		$('.editoptiosBlock').remove();
		//$('.editDrop').css('z-index', '99');
	}
});

/**
 * Generate grid for invoice
 * gridId - div id
 * recordsPerPage - records per page
 * keyword - search keyword
 */
function getInvoices(gridId, recordsPerPage, cust_id){
	var keyword = $("#invoice_searchInput").val();
	if(keyword != '' && keyword != undefined){
		var url = '/ajax-invoice?keyword='+keyword;
		if(cust_id)
			url = url+'&customer_id='+cust_id;
	} else {
		var url = '/ajax-invoice';
		if(cust_id)
			url = url+'?customer_id='+cust_id;
	}
	
	// prepare the data
	var source =
	{
		datatype: "json",
		datafields: [
			{ name: 'id' },
			{ name: 'invoice_number' },
			{ name: 'created_date' },
			{ name: 'customer_name' },
			{ name: 'email' },
			{ name: 'payment_mode' },
            { name: 'date_due' },
			{ name: 'email_date' },
			//{ name: 'xero_account' },
			{ name: 'xero_tax_rate' },
            { name: 'xero_payment_made' },
			{ name: 'xero_date_due' },
			{ name: 'order_created' }
			
		],
		//localdata: data,
		cache: false,
		url: url,
		root: 'Rows',
		sortcolumn: 'created_date',
		sortdirection: 'desc',
		beforeprocessing: function (data) {
        source.totalrecords = data.TotalRows;
			return data.Rows;
		},
		loadComplete:function(data){
			 
		},
		filter: function () {
			// update the grid and send a request to the server.
			$("#"+gridId).jqxGrid('updatebounddata', 'filter');
		},
		bindingComplete:function(){

		}
	};
	
	var pageable = true, sortable = true;
	var dataAdapter = new $.jqx.dataAdapter(source, {
		downloadComplete: function (data, status, xhr) { },
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
	 
	var selectOptions = function(row, columnfield, value, defaulthtml, columnproperties){
		var datarow = $("#"+gridId).jqxGrid('getrowdata', row);
		var html = '';
		if(datarow)
			html = '<a href="javascript:;" data-delete="/deleteinvoice/'+datarow.id+'" data-duplicate="/duplicateinvoice/'+datarow.id+'" data-create-order="/orders/'+datarow.id+'" class="editOptions invoiceOptions"></a>';
		return html;		
	}
	
	var emailStatus = function(row, columnfield, value, defaulthtml, columnproperties){
		var datarow = $("#"+gridId).jqxGrid('getrowdata', row);
		var html = '';
		if(datarow){
			if(datarow.email_date == null)
				html = '<div style="margin-top:12px;text-align:left"><a data-popup="composeEmail" class="cmnBtn vm marT0 lightBoxClick" href="javascript:;" onclick="composeInvoiceEmail('+datarow.id+', 0);">Send</a></div>';
			else
				html = '<div style="margin-top:12px; text-align:left"><a data-popup="composeEmail" class="cmnBtn vm marT0 popupLink lightBoxClick" href="javascript:;" onclick="composeInvoiceEmail('+datarow.id+', 0);">Send</a> &nbsp; <span class="emailSentIcon"></span> &nbsp; '+datarow.email_date+'</div>';
		}
			
		return html;		
	}

	$("#"+gridId).jqxGrid(
	{
		width: '100%',
		source: dataAdapter,
		pageable: pageable,
		sortable: sortable,
		sorttogglestates:1,
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
		//pagermode: 'simple',
		//pager: '#gridpager',
		//columnsresize: true,
		rendergridrows: function (params) {
			return params.data;
		},
		columns: [
			{ text: 'Invoice Number', type: 'string', datafield: 'invoice_number', width:100},
			{ text: 'Created', type: 'string', datafield: 'created_date', width:100},
			{ text: 'Customer <br /> Name', type: 'string', datafield: 'customer_name', width:120},
			{ text: 'Email', type: 'string', datafield: 'email', width:200},
			{ text: 'Payment <br /> Made%', type: 'string', datafield: 'xero_payment_made', width:120},
			{ text: 'Tax Rate%', type: 'string', datafield: 'xero_tax_rate', width:120},
			//{ text: 'Account', type: 'string', datafield: 'xero_account', width:120},
            { text: 'Date Due', type: 'string', datafield: 'xero_date_due', width:120},
			{ text: 'Send <br /> email', type: 'string', cellsrenderer: emailStatus, width:200},
			{ text: 'Options', type: 'string', cellsrenderer: selectOptions, width:100},
			{ text: 'Order<br/>created', type: 'string', datafield: 'order_created', width:100},
		]
	});
	
	$("#"+gridId).bind('cellclick', function(event) {
		var current_index = event.args.rowindex;
		var current_column = event.args.column.datafield;
		
		var datarow = $("#"+gridId).jqxGrid('getrowdata', current_index);
		
		if(current_column)
			window.location.href = '/editinvoicequotes/'+datarow.id;
		
		// Use datarow for display of data in div outside of grid
	});

	
	$("#"+gridId).bind("sort", function (event) {
	    /*var sortinformation = event.args.sortinformation;
	    
	    var sortdatafield = sortinformation.sortcolumn;
	    
	    var sortorder = sortinformation.sortdirection.ascending ? "asc" : "desc";
	    
	    var sortUrl = '/ajaxgetdiamonds?sortdatafield='+sortdatafield+'&sortorder='+sortorder;*/
	    
	   //$.get(sortUrl, function(source){
	    	//$("#jqxDianonds").jqxGrid('databind', source);
	    	$("#"+gridId).jqxGrid('updatebounddata', 'filter');
	   //});
	});
	
	$("#"+gridId).bind("pagesizechanged", function (event) {
		var args = event.args;
		var pagenumber = args.pagenum;
		var pagesize = args.pagesize;
		$('#pageSizeGrabing').val(pagesize);
	});
}

/**
 * Validate email form
 */
function validateComposeInvoiceEmail(form){
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
 * Make ajax call to send email and store in database
 * form
 * elem - submit button
 * gridId - container id
 */
function emailInvoice(form, elem, gridId){
	var url = '/emailinvoice';
	
	var errors = validateComposeInvoiceEmail(form);
	if(errors == 0){
		$.ajax({
			url: url,
			//async: false,
			data: form.serialize(),
			type: 'post',
			beforeSend: function(){
				elem.attr('disabled', 'disabled');
				form.closest('.lightBoxContent').append('<div class="ajaxLoader" style="position:absolute;left0;top:0;right:0;bottom:0"><div class="pageLoader"></div></div>');
			},
			success: function(response){
				if(response == 1){
					$("#"+gridId).jqxGrid('updatebounddata', 'filter');
				}else{
					var data = JSON.parse(response);
					alert(data.oauth_problem_advice);
				}
				form.closest('.lightBoxContent').find('.ajaxLoader').remove();
				$('.lightBoxTitle .closePopup').click();
			}
		});
	}
}

/**
 * Make ajax call to popup the email form
 * invoice_id
 */
function composeInvoiceEmail(invoice_id, is_quote){
	var url = '/composeinvoiceemail';
	$.ajax({
		url: url,
		data: {invoice_id: invoice_id, is_quote: is_quote},
		type: 'post',
		beforeSend: function(){
			$('#composeemailpopup').html('<div class="ajaxLoader"><div class="pageLoader"></div></div>');
			lightboxmid();
		},
		success: function(response){
			$('#composeemailpopup').html(response);
			lightboxmid();
		}
	});
}

/**
 * Grid for invoice emails
 * recordsPerPage, customer_id, keyword
 */
 
function getInvoiceEmails(recordsPerPage, customer_id, keyword, gridId){
	
	if(keyword && !customer_id){
		var url = '/ajaxgetinvoiceemail?keyword='+keyword;
	} else if(!keyword && customer_id){
		var url = '/ajaxgetinvoiceemail?customer_id='+customer_id;
	}else if(keyword && customer_id){
		var url = '/ajaxgetinvoiceemail?keyword='+keyword+'&customer_id='+customer_id;
	}else if(!keyword && !customer_id){
		var url = '/ajaxgetinvoiceemail';
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
			{ name: 'invoice_id' },
			{ name: 'cust_id' },
			{ name: 'subject' },
			{ name: 'message' },
			{ name: 'attachments' },
			{ name: 'created_date' },
			{ name: 'created_time' },
			{ name: 'customer_name' },
			{ name: 'customer_email' },
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
		var url = '/viewinvoiceemail/'+datarow.id;
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
 * Validate reply email form
 */
function validateReplayEmail(form){
	var errors = 0;
	$('.errorText').remove();
		
	if(form.find("textarea[name=message]").val() == ''){
		$( '<p class="errorText nowrape">Please enter email body</p>' ).insertAfter( form.find(".writeCommentBlock") );
		errors++;
	}
	
	return errors;	
}

/**
 * Make ajax call to reply to an email
 */
function replyEmail(form, elem){
	var url = '/replyemail';
	elem.attr('disabled', 'disabled');
	
	var errors = validateReplayEmail(form);
	if(errors == 0){
		$.ajax({
			url: url,
			async: false,
			data: form.serialize(),
			type: 'post',
			beforeSend: function(){
				form.closest('.lightBoxContent').append('<div class="ajaxLoader" style="position:absolute;left0;top:0;right:0;bottom:0"><div class="pageLoader"></div></div>')
			},
			success: function(response){
				$('.lightBoxTitle .closePopup').click();
				window.location.reload();
			}
		});
	}
	elem.removeAttr('disabled');
}
