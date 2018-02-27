/**
 * Populate JQXGrid for user list
 */
function geUsers(recordsPerPage, keyword){
	if(keyword){
		var url = '/ajaxuserlist?keyword='+keyword;
	} else {
		var url = '/ajaxuserlist';
	}
	InitGrid();
	function InitGrid() {
		$('#jqxUsers').remove();
        $('#bindAfterThis').after('<div class="formTable manageMembers" id="jqxUsers"></div>');
	}
	// prepare the data
	var source =
	{
		datatype: "json",
		datafields: [
			{ name: 'user_id' },
			{ name: 'title' },
			{ name: 'first_name' },
			{ name: 'last_name' },
			{ name: 'email' },
			{ name: 'mobile_number' },
			{ name: 'address1' },
			{ name: 'address2' },
			{ name: 'price' },
			{ name: 'state' },
			{ name: 'ciuntry' },
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
			$("#jqxUsers").jqxGrid('updatebounddata', 'filter');
		}
	};	
	
	var pageable = true, sortable = true;
	var dataAdapter = new $.jqx.dataAdapter(source, {
		downloadComplete: function (data, status, xhr) {},
		loadComplete: function (data) { 
			if($("#jqxUsers").find('.jqx-grid-empty-cell').length>0 ){					
					if($("#jqxUsers").find('.jqx-grid-empty-cell').parent('div').height()<30){
						$('.jqx-grid-empty-cell').parent('div').addClass('norecordsFoundRow');
						$('.jqx-grid-empty-cell').closest("#jqxUsers").addClass('noInfoFound');
					}
					if(keyword){
						$("#jqxUsers").find('.jqx-grid-empty-cell >span').text("No matches were found");
						//$('#searchResultsHint').text("No matches were found");
					} else {
						$("#jqxUsers").find('.jqx-grid-empty-cell >span').text("No records found");
					}
			}else{
				if($("#jqxUsers").hasClass('noInfoFound')){
					$("#jqxUsers").removeClass('noInfoFound');
				}
			}
		},
		loadError: function (xhr, status, error) { }
	});
	
	var edit = function(row, columnfield, value, defaulthtml, columnproperties){
		var datarow = $('#jqxUsers').jqxGrid('getrowdata', row);
		var html_del = '';
		var html_edit = ''
		if(datarow){
			html_edit = '<a href="javascript:;" data-popup="userForm" class="cmnBtn lightBoxClick fl marR10" onclick="openUserForm('+datarow.user_id+')">Edit </a>';
			html_del = '<a href="javascript:;" class="cmnBtn lightBoxClick fl" onclick="DeleteUser('+datarow.user_id+')">Delete</a>';
		}
		return  html_edit + html_del;
	};
		
	$("#jqxUsers").jqxGrid(
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
			{ text: 'First Name', type: 'string', datafield: 'first_name', width:'20%' },
			{ text: 'Last Name', type: 'string', datafield: 'last_name', width:'20%' },
			{ text: 'Email', type: 'string', datafield: 'email',},
			{ text: 'Mobile', type: 'string', datafield: 'mobile_number', width:'180' },
			{ text: 'Action', type: 'string', cellsrenderer: edit, cellsalign: 'right', width:'150'},
		]

	});

	
	/*$('#jqxUsers').bind('cellclick', function(event)  {
		var current_index = event.args.rowindex;
		var current_column = event.args.column.datafield;
		var datarow = $('#jqxUsers').jqxGrid('getrowdata', current_index);
		var url = '/ajaxuserlist/'+datarow.id;
		//alert(current_column);
		if(current_column != null){
			$(location).attr('href', url);
		}
		$('#gridTypeId').val(datarow.id);
		$('#gridType').val('earring');
	});*/
	
	$("#jqxUsers").bind("sort", function (event) {
	    /*var sortinformation = event.args.sortinformation;
	    
	    var sortdatafield = sortinformation.sortcolumn;
	    
	    var sortorder = sortinformation.sortdirection.ascending ? "asc" : "desc";
	    
	    var sortUrl = '/ajaxuserlist?sortdatafield='+sortdatafield+'&sortorder='+sortorder;*/
	    
	   //$.get(sortUrl, function(source){
	    	//$("#jqxUsers").jqxGrid('databind', source);
	    	$("#jqxUsers").jqxGrid('updatebounddata', 'filter');
	   //});
	});
	
	$("#jqxUsers").bind("pagesizechanged", function (event) {
		var args = event.args;
		var pagenumber = args.pagenum;
		var pagesize = args.pagesize;
		$('#pageSizeGrabing').val(pagesize);
	});
}

/**
 * Populate user add / edit form
 */
function openUserForm(user_id){
	$.ajax({
		type: 'POST',
		url: '/userform',
		//async: false,
		data: {user_id: user_id},
		beforeSend:function(){
			$('#user_form_content').html('<div class="ajaxLoader"><div class="pageLoader"></div></div>');
		},
		success: function(response){
			$('#user_form_content').html(response);
			lightboxmid();
			$('.dropdown').dropkick('refresh');
			$(".pureNumaric").ForcePureNumericOnly();
		}
	});
}
/* Delete User*/
function DeleteUser(user_id){
	if (confirm("Are you sure you want to delete User")) {
        location.href = "/deleteuser/"+user_id;
		
    } else {
		return false;
	}
	
	}
	
	

/**
 * Validate add / edit user form submit
 */
function validateUserForm(form){
	var errors = 0;	
	$('.errorText').remove();
	
	if($('#first_name').val() == ''){
		$( '<p class="errorText">Please enter first name</p>' ).insertAfter( '#first_name' );
		errors++;
	}
	
	if($('#last_name').val() == ''){
		$( '<p class="errorText">Please enter last name</p>' ).insertAfter( '#last_name' );
		errors++;
	}
	
       
        if($('#image').val() == ''){
		$( '<p class="errorText">Please Upload Image</p>' ).insertAfter( '#image' );
               return ;
		errors++;
	}
        
	if($('#mobile_number').val() == ''){
		$( '<p class="errorText">Please enter mobile number</p>' ).insertAfter( '#mobile_number' );
		errors++;
	}else if(isNaN($('#mobile_number').val()) || $('#mobile_number').val().indexOf('.') > 0){
		$( '<p class="errorText">Please enter numbers only</p>' ).insertAfter( '#mobile_number' );
		errors++;
	}
	
	if($('#email').length > 0 && ($('#email').val() == '' || !validateEmail($('#email').val()))){
		if($('#email').val() == ''){
			$('<p class="errorText">Please enter email address</p>').insertAfter('#email');
		} else {
			$('<p class="errorText">Please enter valid email address</p>').insertAfter('#email');
		}
		errors++;
	}
	
	if ($('#password').attr('required') && $('#password').val() == '') {
		$( '<p class="errorText">Please enter a password</p>' ).insertAfter( '#password' );
		errors++;
	}
	if($('#password').val() != '' && $.trim($('#password').val()).length < 4){
		$( '<p class="errorText">Password should be at least 4 characters long</p>' ).insertAfter( '#password' );
		errors++;
	}
	
	if($('#role_id').length > 0 && $('#role_id').val() == 0){
		$( '<p class="errorText">Please select a role</p>' ).insertAfter( '#role_id' );
		errors++;
	}
	
	if($('#email').length > 0 && errors == 0){
		$.ajax({
			type: 'POST',
			url: '/checkduplicateemail',
			async: false,
			data: form.serialize(),
			success: function(response){
				if(response > 0){
					$( '<p class="errorText">Email Id already exists</p>' ).insertAfter( '#email' );
					errors++;
				}
			}
		});
	}
	
	return errors;
}

/**
 * Make ajax call to store user data in db
 */
function saveUser (form){
	var errors = validateUserForm(form);
	//alert(errors);
	$('#save').attr('disabled','disabled');
	
	if(errors == 0){
		var url = '/saveuser';
                var img=$('#image').val();
               //'frm_diamond': $('#frm_diamond').serialize(),
		var data = $(form).serialize();              
                
                var data =
		$.ajax({
			url: url,
			type: 'POST',
			data: data,
			async: false,
			success: function(response){
				//if(response > 0){
					$(form)[0].reset();
					$('#userForm .closePopup').click();
					$('#jqxUsers').jqxGrid('updatebounddata');
				//}
				$(form)[0].reset();
			}
		});
	}
	
	$('#save').removeAttr('disabled');
}

/**
 * Valiate master password form
 */
 
function validateMasterPasswordForm(){
	var errors = 0;	
	$('.errorText').remove();
	
	if($('#mp_password').val() == ''){
		$( '<p class="errorText">Please enter Password</p>' ).insertAfter( '#mp_password' );
		errors++;
	}else if($('#mp_password').val().length < 4){
		$( '<p class="errorText">Password should be at least 4 characters long</p>' ).insertAfter( '#mp_password' );
		errors++;
	}
	
	if($('#mp_confirm_password').val() == ''){
		$( '<p class="errorText">Please enter confirm password</p>' ).insertAfter( '#mp_confirm_password' );
		errors++;
	}
	
	if(errors== 0 && $('#mp_password').val() != $('#mp_confirm_password').val()){
		$( '<p class="errorText">Password and Confirm Password does not match</p>' ).insertAfter( '#mp_confirm_password' );
		errors++;
	}
	
	return errors;
}

/**
 * Make ajax call to store master password in db
 */
function saveMasterPassword (form){
	var errors = validateMasterPasswordForm(form);
	//alert(errors);
	$('#mp_save').attr('disabled','disabled');
	
	if(errors == 0){
		var url = '/setmasterpass';
		var data = $(form).serialize();
		$.ajax({
			url: url,
			type: 'POST',
			data: data,
			async: false,
			success: function(response){
				//if(response > 0){
					$(form)[0].reset();
					$('#masterPassword .closePopup').click();
				//}
				$(form)[0].reset();
			}
		});
	}
	
	$('#mp_save').removeAttr('disabled');
}


//Update UserStatus
//For Example User is Available or not
function updateUserStatus(status)
{
      $.ajax({
		
            type: 'POST',
		url: '/ajaxUpdateUserStatus',
		//async: false,
	   data: {status: status},
	   success: function(response){
			 alert("Success");
		},        
           error: function(result) {
                   alert("Error");
        }
	});
    
}