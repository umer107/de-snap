/**
 * Lookup for diamonds
 * gridId - div id
 * recordsPerPage - records per page
 * keyword - search keyword
 */
function getDiamondLookup(gridId, recordsPerPage, keyword){
	
	if(keyword != '' && keyword != undefined){
		var url = '/ajaxgetdiamonds?keyword='+keyword;
	} else {
		var url = '/ajaxgetdiamonds';
	}
	
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
			{ name: 'supplier_name' },
		],
		//localdata: data,
		cache: false,
		url: url,
		root: 'Rows',
		sortcolumn: 'created_date',
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
	
	$("#"+gridId).jqxGrid(
	{
		width: '100%',
		source: dataAdapter,
		//sortable: true,
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
		//pager: '#gridpager',
		//columnsresize: true,
		rendergridrows: function (params) {
			return params.data;
		},
		columns: [
			{ text: 'Stock Code', type: 'string', datafield: 'stock_code', width:100},
			{ text: 'Shape', type: 'string', datafield: 'shape', width:100},
			{ text: 'Color', type: 'string', datafield: 'color', width:100},
			{ text: 'Measurements', type: 'string', datafield: 'measurement'},
			{ text: 'Cut', type: 'string',  datafield: 'cut', width:90},
			{ text: 'Polish', type: 'string',  datafield: 'polish', width:90},
			{ text: 'Symmetry', type: 'string', datafield: 'symmetry', width:130},
			{ text: 'Fluro', type: 'string', datafield: 'flurosence', width:90},	
			{ text: 'Price', type: 'string', datafield: 'price', width:90},
		]

	});

	//$("#jqxWidget").next('.pagerHTML').html($('#pagerjqxWidget'));	
	
	$("#"+gridId).bind('cellclick', function(event) {
		var current_index = event.args.rowindex;
		var current_column = event.args.column.datafield;
		var datarow = $("#"+gridId).jqxGrid('getrowdata', current_index);
		
		appInvoiceItems('invoice_items_body', 'diamond', datarow);
		
		$('.lightBoxTitle .closePopup').click();
		
		// Use datarow for display of data in div outside of grid
	});
	
	$("#"+gridId).bind("sort", function (event) {
	   /* var sortinformation = event.args.sortinformation;
	    
	    var sortdatafield = sortinformation.sortcolumn;
	    
	    var sortorder = sortinformation.sortdirection.ascending ? "asc" : "desc";
	    
	    var sortUrl = '/ajaxgetdiamonds?sortdatafield='+sortdatafield+'&sortorder='+sortorder;*/
	    
	   //$.get(sortUrl, function(source){
	    	//$("#"+gridId).jqxGrid('databind', source);
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
 * Lookup for wedding ring
 * gridId - div id
 * recordsPerPage - records per page
 * keyword - search keyword
 */
function getWeddRingLookup(gridId, recordsPerPage, keyword){
	
	if(keyword != '' && keyword != undefined){
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
			{ name: 'metal_thickness' },
			{ name: 'description' },
			{ name: 'invoice' },
			{ name: 'price' },
			{ name: 'created_date' },
			{ name: 'ring_type' },
			{ name: 'metal_type' },
			{ name: 'profile' },
			{ name: 'finish' },
			{ name: 'fit_option' },
			{ name: 'supplier_name' },
		],
		//localdata: data,
		cache: false,
		url: url,
		root: 'Rows',
		sortcolumn: 'created_date',
		sortdirection: 'desc',
		beforeprocessing: function (data) {
			source.totalrecords = data.TotalRows;
		},
		filter: function () {
			// update the grid and send a request to the server.
			$("#"+gridId).jqxGrid('updatebounddata', 'filter');
		}
	};
	
	var pageable, sortable;
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
					pageable = false;
					sortable = false;
			}else{
				if($("#"+gridId).hasClass('noInfoFound')){
					$("#"+gridId).removeClass('noInfoFound');
				}
				pageable = true;
				sortable = true;
			}
		},
		loadError: function (xhr, status, error) { }
	});
	
	$("#"+gridId).jqxGrid(
	{
		width: '100%',
		source: dataAdapter,
		//sortable: true,
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
		//pager: '#gridpager',
		//columnsresize: true,
		rendergridrows: function (params) {
			return params.data;
		},
		columns: [
			{ text: 'Stock Code', type: 'string', datafield: 'stock_code', width:100},
			{ text: 'CAD <br /> Code', type: 'string', datafield: 'cad_code', width:90},
			{ text: 'Metal <br /> Type', type: 'string', datafield: 'metal_type', width:90},
			{ text: 'Profile', type: 'string', datafield: 'profile', width:120},
			{ text: 'Band <br /> Width', type: 'string', datafield: 'band_width', width:80},
			{ text: 'Band <br /> Thickness', type: 'string', datafield: 'band_thickness', width:100},
			{ text: 'Finish', type: 'string', datafield: 'finish', width:90},
			{ text: 'Fit <br /> Option', type: 'string', datafield: 'fit_option', width:100},
			{ text: 'Metal <br /> Thickness', type: 'string', datafield: 'metal_thickness', width:110},
			{ text: 'Price', type: 'string', datafield: 'price', width:90},
		]

	});

	//$("#jqxWidget").next('.pagerHTML').html($('#pagerjqxWidget'));	
	
	$("#"+gridId).bind('cellclick', function(event) {
		var current_index = event.args.rowindex;
		var current_column = event.args.column.datafield;
		var datarow = $("#"+gridId).jqxGrid('getrowdata', current_index);
		
		appInvoiceItems('invoice_items_body', 'weddingring', datarow);
		
		$('.lightBoxTitle .closePopup').click();
		
		// Use datarow for display of data in div outside of grid
	});
	
	$("#"+gridId).bind("sort", function (event) {
	    /* var sortinformation = event.args.sortinformation;
	    
	    var sortdatafield = sortinformation.sortcolumn;
	    
	    var sortorder = sortinformation.sortdirection.ascending ? "asc" : "desc";
	    
	    var sortUrl = '/ajaxgetdiamonds?sortdatafield='+sortdatafield+'&sortorder='+sortorder;*/
	    
	   //$.get(sortUrl, function(source){
	    	//$("#"+gridId).jqxGrid('databind', source);
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
 * Lookup for engagement ring
 * gridId - div id
 * recordsPerPage - records per page
 * keyword - search keyword
 */
function getEngRingLookup(gridId, recordsPerPage, keyword){
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
			{ name: 'metal_thickness' },
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
			{ text: 'Stock Code', type: 'string', datafield: 'stock_code', width:100},
			{ text: 'Supplier <br /> Name', type: 'string', datafield: 'supplier_name', width:100 },
			{ text: 'Ring <br /> Type', type: 'string', datafield: 'ring_type', width:90 },
			{ text: 'CAD <br /> Code', type: 'string', datafield: 'cad_code', width:80 },
			{ text: 'Metal <br /> Type', type: 'string', datafield: 'metal_type', width:90 },
			{ text: 'Profile', type: 'string', datafield: 'profile', width:100 },
			{ text: 'Band <br /> Width', type: 'string', datafield: 'band_width', width:90 },
			{ text: 'Band <br /> Thickness', type: 'string', datafield: 'band_thickness', width:120},
			{ text: 'Halo <br /> Width', type: 'string', datafield: 'halo_width', width:90},
			{ text: 'Halo <br /> Thickness', type: 'string', datafield: 'halo_thickness', width:100 },
			{ text: 'Head <br /> Settings', type: 'string', datafield: 'head_title', width:100},
			{ text: 'Claw <br /> Termination', type: 'string', datafield: 'claw_title', width:120},
			{ text: 'Setting <br /> Height', type: 'string', datafield: 'setting_height', width:90},
			{ text: 'Metal <br /> Thickness', type: 'string', datafield: 'metal_thickness', width:120},
			{ text: 'Price', type: 'string', datafield: 'price', width:90},
			{ text: 'Record <br /> Owner', type: 'string', datafield: 'owner_name', width:100},
		]

	});

	
	$("#"+gridId).bind('cellclick', function(event)  {
		var current_index = event.args.rowindex;
		var current_column = event.args.column.datafield;
		var datarow = $("#"+gridId).jqxGrid('getrowdata', current_index);
		
		appInvoiceItems('invoice_items_body', 'engring', datarow);
		
		$('.lightBoxTitle .closePopup').click();
	});
	
	$("#"+gridId).bind("sort", function (event) {
	    /* var sortinformation = event.args.sortinformation;
	    
	    var sortdatafield = sortinformation.sortcolumn;
	    
	    var sortorder = sortinformation.sortdirection.ascending ? "asc" : "desc";
	    
	    var sortUrl = '/ajaxgetdiamonds?sortdatafield='+sortdatafield+'&sortorder='+sortorder;*/
	    
	   //$.get(sortUrl, function(source){
	    	//$("#"+gridId).jqxGrid('databind', source);
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
 * Lookup for ear ring
 * gridId - div id
 * recordsPerPage - records per page
 * keyword - search keyword
 */
function getEarringLookup(gridId, recordsPerPage, keyword){
	if(keyword){
		var url = '/ajaxgetearrings?keyword='+keyword;
	} else {
		var url = '/ajaxgetearrings';
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
			{ name: 'ring_style' },
			{ name: 'metal_type' },
			{ name: 'profile' },
			{ name: 'metal_weight' },
			{ name: 'company_name' },
			{ name: 'supplier_name' },
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
			{ text: 'Stock Code', type: 'string', datafield: 'stock_code', width:100},
			{ text: 'Supplier <br /> Name', type: 'string', datafield: 'supplier_name', width:100 },
			{ text: 'Style', type: 'string', datafield: 'ring_style', width:80},
			{ text: 'CAD <br /> Stock Code', type: 'string', datafield: 'cad_code', width:110},
			{ text: 'Metal <br /> Type', type: 'string', datafield: 'metal_type', width:90 },
			{ text: 'Profile', type: 'string', datafield: 'profile', width:100 },
			{ text: 'Metal <br /> Weight', type: 'string', datafield: 'metal_weight', width:90},
			{ text: 'Band <br /> Width', type: 'string', datafield: 'band_width', width:90 },
			{ text: 'Band <br /> Thickness', type: 'string', datafield: 'band_thickness', width:110 },
			{ text: 'Price', type: 'string', datafield: 'price', width:90},
		]

	});

	
	$("#"+gridId).bind('cellclick', function(event)  {
		var current_index = event.args.rowindex;
		var current_column = event.args.column.datafield;
		var datarow = $("#"+gridId).jqxGrid('getrowdata', current_index);
		
		appInvoiceItems('invoice_items_body', 'earring', datarow);
		
		$('.lightBoxTitle .closePopup').click();
	});
	
	$("#"+gridId).bind("sort", function (event) {
	    /* var sortinformation = event.args.sortinformation;
	    
	    var sortdatafield = sortinformation.sortcolumn;
	    
	    var sortorder = sortinformation.sortdirection.ascending ? "asc" : "desc";
	    
	    var sortUrl = '/ajaxgetdiamonds?sortdatafield='+sortdatafield+'&sortorder='+sortorder;*/
	    
	   //$.get(sortUrl, function(source){
	    	//$("#"+gridId).jqxGrid('databind', source);
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
 * Lookup for pendant
 * gridId - div id
 * recordsPerPage - records per page
 * keyword - search keyword
 */
function getPendantLookup(gridId, recordsPerPage, keyword){
	if(keyword){
		var url = '/ajaxgetpendants?keyword='+keyword;
	} else {
		var url = '/ajaxgetpendants';
	}
	
	// prepare the data
	var source =
	{
		datatype: "json",
		datafields: [
			{ name: 'id' },
			{ name: 'stock_code' },
			{ name: 'head_settings' },
			{ name: 'setting_style' },
			{ name: 'halo_width' },
			{ name: 'halo_thickness' },
			{ name: 'description' },
			{ name: 'invoice' },
			{ name: 'price' },
			{ name: 'created_date' },
			{ name: 'ring_style' },
			{ name: 'metal_type' },
			{ name: 'profile' },
			{ name: 'metal_weight' },
			{ name: 'company_name' },
			{ name: 'supplier_name' },
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
			{ text: 'Stock Code', type: 'string', datafield: 'stock_code', width:100},
			{ text: 'Supplier <br /> Name', type: 'string', datafield: 'supplier_name', width:100},
			{ text: 'Style', type: 'string', datafield: 'ring_style', width:80},
			{ text: 'Metal <br /> Type', type: 'string', datafield: 'metal_type', width:90},
			{ text: 'Profile', type: 'string', datafield: 'profile', width:100},
			{ text: 'Metal <br /> Weight', type: 'string', datafield: 'metal_weight', width:90},
			{ text: 'Head <br /> Settings', type: 'string', datafield: 'head_settings', width:100},
			{ text: 'Setting <br /> Style', type: 'string', datafield: 'setting_style', width:100},
			{ text: 'Halo <br /> Width', type: 'string', datafield: 'halo_width', width:90},
			{ text: 'Halo <br /> Thickness', type: 'string', datafield: 'halo_thickness', width:100},
			{ text: 'Price', type: 'string', datafield: 'price', width:90},
			{ text: 'Record <br /> Owner', type: 'string', datafield: 'owner_name', width:100},
		]

	});

	
	$("#"+gridId).bind('cellclick', function(event)  {
		var current_index = event.args.rowindex;
		var current_column = event.args.column.datafield;
		var datarow = $("#"+gridId).jqxGrid('getrowdata', current_index);

		appInvoiceItems('invoice_items_body', 'pendant', datarow);
		
		$('.lightBoxTitle .closePopup').click();
	});
	
	$("#"+gridId).bind("sort", function (event) {
	    /* var sortinformation = event.args.sortinformation;
	    
	    var sortdatafield = sortinformation.sortcolumn;
	    
	    var sortorder = sortinformation.sortdirection.ascending ? "asc" : "desc";
	    
	    var sortUrl = '/ajaxgetdiamonds?sortdatafield='+sortdatafield+'&sortorder='+sortorder;*/
	    
	   //$.get(sortUrl, function(source){
	    	//$("#"+gridId).jqxGrid('databind', source);
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
 * Lookup for chain
 * gridId - div id
 * recordsPerPage - records per page
 * keyword - search keyword
 */
function getChainLookup(gridId, recordsPerPage, keyword){
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
			{ name: 'created_date' },
			{ name: 'company_name' },
			{ name: 'supplier_name' },
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
			{ text: 'Stock Code', type: 'string', datafield: 'stock_code', width:100},
			{ text: 'Supplier Name', type: 'string', datafield: 'supplier_name'},
			{ text: 'Style', type: 'string', datafield: 'style', width:90},
			{ text: 'Length', type: 'string', datafield: 'length', width:90},
			{ text: 'Thickness', type: 'string', datafield: 'thickness', width:100},
			{ text: 'Metal <br /> Type', type: 'string', datafield: 'metal_type', width:90},
			{ text: 'Metal <br /> Weight', type: 'string', datafield: 'metal_weight', width:90},
			{ text: 'Price', type: 'string', datafield: 'price', width:90},
			{ text: 'Record <br /> Owner', type: 'string', datafield: 'owner_name', width:100},
		]

	});

	
	$("#"+gridId).bind('cellclick', function(event)  {
		var current_index = event.args.rowindex;
		var current_column = event.args.column.datafield;
		var datarow = $("#"+gridId).jqxGrid('getrowdata', current_index);
		
		appInvoiceItems('invoice_items_body', 'chain', datarow);
		
		$('.lightBoxTitle .closePopup').click();
		
	});
	
	$("#"+gridId).bind("sort", function (event) {
	    /* var sortinformation = event.args.sortinformation;
	    
	    var sortdatafield = sortinformation.sortcolumn;
	    
	    var sortorder = sortinformation.sortdirection.ascending ? "asc" : "desc";
	    
	    var sortUrl = '/ajaxgetdiamonds?sortdatafield='+sortdatafield+'&sortorder='+sortorder;*/
	    
	   //$.get(sortUrl, function(source){
	    	//$("#"+gridId).jqxGrid('databind', source);
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
 * Lookup for miscellaneous
 * gridId - div id
 * recordsPerPage - records per page
 * keyword - search keyword
 */
function getMiscellaneousLookup(gridId, recordsPerPage, keyword){
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
			{ text: 'Stock Code', type: 'string', datafield: 'stock_code', width:100},
			{ text: 'Title', type: 'string', datafield: 'title', width:200 },
			{ text: 'Description', type: 'string', datafield: 'description'},
			{ text: 'Price', type: 'string', datafield: 'price', width:120},
			{ text: 'Record <br /> Owner', type: 'string', datafield: 'owner_name', width:130},
		]

	});
	
	$("#"+gridId).bind('cellclick', function(event)  {
		var current_index = event.args.rowindex;
		var current_column = event.args.column.datafield;
		var datarow = $("#"+gridId).jqxGrid('getrowdata', current_index);
		
		appInvoiceItems('invoice_items_body', 'misc', datarow);
		
		$('.lightBoxTitle .closePopup').click();
	});
	
	$("#"+gridId).bind("sort", function (event) {
	    /* var sortinformation = event.args.sortinformation;
	    
	    var sortdatafield = sortinformation.sortcolumn;
	    
	    var sortorder = sortinformation.sortdirection.ascending ? "asc" : "desc";
	    
	    var sortUrl = '/ajaxgetdiamonds?sortdatafield='+sortdatafield+'&sortorder='+sortorder;*/
	    
	   //$.get(sortUrl, function(source){
	    	//$("#"+gridId).jqxGrid('databind', source);
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
	
	if($('#item_type_'+type+'_'+data.id).length > 0){
		var quantity = parseInt($('#qty_'+type+'_'+data.id).val()) + 1;
		var total_price = quantity * parseFloat(data.price).toFixed(2);
		
		$('#qty_'+type+'_'+data.id).val(quantity);		
		$("#total_price_"+type+'_'+data.id).text('$'+total_price);
	}else{
		$('#showInvoiceItem').show();
		var html = '<tr id="item_tr_'+type+'_'+data.id+'"><td><input type="hidden" name="item_type_'+type+'_'+data.id+'" id="item_type_'+type+'_'+data.id+'" value="'+type+'"><input type="hidden" name="item_id_'+type+'_'+data.id+'" id="item_id_'+type+'_'+data.id+'" value="'+data.id+'"></td><td>';
		var description = '';
		
		if(type == 'diamond'){
			
			description = 'Stock Code: '+data.stock_code+', Cert No.: '+data.cert_no+', Cert URL: '+data.cert_url+', Video URL: '+data.video_url+', Cut: '+data.cut+', Depth: '+data.depth+', Table: '+data.table+', Flurosence: '+data.flurosence+', Measurement: '+data.measurement+', Description: '+data.description+', Color: '+data.color+', Shape: '+data.shape+', Polish: '+data.polish+', Symmetry: '+data.symmetry+', Intensity: '+data.intensity+', Overtone: '+data.overtone+', Lab: '+data.lab+', Supplier Name: '+data.supplier_name;
			
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
			description = 'Stock Code: '+data.stock_code+', Title: '+data.title+', Description: '+data.description+', Supplier Name: '+data.supplier_name;
			html = html + 'Miscellaneous</td><td><textarea name="item_desc_'+type+'_'+data.id+'" id="item_desc_'+type+'_'+data.id+'" class="cmntextarea textareaGlobalMaxLength">'+description+'</textarea>';
		}

		var html = html + '</td><td><input type="text" onkeyup="validateInteger(this);" onblur="itemTotal('+data.id+', \''+data.price+'\', \''+type+'\');" name="qty_'+type+'_'+data.id+'" id="qty_'+type+'_'+data.id+'" class="cmnInput" value="1" /></td><td>$'+parseFloat(data.price).toFixed(2)+'</td><td><input type="text" onkeyup="validateFloat(this);" onblur="itemTotal('+data.id+', \''+data.price+'\', \''+type+'\');" class="cmnInput" name="discount_'+type+'_'+data.id+'" id="discount_'+type+'_'+data.id+'" /></td><td><input type="text" name="acc_'+type+'_'+data.id+'" id="acc_'+type+'_'+data.id+'" class="cmnInput textboxGlobalMaxLength" /></td><td id="total_price_'+type+'_'+data.id+'">$'+parseFloat(data.price).toFixed(2)+'</td><td><a href="javascript:;" onclick="$(\'#item_tr_'+type+'_'+data.id+'\').remove();" class="deleteItem deleteInvoiceRow">Delete</a></td></tr>';
		
		$('#'+containerId).append(html);
		globalTextAreaMaxLength('textareaGlobalMaxLength');
		globalTextboxMaxLength('textboxGlobalMaxLength');
	}
}

/**
 * Creace summarized html for quotes
 */
function getQuotesSummary(){
	var html = '';
	var subTotal = 0;
	var total = 0;
	var qty = 0;
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
				//subTotal = subTotal + (parseFloat($(this).text().replace('$', '')) * qty);
				html = html + '<td>'+$(this).text()+'</td>';
                html += "<input type='hidden' name='sub[]' value='"+$(this).text()+"' />";
			}else if(index == 5){
				html = html + '<td>'+$(this).find('input').val()+'</td>';
                html += "<input type='hidden' name='discount[]' value='"+$(this).find('input').val()+"' />";
			}else if(index == 6){
				html = html + '<td>'+$(this).find('input').val()+'</td>';
                html += "<input type='hidden' name='account[]' value='"+$(this).find('input').val()+"' />";
			}else if(index == 7){
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
	var disc = $(elem).val().replace(/([^0-9\.])/g, "");
	if(disc != '')
		 disc = parseFloat(disc).toFixed(2);
	$(elem).val(disc);
}

/**
 * calculate total value
 */
function itemTotal(item_id, unit_price, type){
	var disc = $('#discount_'+type+'_'+item_id).val();
	var qty = $('#qty_'+type+'_'+item_id).val();
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
			{ name: 'payment_mode' },
            { name: 'date_due' },
			//{ name: 'send_email' },
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
		if(datarow)
			html = '<div style="margin-top:12px;"><a class="cmnBtn vm marT0" href="javascript:;">Send</a> &nbsp; <span class="emailSentIcon"></span> &nbsp; 12/12/14</div>';
			
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
			{ text: 'Payment <br /> Made', type: 'string', datafield: 'payment_mode', width:120},
            { text: 'Date Due', type: 'string', datafield: 'date_due', width:170},
			//{ text: 'Send <br /> email', type: 'string', cellsrenderer: emailStatus, width:200},
			{ text: 'Options', type: 'string', cellsrenderer: selectOptions, width:150},			
		]
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

$(document).on('click','.invoiceOptions', function(){
	$('.editoptiosBlock').remove();
	
	var deleteLink = $(this).attr('data-delete');
	var duplicateLink = $(this).attr('data-duplicate');
	//var copyLink = $(this).attr('data-copy');
	
	var thisleftPos = $(this).offset().left-400;
	var thistopPos = ($(this).offset().top + $('section.rightCol').scrollTop())-20;
	
	$('section.rightCol').append('<ul style="left:'+thisleftPos+'px;top:'+thistopPos+'px;" class="showWithAimat editoptiosBlock"><li><a href="javascript: confirmDelete(\''+deleteLink+'\');">Delete</a></li><li><a href="'+duplicateLink+'">Duplicate</a></li><li><a href="javascript:;">Create Order</a></li></ul>');	
});

$(document).on('click','.quoteOptions', function(){
	$('.editoptiosBlock').remove();
	
	var deleteLink = $(this).attr('data-delete');
	var duplicateLink = $(this).attr('data-duplicate');
	var copyLink = $(this).attr('data-copy');
	
	var thisleftPos = $(this).offset().left-400;
	var thistopPos = ($(this).offset().top + $('section.rightCol').scrollTop())-20;
	
	$('section.rightCol').append('<ul style="left:'+thisleftPos+'px;top:'+thistopPos+'px;" class="showWithAimat editoptiosBlock"><li><a href="javascript: confirmDelete(\''+deleteLink+'\');">Delete</a></li><li><a href="'+duplicateLink+'">Duplicate</a></li><li><a href="'+copyLink+'">Copy Items to Invoice</a></li></ul>');
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
function getInvoices(gridId, recordsPerPage, keyword){
	var keyword = $("#invoice_searchInput").val();
	if(keyword != '' && keyword != undefined){
		var url = '/ajax-invoice?keyword='+keyword;
	} else {
		var url = '/ajax-invoice';
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
			//{ name: 'send_email' },
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
			html = '<a href="javascript:;" data-delete="/deleteinvoice/'+datarow.id+'" data-duplicate="/duplicateinvoice/'+datarow.id+'" class="editOptions invoiceOptions"></a>';
		return html;		
	}
	
	var emailStatus = function(row, columnfield, value, defaulthtml, columnproperties){
		var datarow = $("#"+gridId).jqxGrid('getrowdata', row);
		var html = '';
		if(datarow)
			html = '<div style="margin-top:12px;"><a class="cmnBtn vm marT0" href="javascript:;" onclick="emailInvoice('+datarow.id+', \''+gridId+'\');">Send</a> &nbsp; <span class="emailSentIcon"></span> &nbsp; 12/12/14</div>';
			
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
			{ text: 'Payment <br /> Made', type: 'string', datafield: 'payment_mode', width:120},
            { text: 'Date Due', type: 'string', datafield: 'date_due', width:120},
			{ text: 'Send <br /> email', type: 'string', cellsrenderer: emailStatus, width:200},
			{ text: 'Options', type: 'string', cellsrenderer: selectOptions, width:100},
		]
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

function emailInvoice(invoice_id, gridId){
	var url = '/emailinvoice';
	$.ajax({
		url: url,
		data: {invoice_id: invoice_id},
		type: 'post',
		success: function(response){
			$("#"+gridId).jqxGrid('updatebounddata', 'filter');
		}
	});
}