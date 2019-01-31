var WpfcSchedule = {
	id: "#wpfc-server-time",
	serverTime : "", //milliseconds
	dropdowns: "#wpFastestCacheTimeOutHour, #wpFastestCacheTimeOutMinute, #wpFastestCacheTimeOut",
	init: function(){
		if(jQuery("form[id='wpfc-schedule-panel']").length){
			this.setServerTime();
			this.clock();
			this.setDropdownEvent();
			this.setDeleteCronEvent();
			jQuery("#wpfc-schedule-panel input[type='submit']").bind("click", this, this.submitButtonEvent);
		}
	},
	setDeleteCronEvent: function(){
		jQuery("#deleteCron").click(function(){
			jQuery("#wpFastestCacheTimeOut").val("");
			jQuery("form#wpfc-schedule-panel").submit();
		});
	},
	setDropdownEvent: function(){
		jQuery(this.dropdowns).change(function(e){
			var e = jQuery(e.currentTarget);

			if(e.attr("value")){
				e.css("background-color", "");
			}

/*			if(e.attr("id") == "wpFastestCacheTimeOut"){
				if(e.val() == "hourly"){
					jQuery("#wpFastestCacheTimeOutHour, #wpFastestCacheTimeOutMinute").prop('disabled', true);
				}else{
					jQuery("#wpFastestCacheTimeOutHour, #wpFastestCacheTimeOutMinute").prop('disabled', false);
				}
			}*/
		});
	},
	setServerTime: function(){
		this.serverTime = new Date(jQuery(this.id).text()).getTime();
	},
	clock: function(){
		var self = this;
		var newDate;

		setInterval(function(){
			newDate = new Date(self.serverTime);
			jQuery(self.id).text(newDate.getFullYear() + "-" + (newDate.getMonth() + 1) + "-" + newDate.getDate() + " " + newDate.getHours() + ":" + newDate.getMinutes() + ":" + newDate.getSeconds());
			self.serverTime = self.serverTime + 1000;
		}, 1000);
	},
	submitButtonEvent: function(self){
		var error_count = 0;

		jQuery(self.data.dropdowns).each(function(i, e){
			if(jQuery(e).attr("value")){
				jQuery(e).css("background-color", "");
			}else{
				error_count++;
				jQuery(e).css("background-color", "red");
			}
		});

		return error_count > 0 ? false : true;
	}
};
WpfcSchedule.init();