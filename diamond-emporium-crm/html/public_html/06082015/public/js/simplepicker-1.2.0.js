;
(function($, window, document, undefined) {

	var pluginName = "simplePicker",
		defaults = {
			style: 'dark',
			firstday: 0,
			days: ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'],
			months: ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'],
			delimiter: '-',
			dateformat: function() {
				return 'yyyy' + this.delimiter + 'mm' + this.delimiter + 'dd'
			}
		};


	function Plugin(element, options) {
		this.element = element;

		this.options = $.extend({}, defaults, options);

		this._defaults = defaults;
		this._name = pluginName;
		this.init();
	}

	Plugin.prototype = {

		init: function() {
			var datefield = $(this.element);
			datefield.attr('autocomplete', 'off');
			var ops = this.options;
			var datePicker = $('<div class="datePickerHolder-' + ops.style + '">');
			var tDate = {};
			var md = new Date();
			var defdate = ops.dateformat().split(ops.delimiter);
			var thisdate = {};
			if(datefield.val() == '') {
				$.each(defdate, function(index) {
					defdate[index] == 'yyyy' ? thisdate.year = md.getFullYear() : null;
					defdate[index] == 'mm' ? thisdate.month = md.getMonth() : null;
					defdate[index] == 'dd' ? thisdate.day = md.getDate() : null;
				});
				//alert(thisdate.year);
			} else {
				//alert(ops);
				$.each(defdate, function(index) {
					defdate[index] == 'yyyy' ? thisdate.year = md.getFullYear() : null;
					defdate[index] == 'mm' ? thisdate.month = md.getMonth() : null;
					defdate[index] == 'dd' ? thisdate.day = md.getDate() : null;
				});
			}
			var tbl = this.genTable(thisdate);
			datePicker.empty().html(tbl);

			this.selectRefresh(ops, datePicker);

			datePicker.find('.month').change();

			datePicker.css({
				display: 'none',
				top: datefield.offset().top + datefield.outerHeight(),
				left: datefield.offset().left
			})
			datePicker.insertAfter(datefield);

			datefield.on('click', function() {
				//datefield.val('');
				datePicker.fadeIn(100);
				if($('#js-frequency').length==0 && datePicker.find('select.js-frequency').length==0){
				datePicker.append('<select class="js-frequency" id="frequency"><option value="none">No repeat</option><option value="daily">Daily</option><option value="weekdaily">Working days</option><option value="weekly">Weekly</option><option value="biweekly">Biweekly</option><option value="monthly">Monthly</option><option value="yearly">Yearly</option></select>');
				}
				datePicker.parent().css({'position':'relative','z-index':9999});
			})
			datePicker.on('click', '.remove', function() {
				datePicker.find('#frequency').remove();
				datePicker.fadeOut(100)
			})
			datePicker.on('change', '.year', function() {
				//alert(md.getFullYear());
				var ops = datefield.data().plugin_simplePicker.options;
				thisdate.year = parseInt($(this).val(), 10);
				thisdate.month = parseInt(datePicker.find('.month').find(':selected').val(), 10);
				var tbl = datefield.data().plugin_simplePicker.genTable(thisdate);
				datePicker.empty().html(tbl);
				datefield.data().plugin_simplePicker.selectRefresh(ops, datePicker);
				datefield.val('');
			})
			datePicker.on('change', '.month', function() {
				var ops = datefield.data().plugin_simplePicker.options;
				thisdate.year = parseInt(datePicker.find('.year').find(':selected').val(), 10);
				thisdate.month = parseInt($(this).val(), 10);
				var tbl = datefield.data().plugin_simplePicker.genTable(thisdate);
				datePicker.empty().html(tbl);
				datefield.data().plugin_simplePicker.selectRefresh(ops, datePicker);
				datefield.val('');
			})
			datePicker.on('click', 'td', function() {
				var dy = datePicker.find('.year').val();
				var dm = parseInt(datePicker.find('.month').find(':selected').val(), 10) + 1;
				var dd = parseInt($(this).html(), 10);
				var tval = [];
				$.each(defdate, function(index) {
					defdate[index] == 'yyyy' ? tval[index] = dy : null;
					defdate[index] == 'mm' ? tval[index] = (dm > 9 ? "" + dm : "0" + dm) : null;
					defdate[index] == 'dd' ? tval[index] = (dd > 9 ? "" + dd : "0" + dd) : null;
				});
				datefield.val(tval[0] + ops.delimiter + tval[1] + ops.delimiter + tval[2]);
				//$(this).addClass('selected');
			})
			datePicker.on('click', '.nexti', function() {
				datefield.data().plugin_simplePicker.next(ops, thisdate, defdate, datefield, datePicker);
				
			
			})
			datePicker.on('click', '.previ', function() {
				datefield.data().plugin_simplePicker.prev(ops, thisdate, defdate, datefield, datePicker);
			})
			datePicker.on('click', '.next', function() {
				datefield.data().plugin_simplePicker.next(ops, thisdate, defdate, datefield, datePicker);
			})
			datePicker.on('click', '.prev', function() {
				datefield.data().plugin_simplePicker.prev(ops, thisdate, defdate, datefield, datePicker);
			})
			datePicker.on('click', '.geti', function() {
				datefield.data().plugin_simplePicker.geti(ops, thisdate, defdate, datefield, datePicker);
			})

		},
		prev: function(ops, thisdate, defdate, datefield, datePicker) {
			var ops = datefield.data().plugin_simplePicker.options;
			thisdate.month--;
			thisdate.month < 0 ? (thisdate.year--, thisdate.month = 11) : null;
			var tbl = datefield.data().plugin_simplePicker.genTable(thisdate);
			datePicker.empty().html(tbl);
			datefield.data().plugin_simplePicker.selectRefresh(ops, datePicker);
			datefield.val('');
		},
		next: function(ops, thisdate, defdate, datefield, datePicker) {
			var ops = datefield.data().plugin_simplePicker.options;
			thisdate.month++;
			thisdate.month > 11 ? (thisdate.year++, thisdate.month = 0) : null;
			var tbl = datefield.data().plugin_simplePicker.genTable(thisdate);
			datePicker.empty().html(tbl);
			datefield.data().plugin_simplePicker.selectRefresh(ops, datePicker);
			datefield.val('');
		},
		geti: function(ops, thisdate, defdate, datefield, datePicker) {
			var md = new Date();
			$.each(defdate, function(index) {
				defdate[index] == 'yyyy' ? thisdate.year = md.getFullYear() : null;
				defdate[index] == 'mm' ? thisdate.month = md.getMonth() : null;
				defdate[index] == 'dd' ? thisdate.day = md.getDate() : null;
			});
			var tbl = datefield.data().plugin_simplePicker.genTable(thisdate);
			datePicker.empty().html(tbl);
			var dy = thisdate.year
			var dm = thisdate.month + 1;
			var dd = thisdate.day;
			var tval = [];
			$.each(defdate, function(index) {
				defdate[index] == 'yyyy' ? tval[index] = dy : null;
				defdate[index] == 'mm' ? tval[index] = (dm > 9 ? "" + dm : "0" + dm) : null;
				defdate[index] == 'dd' ? tval[index] = (dd > 9 ? "" + dd : "0" + dd) : null;
			});
			datefield.val(tval[0] + ops.delimiter + tval[1] + ops.delimiter + tval[2]);
			datefield.data().plugin_simplePicker.selectRefresh(ops, datePicker);
		},
		selectRefresh: function(ops, datePicker) {
			var nm = datePicker.find('.month').val();
			datePicker.find('.month').children().remove();
			for(var i = 0; i < ops.months.length; i++) {
				var at = '';
				i == nm ? at = 'selected' : at = '';
				datePicker.find('.month').append('<option value = "' + i + '" ' + at + '>' + ops.months[i] + '</option>');
			};
			var nm = Number(datePicker.find('.year').val());
			datePicker.find('.year').children().remove();
			for(var i = nm - 5; i <= nm + 5; i++) {
				i == nm ? at = 'selected' : at = '';
				datePicker.find('.year').append('<option value = "' + i + '" ' + at + '>' + i + '</option>');
			};
		},
		getWeek: function(h) {
			var target = new Date(h.valueOf());
			var dayNr = (h.getDay() + 6) % 7;
			target.setDate(target.getDate() - dayNr + 3 - this.options.firstday);
			var jan4 = new Date(target.getFullYear(), 0, 4);
			var dayDiff = (target - jan4) / 86400000;
			var weekNr = 1 + Math.ceil(dayDiff / 7);
			return weekNr;
		},
		shifter: function(arrays, count) {
			var array = arrays.slice(0);
			var len = array.length;
			for(var i = 0; i < count; i++) {
				var copy = array[0];
				array.push(copy);
				array.splice(0, 1);
			};
			return array;
		},

		genTable: function(thisdate) {
			var dayArr = this.shifter(this.options.days, this.options.firstday);
			var cd = new Date();
			var sr = new Date(thisdate.year, thisdate.month, 1);
			var sd = new Date(thisdate.year, thisdate.month, 1);
			var ed = new Date(thisdate.year, thisdate.month + 1, 1, 0);
			sd.setDate(sd.getDate() - sd.getDay() + this.options.firstday);
			(this.getWeek(sr) < this.getWeek(sd) && sd.getFullYear() == sr.getFullYear()) ? sd.setDate(sd.getDate() - 7) : true;
			var html = '<table class="datePicker-' + this.options.style + '">';
			html += '<caption>';
			html += '<span class = "previ" title="Previous" ></span>';
			html += '<select class = "year"><option selected>' + thisdate.year + '</option></select><select class = "month">';
			html += '<option value = "' + thisdate.month + '" selected>' + this.options.months[thisdate.month] + '</option></select>';
			html += '<span class = "remove" title="Close"></span><span class = "nexti" title="Next"></span>';
			html += '<thead><tr>';
			var w = true;
			while(sd < ed) {
				html += '<tr>'
				if(w == true) {
					for(var td = -1; td <= 6; td++) {
						if(td == -1) {
							//html += '<th>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</th>';
						} else {
							html += '<th>' + dayArr[td] + '</th>';
						}
					}
					html += '</tr></thead><tbody>';
					w = false;
				} else {
					for(var td = -1; td <= 6; td++) {
						if(td == -1) {
							var ws = new Date(sd);
							ws.setDate(sd.getDate() + 3);
							//html += '<th>' + this.getWeek(ws) + '</th>';
						} else {
							html += '<td class = "';
							if(cd.getFullYear() == sd.getFullYear() && cd.getMonth() == sd.getMonth() && cd.getDate() == sd.getDate()) {
								html += ' now';
							}
							if(td + this.options.firstday == 0 || td + this.options.firstday == 6 || td + this.options.firstday == 7) {
								html += ' weekEnd';
							}
							(sr.getFullYear() < sd.getFullYear()) ? html += ' next' : true;
							(sr.getFullYear() > sd.getFullYear()) ? html += ' prev' : true;
							(sr.getMonth() < sd.getMonth() && sr.getFullYear() == sd.getFullYear()) ? html += ' next' : true;
							(sr.getMonth() > sd.getMonth() && sr.getFullYear() == sd.getFullYear()) ? html += ' prev' : true;
							(sr.getMonth() == sd.getMonth() && sr.getFullYear() == sd.getFullYear()) ? html += ' current' : true;
							html += '">' + sd.getDate() + '</td>';
							sd.setDate(sd.getDate() + 1);
						}
					}
				}
				html += '</tr>'
			}
			return html += '</tbody></table>';
		}
	}

	$.fn[pluginName] = function(options) {
		return this.each(function() {
			if(!$.data(this, "plugin_" + pluginName)) {
				$.data(this, "plugin_" + pluginName, new Plugin(this, options));
			}
		});
	};

})(jQuery, window, document);