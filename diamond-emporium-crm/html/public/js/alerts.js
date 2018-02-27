/**
 * Populate JQXGrid for alert list
 */
function getAlerts(recordsPerPage, url, columnList, target){
	var source =
	{
		datatype: "json",
		datafields: [
			{ name: 'alert_id' },
			{ name: 'target' },
			{ name: 'created_date' },
			{ name: 'message' },
		],
		cache: false,
		url: url,
		root: 'Rows',
		beforeprocessing: function (data) {
			source.totalrecords = data.TotalRows;
		},
	};	
	
	var pageable = true, sortable = true;
	var dataAdapter = new $.jqx.dataAdapter(source, {
		downloadComplete: function (data, status, xhr) {},
		loadComplete: function (data) { 
			if($(target).find('.jqx-grid-empty-cell').length>0 ){					
					if($(target).find('.jqx-grid-empty-cell').parent('div').height()<30){
						$('.jqx-grid-empty-cell').parent('div').addClass('norecordsFoundRow');
						$('.jqx-grid-empty-cell').closest(target).addClass('noInfoFound');
					}
					$(target).find('.jqx-grid-empty-cell >span').text("No records found");
			}else{
				if($(target).hasClass('noInfoFound')){
					$(target).removeClass('noInfoFound');
				}
			}
		},
		loadError: function (xhr, status, error) { }
	});
	
	var cellsrenderer = function (row, columnfield, value, defaulthtml, columnproperties) {
		return '<div><a href="javascript:;" onclick="javascript:clearAlert(this, ' + value + ');" class="gridLink">Clear</a></div>';
    };

	var colList = columnList.split(',');
	var gridColumnList = new Array();
	for (lp = 0; lp < colList.length; lp++){
		if(colList[lp] == 'created_date'){
			gridColumnList.push({ text: 'Date', type: 'string', datafield: 'created_date', width:'10%'});
		}
		if(colList[lp] == 'target'){
			gridColumnList.push({ text: 'Target', type: 'string', datafield: 'target'});
		}
		if(colList[lp] == 'message'){
			gridColumnList.push({ text: 'Message', type: 'string', datafield: 'message', width:'80%' });
		}
		if(colList[lp] == 'action'){
			gridColumnList.push({ text: 'Action', type: 'string', datafield: 'alert_id', cellsrenderer: cellsrenderer});
		}
	}

    $(target).jqxGrid(
	{
		width: '100%',
		source: dataAdapter,
		sorttogglestates:1,
		pageable: true,
		pagesize: parseInt(recordsPerPage),
		pagesizeoptions: ['5', '10', '20', '50', '100'],
		autorowheight: true,
		autoheight: true,
		enabletooltips: true,
		rowsheight:50,
		columnsheight:50,
		pagerheight:50,
		virtualmode: true,
		rendergridrows: function (params) {
			return params.data;
		},
		columns: gridColumnList
	});
	
	$(target).bind("sort", function (event) {
		$(target).jqxGrid('updatebounddata', 'filter');
	});
	
	$(target).bind("pagesizechanged", function (event) {
		var args = event.args;
		var pagenumber = args.pagenum;
		var pagesize = args.pagesize;
		$('#pageSizeGrabing').val(pagesize);
	});
}

function getMyAlerts(recordsPerPage) {
	getAlerts(recordsPerPage, '/ajaxuseralertlist', 'created_date,message,action', '#jqxMyAlerts');
}

function getAllAlerts(recordsPerPage){
	getAlerts(recordsPerPage, '/ajaxallalertlist', 'created_date,target,message', '#jqxAllAlerts');
}

function clearAlert(element, alertId) {
    var url = '/ajaxuseralertclear';
    var data = { id: alertId };
	$.post(url, data, function(response){
		if (response == '1') {
			$(element).parent().parent().parent().fadeOut(400, function() {
			    $(this).remove();
			  });
			userAlertCount();
		} else {
			alert('Error clearing alert: ' + response);
		}
	});
}

function userAlertCount() {
    var url = '/ajaxuseralertcount';
    $.get(url, function (response) {
    	var count = JSON.parse(response).TotalRows;
    	$("#userAlertsCount").html(count);
    	if (count > 0) {
    		$("#userAlerts").addClass("alerts");
    	} else {
    		$("#userAlerts").removeClass("alerts");
    	}
    });	
}

$(document).ready(function () {
	userAlertCount();
});