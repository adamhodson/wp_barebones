<style type="text/css">
	.wpfc-exclude-item:hover{
		background-color: #E5E5E5;
	}
	.wpfc-exclude-item{
		float: left;
		width: 330.5px;
		margin-right: 7px;
	    -moz-border-radius:5px 5px 5px 5px;
	    -webkit-border-radius:5px 5px 5px 5px;
	    border-radius:5px 5px 5px 5px;
	    border:1px solid transparent;
	    cursor:pointer;
	    padding:9px;
		outline:none !important;
		list-style: outside none none;
	}
	.star{
	    float:left;
	    height:28px;
	    width:32px;
	    display: none;
	}
	.star img{
	    margin:9px 4px 4px;
	}

	.wpfc-exclude-item-form-title{
	    max-width:380px;
		font-weight:bold;
	    white-space:nowrap;
	    max-width:615px;
	    margin-bottom:3px;
	    text-overflow:ellipsis;
	    -o-text-overflow:ellipsis;
	    -moz-text-overflow:ellipsis;
	    -webkit-text-overflow:ellipsis;
	    line-height:1em;
	    font-family: Verdana,Geneva,Arial,Helvetica,sans-serif;
	}
	.wpfc-exclude-item-details{
	    font-size:11px;
	    color:#888;
		display:block;
	    white-space:nowrap;
	    font-family: Verdana,Geneva,Arial,Helvetica,sans-serif;
	    line-height:1.5em;
	}
	.wpfc-exclude-item-details b {
		display:inline;
		margin-left: 1px;

	}
	.wpfc-exclude-item-right{
		margin-right: 0;
	}
</style>
<div id="wpfc-modal-exclude" style="display:none;top: 10.5px; left: 226px; position: absolute; padding: 6px; height: auto; width: 560px; z-index: 10001;">
	<div style="height: 100%; width: 100%; background: none repeat scroll 0% 0% rgb(0, 0, 0); position: absolute; top: 0px; left: 0px; z-index: -1; opacity: 0.5; border-radius: 8px;">
	</div>
	<div style="z-index: 600; border-radius: 3px;">
		<div style="font-family:Verdana,Geneva,Arial,Helvetica,sans-serif;font-size:12px;background: none repeat scroll 0px 0px rgb(255, 161, 0); z-index: 1000; position: relative; padding: 2px; border-bottom: 1px solid rgb(194, 122, 0); height: 35px; border-radius: 3px 3px 0px 0px;">
			<table width="100%" height="100%">
				<tbody>
					<tr>
						<td valign="middle" style="vertical-align: middle; font-weight: bold; color: rgb(255, 255, 255); text-shadow: 0px 1px 1px rgba(0, 0, 0, 0.5); padding-left: 10px; font-size: 13px; cursor: move;">Exclude Page Wizard</td>
						<td width="20" align="center" style="vertical-align: middle;"></td>
						<td width="20" align="center" style="vertical-align: middle; font-family: Arial,Helvetica,sans-serif; color: rgb(170, 170, 170); cursor: default;">
							<div title="Close Window" class="close-wiz"></div>
						</td>
					</tr>
				</tbody>
			</table>
		</div>
		<div class="window-content-wrapper" style="padding: 8px;">
			<div style="z-index: 1000; height: auto; position: relative; display: inline-block; width: 100%;" class="window-content">


				<div id="wpfc-wizard-exclude" class="wpfc-cdn-pages-container">
					<div wpfc-cdn-page="1" class="wiz-cont" style="padding:0 0;min-height:37px;">

						<table width="100%" cellspacing="0" cellpadding="0" border="0" height="100%" style="background-color:#FFFFFF;border:1px solid #ccc !important;border-radius:10px;">
							<tbody>
								<tr>
									<td valign="top" id="cond-list">
										<table width="100%" cellspacing="0" cellpadding="5" border="0" class="cond-line active-line">
											<tbody>
												<tr>
													<td width="100" height="35" class="wpfc-condition-text" style="padding-left:10px;font-family: Verdana,Geneva,Arial,Helvetica,sans-serif;font-size: 12px;">If REQUEST_URI</td>
													<td class="" width="95">
														<select name="wpfc-exclude-rule-prefix" style="width: 98px !important;">
															<option selected="" value=""></option>
															<option value="homepage">Home Page</option>
															<option value="category">Categories</option>
															<option value="tag">Tags</option>
															<option value="post">Posts</option>
															<option value="page">Pages</option>

															<option value="archive">Archives</option>

															<option value="attachment">Attachments</option>
										    				<option value="startwith">Starts With</option>
										    				<option value="contain">Contains</option>
										    				<option value="exact">Is Equal To</option>
										    				<option value="googleanalytics">has Google Analytics Parameters</option>
										    			</select>
										    		</td>
										    		<td width="300">
										    			<div class="wpfc-exclude-rule-line-middle">
										    				<input type="text" name="wpfc-exclude-rule-content" style="width:300px;">
										    				<input type="hidden" name="wpfc-exclude-rule-type" style="width:300px;">
										    			</div>
										    		</td>
										    	</tr>
										    </tbody>
										</table>
									</td>
								</tr>
							</tbody>
						</table>
					</div>
				</div>
			</div>
		</div>
		<div class="window-buttons-wrapper" style="padding: 0px; display: inline-block; width: 100%; border-top: 1px solid rgb(255, 255, 255); background: none repeat scroll 0px 0px rgb(222, 222, 222); z-index: 999; position: relative; text-align: right; border-radius: 0px 0px 3px 3px;">
			<div style="padding: 12px; height: 23px;">
				<button class="wpfc-dialog-buttons buttons-blood" type="button" action="remove">
					<span>Remove Rule</span>
				</button>
				<button class="wpfc-dialog-buttons" type="button" action="back">
					<span>Back</span>
				</button>
				<button class="wpfc-dialog-buttons" type="button" action="next">
					<span>Next</span>
				</button>
				<button class="wpfc-dialog-buttons" type="button" action="close">
					<span>Close</span>
				</button>
				<button class="wpfc-dialog-buttons buttons-green" type="button" action="finish">
					<span>Save</span>
				</button>
			</div>
		</div>
	</div>
</div>
<script type="text/javascript">
	var WpFcExcludePages = {
		rules: [],
		init: function(rules){
			this.rules = rules;
			this.insert_existing_rules();
			this.click_event_for_add_button();
			this.reorder();
		},
		remove_rule: function(clone_modal_id, number){
			jQuery("div.wpfc-exclude-item[wpfc-exclude-item-number='" + number + "']").remove();
			jQuery("div.wpfc-exclude-rule-line[wpfc-exclude-rule-number='" + number + "']").remove();
			Wpfc_Dialog.remove(clone_modal_id, number);

			this.save(function(){});
		},
		add_item: function(number, e){
			var self = this;
			var item = jQuery(".wpfc-exclude-item").first().clone();

			if(jQuery(".wpfc-exclude-item").length%2 == 0){
				console.log(item, jQuery(".wpfc-exclude-item").length)
				item.addClass("wpfc-exclude-item-right");
			}


			item.attr("wpfc-exclude-item-number", number);
			item.attr("prefix", e.prefix);
			item.attr("content", e.content);
			item.attr("type", e.type);

			item.find(".wpfc-exclude-item-url").html(self.create_url_description(e.prefix, e.content, e.type));

			item.find(".wpfc-exclude-item-form-title").html(self.create_title(e.prefix, e.content));

			item.click(function(){
				var clone_modal = jQuery("#wpfc-modal-exclude").clone();
				var clone_modal_id = "wpfc-modal-exclude-" + new Date().getTime();

				clone_modal.find("select").change(function(e){
					if(jQuery(this).val().match(/^(homepage|category|tag|archive|post|page|attachment|googleanalytics)$/)){
						clone_modal.find("input[name='wpfc-exclude-rule-content']").hide();
						clone_modal.find("input[name='wpfc-exclude-rule-content']").val(jQuery(this).val());
					}else{
						clone_modal.find("input[name='wpfc-exclude-rule-content']").show();
						clone_modal.find("input[name='wpfc-exclude-rule-content']").val("");
					}
				});

				if(e.prefix.match(/^(homepage|category|tag|archive|post|page|attachment|googleanalytics)$/)){
					clone_modal.find("input[name='wpfc-exclude-rule-content']").hide();
				}

				clone_modal.attr("id", clone_modal_id);
				clone_modal.find("select[name='wpfc-exclude-rule-prefix']").val(jQuery(this).attr("prefix"));
				clone_modal.find("input[name='wpfc-exclude-rule-content']").val(jQuery(this).attr("content"));
				clone_modal.find("input[name='wpfc-exclude-rule-type']").val(jQuery(this).attr("type"));

				if(e.type != "page"){
					if(e.type == "useragent"){
						clone_modal.find(".wpfc-condition-text").text("If User-Agent");
					}else if(e.type == "css"){
						clone_modal.find(".wpfc-condition-text").text("If CSS Url");
					}else if(e.type == "js"){
						clone_modal.find(".wpfc-condition-text").text("If JS Url");
					}else if(e.type == "cookie"){
						clone_modal.find(".wpfc-condition-text").text("If Cookie");
					}

					clone_modal.find("select[name='wpfc-exclude-rule-prefix'] option").each(function(){
						if(this.value != "contain"){
							jQuery(this).remove();
						}
					});
				}
				


				jQuery("#wpfc-modal-exclude").after(clone_modal);

				if(typeof e.editable == "undefined"){
					Wpfc_Dialog.dialog(clone_modal_id, {"close" : 
						function(){
						},
						"remove" : 
						function(){
							self.remove_rule(clone_modal_id, number);
						},
						"finish" :
						function(){
							var prefix = clone_modal.find("select[name='wpfc-exclude-rule-prefix']").val();
							var content = clone_modal.find("input[name='wpfc-exclude-rule-content']").val();
							var type = clone_modal.find("input[name='wpfc-exclude-rule-type']").val();
							
							jQuery("div.wpfc-exclude-rule-line[wpfc-exclude-rule-number='" + number + "']").find("select[name='wpfc-exclude-rule-prefix-" + number + "']").val(prefix);
							jQuery("div.wpfc-exclude-rule-line[wpfc-exclude-rule-number='" + number + "']").find("input[name='wpfc-exclude-rule-content-" + number + "']").val(content);

							if(self.is_empty_values(prefix, content)){
								Wpfc_Dialog.remove(clone_modal_id);

								self.save(function(){
									jQuery("div.wpfc-exclude-item[wpfc-exclude-item-number='" + number + "']").attr("prefix", prefix);
									jQuery("div.wpfc-exclude-item[wpfc-exclude-item-number='" + number + "']").attr("content", content);

									jQuery("div.wpfc-exclude-item[wpfc-exclude-item-number='" + number + "']").find(".wpfc-exclude-item-url").html(self.create_url_description(prefix, content, type));
									
									jQuery("div.wpfc-exclude-item[wpfc-exclude-item-number='" + number + "']").find(".wpfc-exclude-item-form-title").html(self.create_title(prefix, content));
								});
							}
						}
					});
				}else if(e.editable == false){
					Wpfc_Dialog.dialog(clone_modal_id, {"close" : function(){}});
				}
			});
			
			item.show();

			jQuery(".wpfc-exclude-" + e.type + "-list").append(item);

			this.reorder();
		},
		create_title: function(prefix, content){
			var title = "";

			if(prefix == "exact"){
				title = "Is Equal To: " + content;
			}else if(prefix == "startwith"){
				title = "Start With: " + content;
			}else if(prefix == "contain"){
				title = "Contains: " + content;
			}else if(prefix == "homepage"){
				title = "Home Page";
			}else if(prefix == "tag"){
				title = "Tags";
			}else if(prefix == "archive"){
				title = "Archives";
			}else if(prefix == "category"){
				title = "Categories";
			}else if(prefix == "post"){
				title = "Posts";
			}else if(prefix == "page"){
				title = "Pages";
			}else if(prefix == "attachment"){
				title = "Attachments";
			}else if(prefix == "googleanalytics"){
				title = "Google Analytics Parameters";
			}

			return title;
		},
		create_url_description: function(prefix, content, type){
				var request_uri = content;
				var b_start = "<b style='font-size:11px;color:#FFA100;'>";
				var b_end = "</b>"

				if(prefix == "exact"){
					request_uri = b_start + content + b_end;
				}else if(prefix == "startwith"){
					request_uri = b_start + content + b_end + '(.*)';
				}else if(prefix == "contain"){
					request_uri = '(.*)' + b_start + content + b_end + '(.*)';
				}else if(prefix == "homepage"){
					request_uri = "";
				}

				if(type == "page" || type == "css" || type == "js"){
					if(prefix.match(/^(homepage|category|tag|archive|post|page|attachment|googleanalytics)$/)){
						if(prefix == "homepage"){
							return "The " + b_start + "homepage" + b_end + " has been excluded";
						}else{
							return "All" + " " + b_start + this.create_title(prefix).toLowerCase() + b_end + " " + "have been excluded";
						}
					}else{
						return "<?php echo preg_replace("/(https?\:\/\/[^\/]+).*/", "$1", site_url());?>" + "/" + request_uri;
					}
				}else if(type == "useragent"){
					return "User-Agent: " + request_uri;
				}else if(type == "cookie"){
					return "Cookie: " + request_uri;
				}

		},
		add_line: function(number, e){
			var line = jQuery(".wpfc-exclude-rule-line").first().closest(".wpfc-exclude-rule-line").clone();
			
			line.attr("wpfc-exclude-rule-number", number);

			line.find(".wpfc-exclude-rule-line-add").remove();
			line.find(".wpfc-exclude-rule-line-delete").show();
			line.find("select[name^='wpfc-exclude-rule-prefix']").attr("name", "wpfc-exclude-rule-prefix-" + number).val(e.prefix);
			line.find("input[name^='wpfc-exclude-rule-content']").attr("name", "wpfc-exclude-rule-content-" + number).val(e.content);
			line.find("input[name^='wpfc-exclude-rule-type']").attr("name", "wpfc-exclude-rule-type-" + number).val(e.type);

			jQuery(".wpfc-exclude-rule-container").append(line);
		},
		click_event_for_add_button: function(){
			var self = this;

			jQuery(".wpfc-add-new-exclude-button").click(function(e){
				var clone_modal = jQuery("#wpfc-modal-exclude").clone();
				//var number = jQuery("div.wpfc-exclude-rule-line[wpfc-exclude-rule-number]").length;
				var number = new Date().getTime();
				var clone_modal_id = "wpfc-modal-exclude-" + new Date().getTime();
				var clone_modal_type = jQuery(e.currentTarget).attr("data-type");

				clone_modal.attr("id", clone_modal_id);
				clone_modal.find("input[name='wpfc-exclude-rule-type']").val(clone_modal_type);
				
				if(clone_modal_type != "page"){
					clone_modal.find("select[name='wpfc-exclude-rule-prefix'] option").each(function(){
						if(this.value != "contain"){
							jQuery(this).remove();
						}
					});

					if(clone_modal_type == "useragent"){
						clone_modal.find(".wpfc-condition-text").text("If User-Agent");
					}else if(clone_modal_type == "css"){
						clone_modal.find(".wpfc-condition-text").text("If CSS Url");
					}else if(clone_modal_type == "js"){
						clone_modal.find(".wpfc-condition-text").text("If JS Url");
					}else if(clone_modal_type == "cookie"){
						clone_modal.find(".wpfc-condition-text").text("If Cookie");
					}
				}




				clone_modal.find("select").change(function(){
					if(jQuery(this).val().match(/^(homepage|category|tag|archive|post|page|attachment|googleanalytics)$/)){
						clone_modal.find("input[name='wpfc-exclude-rule-content']").hide();
						clone_modal.find("input[name='wpfc-exclude-rule-content']").val(jQuery(this).val());
					}else{
						clone_modal.find("input[name='wpfc-exclude-rule-content']").show();
						clone_modal.find("input[name='wpfc-exclude-rule-content']").val("");
					}
				});
				
				jQuery("#wpfc-modal-exclude").after(clone_modal);

				Wpfc_Dialog.dialog(clone_modal_id, {"finish" : 
					function(){
						var prefix = clone_modal.find("select[name='wpfc-exclude-rule-prefix']").val();
						var content = clone_modal.find("input[name='wpfc-exclude-rule-content']").val();
						var type = clone_modal.find("input[name='wpfc-exclude-rule-type']").val();

						content = self.remove_host_name(content);

						content = content.replace(/^\/|\/$/g, '');

						if(self.is_empty_values(prefix, content)){
							self.add_line(number + 1, {"prefix" : prefix, "content" : content, "type" : type});

							Wpfc_Dialog.remove(clone_modal_id);
							
							self.save(function(){
								self.add_item(number + 1, {"prefix" : prefix, "content" : content, "type" : type});
							});
						}
					},
					"close" : 
					function(){
					}
				});
			});
		},
		save: function(callback){
			var self = this, rule_number, prefix, content, rule, rules = [];

			jQuery("form div.wpfc-exclude-rule-line").each(function(i, e){
				rule_number = jQuery(e).attr("wpfc-exclude-rule-number");
				prefix = jQuery(e).find("select[name^='wpfc-exclude-rule-prefix']").val();
				type = jQuery(e).find("input[name^='wpfc-exclude-rule-type']").val();
				content = jQuery(e).find("input[name^='wpfc-exclude-rule-content']").val();

				content = self.remove_host_name(content); 

				rules.push({"prefix" : prefix, "content" : content, "type" : type});
			});

			jQuery("#revert-loader-toolbar").show();

			jQuery.ajax({
				type: 'POST',
				dataType: "json",
				url: ajaxurl,
				data : {"action": "wpfc_save_exclude_pages", "rules" : rules, security: '<?php echo wp_create_nonce( "wpfc-save-exclude-ajax-nonce" ); ?>'},
			    success: function(res){
			    	if(res.success){
			    		jQuery("#revert-loader-toolbar").hide();
			    		callback();
			    		self.reorder();
			    	}else{
			    		alert("The rule cannot be added...");
			    	}
			    },
			    error: function(e) {
			    	alert("unknown error");
			    }
			  });
		},
		insert_existing_rules: function(){
			var self = this;

			self.add_item(new Date().getTime(), {"type" : "page", "prefix" : "exact", "content" : "wp-login.php", "editable" : false});
			//self.add_item(new Date().getTime(), {"prefix" : "startwith", "content" : "wp-content", "editable" : false});
			self.add_item(new Date().getTime(), {"type" : "page", "prefix" : "startwith", "content" : "wp-admin", "editable" : false});
			self.add_item(new Date().getTime(), {"type" : "useragent", "prefix" : "contain", "content" : "facebookexternalhit", "editable" : false});

			self.add_item(new Date().getTime(), {"type" : "useragent", "prefix" : "contain", "content" : "LinkedInBot", "editable" : false});
			
			self.add_item(new Date().getTime(), {"type" : "useragent", "prefix" : "contain", "content" : "WhatsApp", "editable" : false});

			self.add_item(new Date().getTime(), {"type" : "useragent", "prefix" : "contain", "content" : "Twitterbot", "editable" : false});


			if(typeof this.rules != "undefined" && this.rules && this.rules.length > 0){
				jQuery.each(self.rules, function(i, e){
					if(i > 0){
					}
					e.type = e.type ? e.type : "page";
					
					self.add_line(i + 1, e);
					self.add_item(i + 1, e);
				});
			}
		},
		is_empty_values: function(prefix, content){
			if(prefix){
				jQuery("#wpfc-wizard-exclude select[name='wpfc-exclude-rule-prefix']").css({'border-color': '#ddd'});
			}else{
				jQuery("#wpfc-wizard-exclude select[name='wpfc-exclude-rule-prefix']").css({'border-color': 'red'});
			}

			if(content){
				jQuery("#wpfc-wizard-exclude input[name='wpfc-exclude-rule-content']").css({'border-color': '#ddd'});
			}else{
				jQuery("#wpfc-wizard-exclude input[name='wpfc-exclude-rule-content']").css({'border-color': 'red'});
			}

			if(prefix && content){
				return true;
			}
			
			return false;
		},
		remove_host_name: function(content){
			//to replace the urls which start with http:// or www. or with Host_Name
			content = content.replace(new RegExp('.*' + location.hostname.replace(/www\./, "") + "\/", "gi"), "");
			content = content.replace(/\/$/, "");

			return content;
		},
		reorder: function(type){
			jQuery("div.tab6 div[class^='wpfc-exclude'][class$='-list']").each(function(i,e){
				var type = jQuery(e).attr("class").match(/wpfc-exclude-([^-]+)-list/);
				
				if(typeof type[1] != "undefined"){
					jQuery("div.wpfc-exclude-" + type[1] + "-list div.wpfc-exclude-item").each(function(i, e){
						jQuery(e).removeClass("wpfc-exclude-item-right");

						if(i%2 != 0){
							jQuery(e).addClass("wpfc-exclude-item-right");
						}
					});
				}
			});
		}
	};
</script>



