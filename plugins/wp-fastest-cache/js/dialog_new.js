var Wpfc_New_Dialog = {
	template_id: "",
	id : "",
	buttons: [],
	clone: "",
	current_page_number: 1,
	total_page_number: 0,
	dialog: function(id, buttons, callback){
		var self = this;
		self.clone = jQuery("div[template-id='" + id + "']").clone();

		self.total_page_number = self.clone.find("div[wpfc-page]").length;

		self.template_id = id;
		self.id = id + "-" + new Date().getTime();
		self.buttons = buttons;
		
		self.clone.attr("id", self.id);
		self.clone.removeAttr("template-id");

		jQuery("body").append(self.clone);
		
		self.clone.show();
		
		self.clone.draggable({
			stop: function(){
				jQuery(this).height("auto");
			}
		});
		self.clone.position({my: "center", at: "center", of: window});
		self.clone.find(".close-wiz").click(function(){
			self.remove(this);
		});

		self.update_ids_for_label();
		
		self.show_buttons();

		if(typeof callback != "undefined"){
			if(typeof callback == "function"){
				callback(self);
			}
		}

		self.click_event_add_new_keyword_button();
		self.add_new_keyword_keypress();
	},
	remove: function(button){
		jQuery(button).closest("div[id^='wpfc-modal-']").remove();
	},
	show_buttons: function(){
		var self = this;

		if(typeof self.buttons != "undefined"){
			jQuery.each(self.buttons, function( index, value ) {
				self.clone.find("button[action='" + index + "']").click(function(){
					if(value == "default"){
						if(index == "next"){
							self.default_next_action();
						}

						if(index == "back"){
							self.default_back_action();
						}

						if(index == "close"){
							self.default_close_action();
						}
					}else{
						value(this);
					}
				});
			});
		}
	},
	default_next_action: function(){
		this.current_page_number = this.current_page_number + 1;

		this.show_page(this.current_page_number);

		this.show_button("back");

		if(this.total_page_number == this.current_page_number){
			this.hide_button("next");
			this.show_button("finish");
		}
	},
	default_back_action: function(){
		this.current_page_number = this.current_page_number - 1;

		this.show_page(this.current_page_number);

		this.show_button("next");
		this.hide_button("finish");

		if(this.current_page_number == 1){
			this.hide_button("back");
		}
	},
	default_close_action: function(){
		Wpfc_New_Dialog.clone.remove();
	},
	show_button: function(index){
		this.clone.find("button[action='" + index + "']").show();
	},
	hide_button: function(index){
		this.clone.find("button[action='" + index + "']").hide();
	},
	show_page: function(number){
		this.clone.find("div[wpfc-page]").hide();
		this.clone.find("div[wpfc-page='" + number + "']").show();
		this.current_page_number = number;
	},
	update_ids_for_label: function(){
		var self = this;
		var input;
		var id = "";

		self.clone.find("div.window-content div.wiz-input-cont").each(function(){
			input = jQuery(this).find("label.mc-input-label input");

			if(input.length){
				id = input.attr("id") + self.id;

				jQuery(this).find("label.mc-input-label input").attr("id", id);
				jQuery(this).find("label").last().attr("for", id);
			}
		});
	},
	set_values_from_tmp_to_real: function(){
		var self = this;

		Wpfc_New_Dialog.clone.find("div.window-content input, div.window-content select").each(function(){
			if(jQuery(this).prop("tagName") == "SELECT"){
				jQuery("div.tab1 div[template-id='" + self.template_id + "'] div.window-content select[name='" + jQuery(this).attr("name") + "']").val(jQuery(this).val());
			}else if(jQuery(this).prop("tagName") == "INPUT"){
				if(jQuery(this).attr("type") == "checkbox"){
					if(typeof jQuery(this).attr("checked") != "undefined"){
						jQuery("div.tab1 div[template-id='" + self.template_id + "'] div.window-content input[name='" + jQuery(this).attr("name") + "']").attr("checked", true);
					}else{
						jQuery("div.tab1 div[template-id='" + self.template_id + "'] div.window-content input[name='" + jQuery(this).attr("name") + "']").attr("checked", false);
					}
				}else{
					//toDo
				}
			}
		});
	},
	add_new_keyword_keypress: function(){
		Wpfc_New_Dialog.clone.find(".wpfc-textbox-con .fixed-search input").keypress(function(e){
			if(e.keyCode == 13){
				var keyword = jQuery(e.target).val().replace(/(\s|\,)/g, "");
				
				Wpfc_New_Dialog.clone.find(".wpfc-textbox-con").hide();
				jQuery(e.target).val("");
				jQuery('<li class="keyword-item"><a class="keyword-label">' + keyword + '</a></li>').insertBefore(Wpfc_New_Dialog.clone.find(".wpfc-add-new-keyword").closest(".keyword-item")).click(function(){
					jQuery(this).remove();
				});
			}
		});
	},
	click_event_add_new_keyword_button: function(){
		Wpfc_New_Dialog.clone.find(".wpfc-add-new-keyword").click(function(){
			Wpfc_New_Dialog.clone.find(".wpfc-textbox-con").show();
			Wpfc_New_Dialog.clone.find(".wpfc-textbox-con .fixed-search input").focus();
		});
	},
	insert_keywords: function(id, keywords){
		if(keywords){
			jQuery.each(keywords.split(","), function( index, value ) {
				jQuery('<li class="keyword-item"><a class="keyword-label">' + value + '</a></li>').insertBefore(jQuery("div[id^='" + id + "']").find(".wpfc-add-new-keyword").closest(".keyword-item")).click(function(){
					jQuery(this).remove();
				});
			});
		}

		
	},
};