var WpfcDB = {
	init: function(){
		var self = this;

		jQuery("#wpfc-db").change(function(e){
			jQuery("#revert-loader-toolbar").show();
			self.update();
		});

		if(jQuery(".tab8").is(":visible")){
			jQuery("#revert-loader-toolbar").show();
			self.update();
    	}

    	jQuery(function(){
    		self.update();
    	});

    	self.click_event_for_warnings();
	},
	click_event_for_warnings: function(){
		var self = this;

		jQuery("div.tab8 div[wpfc-db-name]").click(function(e){
			jQuery("#revert-loader-toolbar").show();

			jQuery.ajax({
				type: 'GET', 
				url: ajaxurl,
				dataType : "json",
				data : {"action": "wpfc_db_fix", "type": jQuery(this).attr("wpfc-db-name")},
				cache: false, 
				success: function(data){
					if(data.success){
						self.update();
					}else{
						jQuery("#revert-loader-toolbar").hide();
						
						if(data.showupdatewarning){
							Wpfc_New_Dialog.dialog("wpfc-modal-updatenow", {close: function(){
								Wpfc_New_Dialog.clone.find("div.window-content input").each(function(){
									if(jQuery(this).attr("checked")){
										var id = jQuery(this).attr("action-id");
										jQuery("div.tab1 div[template-id='wpfc-modal-updatenow'] div.window-content input#" + id).attr("checked", true);
									}
								});

								Wpfc_New_Dialog.clone.remove();
							}});
						}else{
							if(typeof data.message != "undefined" && data.message){
								alert(data.message);
							}else{
								alert("DB Error");
							}
						}
					}
				}
			});
		});
	},
	update: function(){
		var self = this;

		jQuery.ajax({
			type: 'GET', 
			url: ajaxurl,
			dataType : "json",
			data : {"action": "wpfc_db_statics"},
			cache: false, 
			success: function(data){
				jQuery.each(data, function(key, value){
					jQuery(".tab8 div[wpfc-db-name='" + key + "'] span.db-number").css({'color': (value > 0) ? "red" : "#6BC359"});
					jQuery(".tab8 div[wpfc-db-name='" + key + "'] span.db-number").text("(" + value + ")");
					jQuery(".tab8 div[wpfc-db-name='" + key + "'] div.meta").attr('class', (value > 0) ? "meta warning" : "meta success");
				});

				if(data.all_warnings > 0){
					jQuery("label[for='wpfc-db']").text("DB (" + data.all_warnings + ")");
				}else{
					jQuery("label[for='wpfc-db']").text("DB");
				}
				
				jQuery("#revert-loader-toolbar").hide();
			}
		});
	}
};

// if(window.attachEvent) {
//     window.attachEvent('onload', WpfcDB_init);
// } else {
//     if(window.onload) {
//         var curronload = window.onload;
//         var newonload = function(evt) {
//             curronload(evt);
//             WpfcDB_init(evt);
//         };
//         window.onload = newonload;
//     } else {
//         window.onload = WpfcDB_init;
//     }
// }

if(window.attachEvent){
	window.attachEvent('onload', WpfcDB_init);
}else if(window.addEventListener){
	window.addEventListener('load', WpfcDB_init, false);
}

function WpfcDB_init(){WpfcDB.init();}