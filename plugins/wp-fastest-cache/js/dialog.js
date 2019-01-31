var Wpfc_Dialog = {
	id : "",
	buttons: [],
	dialog: function(id, buttons){
		var self = this;
		self.id = id;
		self.buttons = buttons;

		jQuery("#" + id).show();
		
		jQuery("#" + id).draggable({
			stop: function(){
				jQuery(this).height("auto");
			}
		});

		jQuery("#" + id).position({my: "center", at: "center", of: window});

		jQuery(".close-wiz").click(function(e){
			jQuery(e.target).closest("div[id^='wpfc-modal-']").remove();
		});

		self.show_buttons();
	},
	remove: function(clone_modal_id){
		if(typeof clone_modal_id != "undefined"){
			jQuery("#" + clone_modal_id).remove();
		}else{
			var self = this;
			jQuery("#" + self.id).remove();
		}
	},
	show_buttons: function(){
		var self = this;
		if(typeof self.buttons != "undefined"){
			jQuery.each(self.buttons, function( index, value ) {
				jQuery("#" + self.id + " button[action='" + index + "']").show();
				jQuery("#" + self.id + " button[action='" + index + "']").click(function(e){
					if(index == "close"){
						jQuery(e.target).closest("div[id^='wpfc-modal-']").remove();
					}else{
						value();
					}
				});
			});
		}
	}
};