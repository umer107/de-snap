// JavaScript Document
$(document).ready(function () {

    jQuery(".dropdown").dropkick({
        mobile: true
    });
    /*if($('.datepickerInput').length>0){
     jQuery('.datepickerInput').Zebra_DatePicker({
     format: 'd-m-Y',
     onChange: function(view, elements) {
     var thisIbling=jQuery(this).siblings('.Zebra_DatePicker_Icon'); 
     jQuery('.Zebra_DatePicker').css('opacity','0'); 
     setTimeout(function(){
     jQuery('.Zebra_DatePicker').animate({opacity:1},300);
     var datepickerTopPos=thisIbling.offset().top+38;
     var datepickerLeftPos=thisIbling.offset().left;
     jQuery('.Zebra_DatePicker').css({'top':datepickerTopPos,'left':datepickerLeftPos})	
     })
     }
     });
     }*/



    if ($('.datepickerInput').length > 0) {
        $('.datepickerInput').datepicker({
            dateFormat: jsDateFormat,
            changeYear: true,
            yearRange: "-100:+0",
            beforeShow: function () {
                $(this).after($(this).datepicker("widget"));
            }
        });


    }
	
	if ($('.dateTimepickerInput').length > 0) {
		$('.dateTimepickerInput').datetimepicker({
            dateFormat: jsDateFormat,
            changeYear: true,
            yearRange: "-100:+0",
            beforeShow: function () {
                $(this).after($(this).datetimepicker("widget"));
            }
        });
	}

    $('body').on('click', '.datePickCal', function () {
        $(this).siblings('.datepickerInput').focus();
    });

    /* Task Date Picker Cal*/
    $('body').on('click', '.taskDatePicker .taskpicker', function () {
        var datePicker = $('.datePickerPopup');
        $(this).after(datePicker);
        $('.datePickerPopup').show();
    });

    jQuery("body").on('click', function (event) {
        var $target = jQuery(event.target);
        if (!$target.parents().is(".taskDatePicker") && !$target.is(".taskpicker")) {
            $('.datePickerPopup').appendTo('.tasksBlock');
            $('.datePickerPopup').hide();
        }
    });

    /* Task Date Picker Cal End*/

    $('body').on('focus', '.mainSearch .searchInput', function () {
        $(this).closest('.mainSearch').addClass('thisFocus');
    });
    $('body').on('blur', '.mainSearch .searchInput', function () {
        $(this).closest('.mainSearch').removeClass('thisFocus');
    });


    if ($('.datepickerInput2').length > 0) {
        $('.datepickerInput2').simplePicker({
            // add style to datepicker
            style: 'dark',
            // set first day in a week 0 = Sunday, 1 = Monday ...
            firstday: 1,
            // deckare names of the days
            days: ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'],
            // deckare names of the months
            months: ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'],
            // set delimiter
            delimiter: '/',
            dateformat: function () {
                // set dateformat
                return 'dd' + this.delimiter + 'mm' + this.delimiter + 'yyyy';
            }
        });

        jQuery("body").on('click', function (event) {
            var $target = jQuery(event.target);
            if (!$target.parents().is(".headerSection") && !$target.is(".datepickerInput2") && !$target.is(".previ") && !$target.is(".nexti")) {
                $('.datePickerHolder-dark').hide();
            }
        });

    }

    /* Customer Details page links */
    $('.editInfo').on('click', function () {
        $(this).siblings('.saveInfo').show().end().hide();
        $(this).closest('.editCustomerForm').find('.editViewField').show();
        $(this).closest('.editCustomerForm').find('.editViewField + .datePickCal').show();
        $(this).closest('.editCustomerForm').find('.inputVal').hide();
        $('#profile_photo_uploader-button').show();
        if ($(this).parent('form').attr('id') == 'frm_customer') {
            $('.cstmrDetails').addClass('editMode');
            $('.cstmrPhoto').find('.uploadify-button-text').text("Upload Photo")
        }
    });

    /*$('.saveInfo').on('click', function(){
     $(this).siblings('.editInfo').show().end().hide();
     $(this).closest('.editCustomerForm').find('.editViewField').hide();
     $(this).closest('.editCustomerForm').find('.editViewField + .datePickCal').hide();
     $(this).closest('.editCustomerForm').find('.inputVal').show();
     }); 
     
     
     $('.unAssignPartner').on('click', function(){
     $(this).closest('.editCustomerForm').find('.assignPartner').show();
     $(this).closest('.editCustomerForm').find('.cusomerViewBlock').addClass('disabledMode');
     $(this).parent('.assignPartnerEditBtns').hide();
     
     });
     */


    /* Customer Details page links  End*/

    /* Customer Tabbing Script */
    $('.ctb > ul >li').on('click', function () {
        $(this).siblings('li').removeClass('current').end().addClass('current');
        $('.customTabbingInner >.customerTabingInfo').hide();
        $('.customTabbingInner >.customerTabingInfo').eq($(this).index()).show();
        if ($('.customTabbingInner >.customerTabingInfo').eq($(this).index()).hasClass('tasksView')) {
            tasksH();
        }
    });
    /* Customer Tabbing Script End */

    /* Collapse Link */
    $('.toggleListView').on('click', function () {
        if ($(this).siblings('.toggleInfo').is(':hidden')) {
            $(this).find('.showNhide').text('Hide').end().find('i').addClass('hI');
            $(this).siblings('.toggleInfo').slideDown();
        } else {
            $(this).find('.showNhide').text('Show').end().find('i').removeClass('hI');
            $(this).siblings('.toggleInfo').slideUp();
        }
    });
    /* Collapse Link End */

    /* AssignDrop */
    $("body").on('click', '.dropValue', function () {
        if ($(this).siblings('ul').is(':hidden')) {
            //$(this).find('.showNhide').text('Hide').end().find('i').addClass('hI');
            $(this).siblings('ul').show().addClass('showWithAimat');
        } else {
            //$(this).find('.showNhide').text('Show').end().find('i').removeClass('hI');
            $(this).siblings('ul').hide().removeClass('showWithAimat');
        }
    });

    $("body").on('click', '.assignedDrop > ul >li', function () {
        var thisHTML = $(this).html();
        $(this).siblings('li').removeClass('selected').end().addClass('selected');
        $(this).parent().siblings('.dropValue').html(thisHTML);
        $(this).parent().hide().removeClass('showWithAimat');
    });

    $("body").on('click', function (event) {
        var $target = jQuery(event.target);
        if (!$target.parents().is(".assignedDrop") && !$target.is(".dropValue")) {
            $('.assignedDrop >ul').hide().removeClass('showWithAimat');
        }
    });

    /* AssignDrop End */

    /* editDrop */
    $(document).on('click', '.editDrop >.editTaskBtn', function () {
        $('.editDrop >.editTaskBtn').siblings('ul').hide().removeClass('showWithAimat');
        $('.editDrop').css('z-index', '99');
        if ($(this).siblings('ul').is(':hidden')) {
            //$(this).find('.showNhide').text('Hide').end().find('i').addClass('hI');
            $(this).siblings('ul').show().addClass('showWithAimat');
            $(this).parent('.editDrop').css('z-index', '9999');
			if($('#quote-grid').length > 0){
				$('#quote-grid').find('.jqx-grid-cell').css('z-index', '999');
				$(this).closest('.jqx-grid-cell').css('z-index', '99999');
			}
			if($('#invoice-grid').length > 0){
				$('#invoice-grid').find('.jqx-grid-cell').css('z-index', '999');
				$(this).closest('.jqx-grid-cell').css('z-index', '99999');
			}
        } else {
            //$(this).find('.showNhide').text('Show').end().find('i').removeClass('hI');
            $(this).siblings('ul').hide().removeClass('showWithAimat');
            $('.editDrop').css('z-index', '99');
			
        }
    });

    $('.assignedDrop > ul >li').on('click', function () {
        //var thisHTML = $(this).html()
        //$(this).parent().siblings('.dropValue').html(thisHTML);
        $(this).parent().hide().removeClass('showWithAimat');
        $('.editDrop').css('z-index', '99');
    })

    jQuery("body").on('click', function (event) {
        var $target = jQuery(event.target);
        if (!$target.parents().is(".editDrop") && !$target.is(".editTaskBtn")) {
            $('.editDrop >ul').hide().removeClass('showWithAimat');
            $('.editDrop').css('z-index', '99');
        }
    });

    /* editDrop End */



    /* Task Title Edit */
    $('.taslTitle > p').on('click', function () {
        var thisTxt = $(this).text();
        $(this).siblings('.taskTitleEdit').show().val(thisTxt).focus().end().hide();
    });
    $('.taslTitle > .taskTitleEdit').on('blur', function () {
        var thisTxt = $(this).val();
        $(this).siblings('p').show().text(thisTxt).end().hide();
    });
    /* Task Title Edit End */



/// Common light box click function/////
    $('body').on('click', '.lightBoxClick', function () {
        //$('body').css({'overflow':'hidden','padding-right':'15px'});
        $("#" + $(this).attr('data-popup')).show().animate({opacity: 1}, 300);
        $("#" + $(this).attr('data-popup')).find();
        var winHeight = parseInt($(window).height());
        var thisHeight = parseInt($("#" + $(this).attr('data-popup') + ' > .lightBoxMid > .lightBoxContent').outerHeight()) + 38;
        var selectHeight = parseInt(thisHeight) + 30;

        if (selectHeight > winHeight) {
            var realheight = parseInt(winHeight) - 78;
            /*$("#" + $(this).attr('data-popup')+ ' >  .lightBoxMid > .lightBoxContent').css({top: '70px', 'height': realheight});
             $("#" + $(this).attr('data-popup')).find('.lightBoxTitle').css('top', '20px');*/
            $("#" + $(this).attr('data-popup') + ' >  .lightBoxMid > .lightBoxContent').css({top: '58px'});
            $("#" + $(this).attr('data-popup')).find('.lightBoxTitle').css({top: '20px'});
        } else if (selectHeight <= winHeight) {
            var popupHeight = (parseInt(winHeight) - parseInt(thisHeight)) / 2;
            var topPos = parseInt(popupHeight) + 38;
            /*$("#" + $(this).attr('data-popup')+ ' > .lightBoxMid > .lightBoxContent').css({'top': topPos});
             $("#" + $(this).attr('data-popup')).find('.lightBoxTitle').css('top', popupHeight);*/
            $("#" + $(this).attr('data-popup') + ' > .lightBoxMid > .lightBoxContent').css({'top': topPos + 'px'});
            $("#" + $(this).attr('data-popup')).find('.lightBoxTitle').css({top: popupHeight + 'px'});
        }
    });
    /// Common light box click function End/////
    $('.lightBoxTitle .closePopup').on('click', function () {
        var thisElm = $(this);
        //$('body').css({'overflow':'auto','padding-right':'0px'});
        //$('.lightBoxTitle,.lightBoxContent').css({top:'70px'});
        $(thisElm).closest('.lightBox').animate({opacity: 0}, 300, function () {
            $(thisElm).closest('.lightBox').hide();
        });
    });

///Main light box End//////////


	

});


jQuery(document).on('click', '.invoicetabing > li', function(){
	jQuery(this).addClass('active').siblings('li').removeClass('active');
	jQuery(this).parent('ul').siblings('.invoiceTabingInner').find('.invoiceTabingContent').hide();
	jQuery(this).parent('ul').siblings('.invoiceTabingInner').find('.invoiceTabingContent').eq(jQuery(this).index()).show();
})

$(document).on('click', '.expandLeftPannel', function () {
    if ($('.mainWrapper  aside.leftCol').css('left') == '-260px') {
        $('.mainWrapper  aside.leftCol').animate({left: 0});
        $(this).animate({left: '240px'});

    } else {
        $('.mainWrapper  aside.leftCol').animate({left: '-260px'});
        $(this).animate({left: '0'});
    }
})



///Main light box come mid to the window//////////
function lightboxmid() {
    if ($('.lightBox').length > 0) {
        $('.lightBoxContent').css('height', 'auto');
        $('.lightBox').each(function () {
            if ($(this).css('display') == 'block' || $(this).css('display') == 'inline') {
                var thisElm = $(this).children('.lightBoxMid').children('.lightBoxContent');
                var winHeight = parseInt($(window).height());

                var thisHeight = parseInt($(thisElm).outerHeight()) + 38;
                var selectHeight = parseInt(thisHeight) + 30;

                if (selectHeight > winHeight) {
                    var realheight = parseInt(winHeight) - 78;
                    $(thisElm).css({'top': '58px'});
                    $(this).find('.lightBoxTitle').css('top', '20px');

                } else if (selectHeight < winHeight) {
                    var popupHeight = (parseInt(winHeight) - parseInt(thisHeight)) / 2;
                    var topPos = parseInt(popupHeight) + 38;
                    $(thisElm).css({'top': topPos});
                    $(this).find('.lightBoxTitle').css('top', popupHeight);

                }
            }

        });

    }
}


///Main light box come mid to the window//////////
$(window).resize(function () {
    lightboxmid();


    if ($('.customTabbingInner >.tasksView').is(':visible')) {
        tasksH();
    }


});

function tasksH() {
    var contentINH = parseInt($('.contentArea > .contentINN').height() + $('.customerTabing > ul').height()) + 100;
    var winH = $(window).height();
    if (winH > contentINH) {
        var taskH = winH - contentINH;
        $('.tasksBlock .taskCulmnInn').css({'max-height': taskH + 'px', 'min-height': taskH + 'px'});
    } else {
        $('.tasksBlock .taskCulmnInn').css({'max-height': '100px'});
    }
}




function getCount(gridType, customerId) {
    var url = '/ajaxrecordscount';
    var data = 'grid_type=' + gridType + '&customerId=' + customerId;
    $.post(url, data, function (response) {
        var countList = JSON.parse(response);
        if (gridType == 'customers') {
            $('#customersCountGlobal').html(countList['customers']);
        }
        if (gridType == 'leads') {
            $('#leadsCountGlobal').html(countList['leads']);
        }
        if (gridType == 'opportunities') {
            if (customerId) {
                $('#oppCustomerCountGlobal').html(countList['opportunities']);
            } else {
                $('#opportunitiesCountGlobal').html(countList['opportunities']);
            }
        }
        if (gridType == 'suppliers') {
            $('#suppplierCountGlobal').html(countList['suppliers']);
        }
		/*if (gridType == 'inventory') {
			$('#inventoryDiamondsGlobal').html(countList['inventory_diamonds']);
            $('#inventoryWeddingringsGlobal').html(countList['inventory_weddingrings']);
            $('#inventoryEngagementringsGlobal').html(countList['inventory_engagementrings']);
            $('#inventoryEarringsGlobal').html(countList['inventory_earrings']);
            $('#inventoryPendantsGlobal').html(countList['inventory_pendants']);
            $('#inventoryMiscellaneousGlobal').html(countList['inventory_miscellaneous']);
            $('#inventoryChainsGlobal').html(countList['inventory_chains']);
			$('#inventoryTotalGlobal').html(countList['inventory_total']);
        }*/
        if (gridType == 'gridType') {
            $('#customersCountGlobal').html(countList['customers']);
            $('#leadsCountGlobal').html(countList['leads']);
            $('#opportunitiesCountGlobal').html(countList['opportunities']);
            $('#suppplierCountGlobal').html(countList['suppliers']);
			$('#leftPanelOrderCountGlobal').html(countList['ordersList_total']);
			$('#inventoryTotalGlobal').html(countList['inventory_total']);
			$('#leftPanelQuotesnInviocesCountGlobal').html(countList['quoteInvoices_total']);
			$('#leftPanelUserCountGlobal').html(countList['userList_total']);
			/*$('#inventoryDiamondsGlobal').html(countList['inventory_diamonds']);
			$('#inventoryWeddingringsGlobal').html(countList['inventory_weddingrings']);
			$('#inventoryEngagementringsGlobal').html(countList['inventory_engagementrings']);
			$('#inventoryEarringsGlobal').html(countList['inventory_earrings']);
			$('#inventoryPendantsGlobal').html(countList['inventory_pendants']);
			$('#inventoryMiscellaneousGlobal').html(countList['inventory_miscellaneous']);
			$('#inventoryChainsGlobal').html(countList['inventory_chains']);*/
        }
    });
}

$(document).ready(function () {
	//Making numaric value entry by user
	loadBasicValidations();
});

// Mobile restricted to numbers only
$(document).on('keydown', 'input[data-numeric]', function (e) {	
	// Allow: backspace, delete, tab, escape and enter
	if ($.inArray(e.keyCode, [46, 8, 9, 27, 13]) !== -1 ||
			// Allow: Ctrl+A
					(e.keyCode == 65 && e.ctrlKey === true) ||
					// Allow: home, end, left, right, down, up
							(e.keyCode >= 35 && e.keyCode <= 40) ||
								// Allow: . (dot)
								($.inArray(e.keyCode, [190, 110]) !== -1 && $(this).val().indexOf('.') == -1)) {
				// let it happen, don't do anything
				return true;
	}
	
	// Ensure that it is a number and stop the keypress
	if ((e.shiftKey || (e.keyCode < 48 || e.keyCode > 57)) && (e.keyCode < 96 || e.keyCode > 105)) {
		e.preventDefault();
	}
});

$(document).on('keyup', 'input[data-numeric]', function (e) {
	$(this).val($(this).val().replace(/[^0-9.]/g,''));
});


function validateEmail($email) {
    var emailReg = /^([\w-\.]+@([\w-]+\.)+[\w-]{2,4})?$/;
    if (!emailReg.test($email))
        return false;
    else
        return true;
}

function searchByEnterKey(fieldId, submitBtnId) {
    $(document).on("keypress", "#" + fieldId, function (e) {
        var code = (e.keyCode ? e.keyCode : e.which);
        if (code == 13) {
            e.stopPropagation();
            $('#' + submitBtnId).click();
        }
    });
}

/*BOF JS functions related to Grid Views*/

function validateGridView(lastColmn, prefix) {
    var errors = 0;
    $('.errorText').remove();
    $('#last_' + lastColmn).html('');
    if ($('#' + prefix + 'view_title').val() == '') {
        $('<p class="errorText">Please enter View Title</p>').insertAfter('#' + prefix + 'view_title');
        errors++;
    } else if (checkViewExist('/checkviewexist', $('#' + prefix + 'grid_type').val(), $('#' + prefix + 'hiddenUserId').val(), $('#' + prefix + 'view_title').val(), $('#' + prefix + 'hiddenSelectGridView').val()) > 0) {
        $('<p class="errorText">View title already exists</p>').insertAfter('#' + prefix + 'view_title');
        errors++;
    }

    if ($('#' + prefix + 'frm_mygridview').find('input[type=checkbox]:checked').length == 0)
    {
        $('#last_' + lastColmn).html('<p class="errorText">Please select atleast one column</p>');
        errors++;
    }

    return errors;
}

function saveGridView(lastColmn, prefix) {
    var recordsPerPage = $('#pageSizeGrabing').val();
    var hiddenSelectGridView = $('#' + prefix + 'hiddenSelectGridView').val();
	var hiddenGenerateNewGridView = $('#' + prefix + 'hiddenGenerateNewGridView').val();
    var keyword = $('#' + prefix + 'searchInput').val();
    var customer_id = $('#customer_id').val();
    var gridType = $('#' + prefix + 'grid_type').val();
    var errors = validateGridView(lastColmn, prefix);
    if (errors == 0) {
        var url = '/savemygridview';
        var data = $('#' + prefix + 'frm_mygridview').serialize();
        $.post(url, data, function (response) {
            if (response != 0) {
                var serverResult = response.split("!(@%&)");
                $('#' + prefix + 'myGridView .closePopup').click();
                $('#' + prefix + 'frm_mygridview')[0].reset();
                if (hiddenSelectGridView && hiddenGenerateNewGridView == 0) {
                    if (hiddenSelectGridView == serverResult[0]) {
                        $('#' + prefix + 'selectGridView').find("option[value=" + serverResult[0] + "]").text(serverResult[1]);
                    }
                } else {
                    $('#' + prefix + 'selectGridView').append("<option value=" + serverResult[0] + ">" + serverResult[1] + "</option>");
                }

                $('#' + prefix + 'selectGridView').val(serverResult[0]);
                $('#' + prefix + 'selectGridView').dropkick('refresh');

                $('#' + prefix + 'hiddenSelectGridView').val(serverResult[0]);
                $('#' + prefix + 'editMyGridView').addClass('lightBoxClick');
                $('#' + prefix + 'editMyGridView').attr("data-popup", prefix + "myGridView");
				$('#' + prefix + 'hiddenGenerateNewGridView').val(0);
                //$('#' + prefix + 'addMyGridView').removeClass('lightBoxClick');
                //$('#' + prefix + 'addMyGridView').removeAttr("data-popup");
                if (gridType == 'leads') {
                    getLeadsFromGridView(recordsPerPage, serverResult[2], keyword);
                } else if (gridType == 'customers') {
                    customersListFromGridView(recordsPerPage, serverResult[2]);
                } else if (gridType == 'opportunities') {
                    getOpportunitiesFromGridView(recordsPerPage, serverResult[2], keyword, customer_id);
                } else if (gridType == 'suppliers') {
                    getSuppliersFromGridView(recordsPerPage, serverResult[2], keyword);
                } else if (gridType == 'diamond') {
                    getDiamondFromGridView(recordsPerPage, serverResult[2], keyword);
                } else if (gridType == 'weddingring') {
                    getWeddingringsFromGridView(recordsPerPage, serverResult[2], keyword);
                } else if (gridType == 'engagementring') {
                    getEngagementringsFromGridView(recordsPerPage, serverResult[2], keyword);
                } else if (gridType == 'earring') {
                    getEarringsFromGridView(recordsPerPage, serverResult[2], keyword);
                } else if (gridType == 'pendant') {
                    getPendantsFromGridView(recordsPerPage, serverResult[2], keyword);
                } else if (gridType == 'miscellaneous') {
                    getMiscellaneousFromGridView(recordsPerPage, serverResult[2], keyword);
                } else if (gridType == 'chain') {
                    getChainFromGridView(recordsPerPage, serverResult[2], keyword);
                }
            }
        });
    }
}

function deleteMyGridView(prefix) {
	$('#'+prefix+'frm_mygridview .errorText').remove();
    var id = $('#' + prefix + 'hiddenSelectGridView').val();
    var keyword = $('#' + prefix + 'searchInput').val();
    var customer_id = $('#customer_id').val();
    var recordsPerPage = $('#pageSizeGrabing').val();
    var gridType = $('#' + prefix + 'grid_type').val();
    if (id != '') {
        if (!confirm("Are you sure you want to delete this view?")) {
            return false;
        } else {
            var url = '/deletegridview';
            var data = 'id=' + id;
            $.post(url, data, function (response) {
                if (response == 1) {
                    $('#' + prefix + 'hiddenSelectGridView').val('');
					$('#' + prefix + 'hiddenGenerateNewGridView').val(0);
                    $('#' + prefix + 'selectGridView').find('option[value="' + id + '"]').remove();
                    $('#' + prefix + 'selectGridView').dropkick('refresh');
                    $('#' + prefix + 'editMyGridView').removeClass('lightBoxClick');
                    $('#' + prefix + 'editMyGridView').removeAttr('data-popup');
                    $('#' + prefix + 'addMyGridView').addClass('lightBoxClick');
                    $('#' + prefix + 'addMyGridView').attr("data-popup", prefix + "myGridView");
                    if (gridType == 'leads') {
                        getLeads(recordsPerPage, keyword);
                    } else if (gridType == 'customers') {
                        customersList(recordsPerPage);
                    } else if (gridType == 'opportunities') {
                        getOpportunities(recordsPerPage, keyword, customer_id);
                    } else if (gridType == 'suppliers') {
                        getSuppliers(recordsPerPage, keyword);
                    } else if (gridType == 'diamond') {
                        getDiamonds(recordsPerPage, keyword);
                    } else if (gridType == 'weddingring') {
                        getWeddingrings(recordsPerPage, keyword);
                    } else if (gridType == 'engagementring') {
                        getEngagementrings(recordsPerPage, keyword);
                    } else if (gridType == 'earring') {
                        getEarrings(recordsPerPage, keyword);
                    } else if (gridType == 'pendant') {
                        getPendants(recordsPerPage, keyword);
                    } else if (gridType == 'miscellaneous') {
                        getMiscellaneous(recordsPerPage, keyword);
                    } else if (gridType == 'chain') {
                        getChain(recordsPerPage, keyword);
                    }
                }
            });
        }
    }
}

function editMyGridView(prefix) {
	$('#'+prefix+'frm_mygridview .errorText').remove();
    var id = $('#' + prefix + 'hiddenSelectGridView').val();
	$('#' + prefix + 'hiddenGenerateNewGridView').val(0);
	var hiddenGenerateNewGridView = $('#' + prefix + 'hiddenGenerateNewGridView').val();
	if(hiddenGenerateNewGridView){
    	$('#' + prefix + 'gridViewHeading').html('Edit View Details');
	} else {
		$('#' + prefix + 'gridViewHeading').html('New View Details');
	}
    if (id != '') {
        var url = '/editgridview';
        var data = 'id=' + id;
        $.post(url, data, function (response) {
            var setting = JSON.parse(response);
            var columns = setting.columns_list.split(',');
            $('#' + prefix + 'frm_mygridview')[0].reset();
            $('#' + prefix + 'view_title').val(setting.view_title);
            for (i = 0; i < columns.length; i++) {
                $('#' + prefix + '' + columns[i]).prop('checked', true);
            }
        });
    }
}


function searchGridsData(prefix) {
    var keyword = $('#' + prefix + 'searchInput').val();
    var gridType = $('#' + prefix + 'grid_type').val();
    var customer_id = $('#customer_id').val();
    var selectedValue = $('#' + prefix + 'hiddenSelectGridView').val();
    var recordsPerPage = $('#pageSizeGrabing').val();
    keyword = $.trim(keyword);
    if (keyword != '' && keyword != 'undefined') {
        if (gridType == 'leads') {
            if (selectedValue != '') {
                var url = '/editgridview';
                var data = 'id=' + selectedValue;
                $.post(url, data, function (response) {
                    var setting = JSON.parse(response);
                    var columnList = setting.columns_list;
                    getLeadsFromGridView(recordsPerPage, columnList, keyword);
                });
            } else {
                getLeads(recordsPerPage, keyword);
            }
        } else if (gridType == 'customers') {
            if (selectedValue != '') {
                var url = '/editgridview';
                var data = 'id=' + selectedValue;
                $.post(url, data, function (response) {
                    var setting = JSON.parse(response);
                    var columnList = setting.columns_list;
                    customersListFromGridView(recordsPerPage, columnList, keyword);
                });
            } else {
                customersList(recordsPerPage, keyword);
            }
        } else if (gridType == 'opportunities') {

            if (selectedValue != '') {
                var url = '/editgridview';
                var data = 'id=' + selectedValue;
                $.post(url, data, function (response) {
                    var setting = JSON.parse(response);
                    var columnList = setting.columns_list;
                    getOpportunitiesFromGridView(recordsPerPage, columnList, keyword, customer_id);
                });
            } else {
                getOpportunities(recordsPerPage, keyword, customer_id);
            }
        } else if (gridType == 'suppliers') {

            if (selectedValue != '') {
                var url = '/editgridview';
                var data = 'id=' + selectedValue;
                $.post(url, data, function (response) {
                    var setting = JSON.parse(response);
                    var columnList = setting.columns_list;
                    getSuppliersFromGridView(recordsPerPage, columnList, keyword);
                });
            } else {
                getSuppliers(recordsPerPage, keyword);
            }
        } else if (gridType == 'diamond') {
			var formData = $('#frm_filter_fiamond').serialize();
            if (selectedValue != '') {
                var url = '/editgridview';
                var data = 'id=' + selectedValue;
                $.post(url, data, function (response) {
                    var setting = JSON.parse(response);
                    var columnList = setting.columns_list;					
                    getDiamondFromGridView(recordsPerPage, columnList, keyword, formData);
                });
            } else {
                getDiamonds(recordsPerPage, keyword, formData);
            }
        } else if (gridType == 'weddingring') {

            if (selectedValue != '') {
                var url = '/editgridview';
                var data = 'id=' + selectedValue;
                $.post(url, data, function (response) {
                    var setting = JSON.parse(response);
                    var columnList = setting.columns_list;
                    getWeddingringsFromGridView(recordsPerPage, columnList, keyword);
                });
            } else {
                getWeddingrings(recordsPerPage, keyword);
            }
        } else if (gridType == 'engagementring') {
            if (selectedValue != '') {
                var url = '/editgridview';
                var data = 'id=' + selectedValue;
                $.post(url, data, function (response) {
                    var setting = JSON.parse(response);
                    var columnList = setting.columns_list;
                    getEngagementringsFromGridView(recordsPerPage, columnList, keyword);
                });
            } else {
                getEngagementrings(recordsPerPage, keyword);
            }
        } else if (gridType == 'earring') {
            if (selectedValue != '') {
                var url = '/editgridview';
                var data = 'id=' + selectedValue;
                $.post(url, data, function (response) {
                    var setting = JSON.parse(response);
                    var columnList = setting.columns_list;
                    getEarringsFromGridView(recordsPerPage, columnList, keyword);
                });
            } else {
                getEarrings(recordsPerPage, keyword);
            }
        } else if (gridType == 'pendant') {
            if (selectedValue != '') {
                var url = '/editgridview';
                var data = 'id=' + selectedValue;
                $.post(url, data, function (response) {
                    var setting = JSON.parse(response);
                    var columnList = setting.columns_list;
                    getPendantsFromGridView(recordsPerPage, columnList, keyword);
                });
            } else {
                getPendants(recordsPerPage, keyword);
            }
        } else if (gridType == 'miscellaneous') {
            if (selectedValue != '') {
                var url = '/editgridview';
                var data = 'id=' + selectedValue;
                $.post(url, data, function (response) {
                    var setting = JSON.parse(response);
                    var columnList = setting.columns_list;
                    getMiscellaneousFromGridView(recordsPerPage, columnList, keyword);
                });
            } else {
                getMiscellaneous(recordsPerPage, keyword);
            }
        } else if (gridType == 'chain') {
            if (selectedValue != '') {
                var url = '/editgridview';
                var data = 'id=' + selectedValue;
                $.post(url, data, function (response) {
                    var setting = JSON.parse(response);
                    var columnList = setting.columns_list;
                    getChainFromGridView(recordsPerPage, columnList, keyword);
                });
            } else {
                getChain(recordsPerPage, keyword);
            }
        }
    } else {
        searchInputClear(prefix);
    }
}

function searchInputClear(prefix) {
    $('#' + prefix + 'searchInput').val('');
    var gridType = $('#' + prefix + 'grid_type').val();
    var keyword = $('#' + prefix + 'searchInput').val();
    var customer_id = $('#customer_id').val();
    var selectedValue = $('#' + prefix + 'hiddenSelectGridView').val();
    var recordsPerPage = $('#pageSizeGrabing').val();

    if (gridType == 'leads') {
        if (selectedValue != '') {
            var url = '/editgridview';
            var data = 'id=' + selectedValue;
            $.post(url, data, function (response) {
                var setting = JSON.parse(response);
                var columnList = setting.columns_list;
                getLeadsFromGridView(recordsPerPage, columnList);
            });
        } else {
            getLeads(recordsPerPage);
        }
    } else if (gridType == 'customers') {
        if (selectedValue != '') {
            var url = '/editgridview';
            var data = 'id=' + selectedValue;
            $.post(url, data, function (response) {
                var setting = JSON.parse(response);
                var columnList = setting.columns_list;
                customersListFromGridView(recordsPerPage, columnList);
            });
        } else {
            customersList(recordsPerPage);
        }
    } else if (gridType == 'opportunities') {
        if (selectedValue != '') {
            var url = '/editgridview';
            var data = 'id=' + selectedValue;
            $.post(url, data, function (response) {
                var setting = JSON.parse(response);
                var columnList = setting.columns_list;
                getOpportunitiesFromGridView(recordsPerPage, columnList, keyword, customer_id);
            });
        } else {
            getOpportunities(recordsPerPage, keyword, customer_id);
        }
    } else if (gridType == 'suppliers') {
        if (selectedValue != '') {
            var url = '/editgridview';
            var data = 'id=' + selectedValue;
            $.post(url, data, function (response) {
                var setting = JSON.parse(response);
                var columnList = setting.columns_list;
                getSuppliersFromGridView(recordsPerPage, columnList, keyword, customer_id);
            });
        } else {
            getSuppliers(recordsPerPage, keyword, customer_id);
        }
    } else if (gridType == 'diamond') {
        if (selectedValue != '') {
            var url = '/editgridview';
            var data = 'id=' + selectedValue;
            $.post(url, data, function (response) {
                var setting = JSON.parse(response);
                var columnList = setting.columns_list;
                getDiamondFromGridView(recordsPerPage, columnList, keyword, customer_id);
            });
        } else {
			var formData = $('#frm_filter_fiamond').serialize();
            getDiamonds(recordsPerPage, keyword, formData);
        }
    } else if (gridType == 'weddingring') {
        if (selectedValue != '') {
            var url = '/editgridview';
            var data = 'id=' + selectedValue;
            $.post(url, data, function (response) {
                var setting = JSON.parse(response);
                var columnList = setting.columns_list;
                getWeddingringsFromGridView(recordsPerPage, columnList, keyword, customer_id);
            });
        } else {
            getWeddingrings(recordsPerPage, keyword, customer_id);
        }
    } else if (gridType == 'engagementring') {
        if (selectedValue != '') {
            var url = '/editgridview';
            var data = 'id=' + selectedValue;
            $.post(url, data, function (response) {
                var setting = JSON.parse(response);
                var columnList = setting.columns_list;
                getEngagementringsFromGridView(recordsPerPage, columnList, keyword);
            });
        } else {
            getEngagementrings(recordsPerPage, keyword);
        }
    } else if (gridType == 'earring') {
        if (selectedValue != '') {
            var url = '/editgridview';
            var data = 'id=' + selectedValue;
            $.post(url, data, function (response) {
                var setting = JSON.parse(response);
                var columnList = setting.columns_list;
                getEarringsFromGridView(recordsPerPage, columnList, keyword);
            });
        } else {
            getEarrings(recordsPerPage, keyword);
        }
    } else if (gridType == 'pendant') {
        if (selectedValue != '') {
            var url = '/editgridview';
            var data = 'id=' + selectedValue;
            $.post(url, data, function (response) {
                var setting = JSON.parse(response);
                var columnList = setting.columns_list;
                getPendantsFromGridView(recordsPerPage, columnList, keyword);
            });
        } else {
            getPendants(recordsPerPage, keyword);
        }
    } else if (gridType == 'miscellaneous') {
        if (selectedValue != '') {
            var url = '/editgridview';
            var data = 'id=' + selectedValue;
            $.post(url, data, function (response) {
                var setting = JSON.parse(response);
                var columnList = setting.columns_list;
                getMiscellaneousFromGridView(recordsPerPage, columnList, keyword);
            });
        } else {
            getMiscellaneous(recordsPerPage, keyword);
        }
    } else if (gridType == 'chain') {
        if (selectedValue != '') {
            var url = '/editgridview';
            var data = 'id=' + selectedValue;
            $.post(url, data, function (response) {
                var setting = JSON.parse(response);
                var columnList = setting.columns_list;
                getChainFromGridView(recordsPerPage, columnList, keyword);
            });
        } else {
            getChain(recordsPerPage, keyword);
        }
    }
}

function checkViewExist(url, grid_type, user_id, view_title, selectedView) {
    var count;
    $.ajax({
        type: 'POST',
        url: url,
        async: false,
        data: {
            'grid_type': grid_type,
            'user_id': user_id,
            'view_title': view_title,
            'selected_view': selectedView
        },
        success: function (response) {
            count = response;
        }
    });

    return count;
}

/*EOF JS functions related to Grid Views*/


function cancelButtonProperty(formId, popupId) {
    $('#' + formId)[0].reset();
    $('#' + popupId + ' span.errorText').remove();
    $('#' + popupId + ' .closePopup').click();
}

function checkDate(input) {

    //http://www.javascriptkit.com/script/script2/validatedate.shtml

    var validformat = /^\d{2}\/\d{2}\/\d{4}$/ //Basic check for format validity
    if (!validformat.test(input)) {
        return false;
    } else { //Detailed check for valid date ranges
        var dayfield = input.split("/")[0];
        var monthfield = input.split("/")[1];
        var yearfield = input.split("/")[2];
        var dayobj = new Date(yearfield, monthfield - 1, dayfield);
        if ((dayobj.getMonth() + 1 != monthfield) || (dayobj.getDate() != dayfield) || (dayobj.getFullYear() != yearfield))
            return false;
        else
            return true;
    }
}

function addAdditionalRow(settingOptions, shapeOptions, gemtypeOptions, hideState) {
   var settOpp = '';
   var shapeOpp = '';
   var gemtypeOpp = '';
   
   var additionalTbl = document.getElementById('additionalTbl');
   var Row = parseInt(document.getElementById('hiddenRowCount').value);
   var temprow=Row+1;
   
   var mainRow = additionalTbl.insertRow(temprow);
   var trId ="tr"+temprow;
   mainRow.id=trId;
   if(hideState == 1){
   	 mainRow.className = "displayHide";
	 mainRow.style.display = "table-row";
   }
  
   var table =  document.getElementById('additionalTbl');
   //var newRow = table.insertRow(0);
   
   for(i = 0; i < settingOptions.length; i++){
	   settOpp += '<option value='+settingOptions[i]['id']+'>'+settingOptions[i]['setting_style']+'</option>';
   }
   
   for(i = 0; i < shapeOptions.length; i++){
	   shapeOpp += '<option value='+shapeOptions[i]['id']+'>'+shapeOptions[i]['shape']+'</option>';
   }
   
   for(i = 0; i < gemtypeOptions.length; i++){
	   gemtypeOpp += '<option value='+gemtypeOptions[i]['id']+'>'+gemtypeOptions[i]['gem_type']+'</option>';
   }
   
   var newCell = mainRow.insertCell(0);
   newCell.innerHTML = '<a href="javascript:;" onclick="removeAdditionalRow(this)" title="Remove" class="removeRow">-</a><div class="selectDrop width100p"><select class="dropdown" name="style[]" id="style'+temprow+'">'+settOpp+'</select></div>';
  
   var newCell = mainRow.insertCell(1);
   newCell.innerHTML = '<div class="selectDrop width100p"><select class="dropdown" name="shape[]" id="shape'+temprow+'">'+shapeOpp+'</select></div>';   
   
   var newCell = mainRow.insertCell(2);
   newCell.innerHTML = '<div class="selectDrop width100p"><select class="dropdown" name="gemtype[]" id="gemtype'+temprow+'">'+gemtypeOpp+'</select></div>';   
   
   var newCell = mainRow.insertCell(3);
   newCell.innerHTML = '<input type="text" value="" class="inputTxt pureNumaric" name="quantity[]" id="quantity'+temprow+'">';
   
   var newCell = mainRow.insertCell(4);
   newCell.innerHTML = '<input type="text" value="" class="inputTxt pureNumaric" name="size[]" id="size'+temprow+'">';
   
   var newCell = mainRow.insertCell(5);
   newCell.innerHTML = '<input type="text" value="" class="inputTxt pureNumaric" name="totalcarat[]" id="totalcarat'+temprow+'">';   
   
  // var newCell = mainRow.insertCell(0);
   //newCell.innerHTML = '<a href="javascript:;" onclick="removeAdditionalRow(this)" title="Remove">Remove</a>';   
  
   tbodyAdditional.appendChild(mainRow);
   $('.dropdown').dropkick();   
   //mainRow.appendChild(mainRow);
   document.getElementById('hiddenRowCount').value=temprow; 
   //Force Numaric Entry While adding New Row
   $(".pureNumaric").ForcePureNumericOnly();
}

function removeAdditionalRow(r) {
    var i = r.parentNode.parentNode.rowIndex;
    document.getElementById("additionalTbl").deleteRow(i);
    document.getElementById('hiddenRowCount').value = document.getElementById('hiddenRowCount').value - 1;
}

function deleteAddtionalAjax(id, type, rowid){
	if (confirm("Are you sure you want to delete")) {
        var url = '/deleteadditional';
		var data = {'id':id, 'type':type};
		$.post(url, data, function(response){
			//if(response == 1){
				$('#'+rowid).remove();
			//}
		});
    } else {
		return false;
	}
}

$.fn.ForcePureNumericOnly =
function()
{
    return this.each(function()
    {
        $(this).keydown(function(e)
        {
            /*var key = e.charCode || e.keyCode || 0;
            // allow backspace, tab, delete, enter, arrows, numbers and keypad numbers ONLY
            // home, end, period, and numpad decimal
            return (
                key == 8 || 
                key == 9 ||
                key == 13 ||
                key == 46 ||
                //key == 110 ||
                //key == 190 ||
                (key >= 35 && key <= 40) ||
                (key >= 48 && key <= 57) ||
                (key >= 96 && key <= 105));*/
			
			
			if ($.inArray(e.keyCode, [46, 8, 9, 27, 13]) !== -1 ||
					// Allow: Ctrl+A
							(e.keyCode == 65 && e.ctrlKey === true) ||
							// Allow: home, end, left, right, down, up
									(e.keyCode >= 35 && e.keyCode <= 40)) {
				// let it happen, don't do anything
				return true;
			 }
	
			// Ensure that it is a number and stop the keypress
			if ((e.shiftKey || (e.keyCode < 48 || e.keyCode > 57)) && (e.keyCode < 96 || e.keyCode > 105)) {
				e.preventDefault();
			}
        });
    });
};

function loadBasicValidations(){
	//Force Numaric Entry
	$(".pureNumaric").ForcePureNumericOnly();
	globalTextboxMaxLength('textboxGlobalMaxLength');
	globalTextAreaMaxLength("textareaGlobalMaxLength");
}

function validateNumeric(){
	$(document).on('keydown','input[data-numeric]', function (e) {
		// Allow: backspace, delete, tab, escape, enter and .
		if ($.inArray(e.keyCode, [46, 8, 9, 27, 13]) !== -1 ||
		// Allow: Ctrl+A
				(e.keyCode == 65 && e.ctrlKey === true) ||
				// Allow: home, end, left, right, down, up
						(e.keyCode >= 35 && e.keyCode <= 40)) {
			// let it happen, don't do anything
			return;
		}

		// Ensure that it is a number and stop the keypress
		if ((e.shiftKey || (e.keyCode < 48 || e.keyCode > 57)) && (e.keyCode < 96 || e.keyCode > 105)) {
			e.preventDefault();
		}
	});
}

function unlinkGalleryImage(imgFullData, DivId, frmId, hiddenValueHolderId, imgId){
	var imgValidId = 0;
	if(imgId > 0){
		imgValidId = imgId;
	}
	var url = '/unlinkfile';
	var existingJsonValue = $('#'+frmId+' #'+hiddenValueHolderId).val();
	var unlinkResponse = 0;
	if (confirm("Are you sure?")) {
		if(imgValidId > 0){
			data = {'fileFullData':imgFullData, 'imgId':imgId};
		} else {
			data = {'fileFullData':imgFullData};
		}
		
		$.ajax({
			url: url,
			type: 'POST',
			async: false,
			data: data,
			success: function(response){
				if(response){
					$('#'+DivId).remove();
					if(DivId == "cad_stepthree_galleryImg0"){
						existingJsonValue= '';
					} else {
						existingJsonValue = JSON.parse(existingJsonValue);
						imgName = imgFullData.replace('/milestone_attachments/', '');
						existingJsonValue.splice( $.inArray(imgName, existingJsonValue), 1 );
						existingJsonValue = JSON.stringify(existingJsonValue);
					}
					$('#'+frmId+' #'+hiddenValueHolderId).val(existingJsonValue);
				}else{
					unlinkResponse = 0;
				}
			}
		});
	}
}

function isUrlValid(url) {
    if(/^(http|https|ftp):\/\/[a-z0-9]+([\-\.]{1}[a-z0-9]+)*\.[a-z]{2,5}(:[0-9]{1,5})?(\/.*)?$/i.test(url)){
		return true;
	} else {
		return false;
	}
}

function globalTextboxMaxLength(classIdentity) {
    $('.'+classIdentity).attr('maxlength','50');
}

function globalTextAreaMaxLength(classIdentity) {
    $('.'+classIdentity).attr('maxlength','1000');
}
