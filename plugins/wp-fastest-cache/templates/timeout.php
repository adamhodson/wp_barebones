<style type="text/css">
	.wpfc-timeout-item:hover{
		background-color: #E5E5E5;
	}
	.wpfc-timeout-item{
		float: left;
		width: 330.5px;
		margin-right: 7px;
		margin-left: 20px;
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

	.wpfc-timeout-item-form-title{
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
	.wpfc-timeout-item-details{
	    font-size:11px;
	    color:#888;
		display:block;
	    white-space:nowrap;
	    font-family: Verdana,Geneva,Arial,Helvetica,sans-serif;
	    line-height:1.5em;
	}
	.wpfc-timeout-item-details b {
		display:inline;
		margin-left: 1px;

	}
	.wpfc-timeout-item-right{
		margin-right: 0;
		margin-left: 0;
	}
</style>
<div id="wpfc-modal-timeout" style="display:none;top: 10.5px; left: 226px; position: absolute; padding: 6px; height: auto; width: 560px; z-index: 10001;">
	<div style="height: 100%; width: 100%; background: none repeat scroll 0% 0% rgb(0, 0, 0); position: absolute; top: 0px; left: 0px; z-index: -1; opacity: 0.5; border-radius: 8px;">
	</div>
	<div style="z-index: 600; border-radius: 3px;">
		<div style="font-family:Verdana,Geneva,Arial,Helvetica,sans-serif;font-size:12px;background: none repeat scroll 0px 0px rgb(255, 161, 0); z-index: 1000; position: relative; padding: 2px; border-bottom: 1px solid rgb(194, 122, 0); height: 35px; border-radius: 3px 3px 0px 0px;">
			<table width="100%" height="100%">
				<tbody>
					<tr>
						<td valign="middle" style="vertical-align: middle; font-weight: bold; color: rgb(255, 255, 255); text-shadow: 0px 1px 1px rgba(0, 0, 0, 0.5); padding-left: 10px; font-size: 13px; cursor: move;">Cache Timeout Wizard</td>
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


				<div id="wpfc-wizard-timeout" class="wpfc-cdn-pages-container">
					<div wpfc-cdn-page="1" class="wiz-cont" style="padding:0 0;min-height:37px;">

						<table width="100%" cellspacing="0" cellpadding="0" border="0" height="100%" style="background-color:#FFFFFF;border:1px solid #ccc !important;border-radius:10px;">
							<tbody>
								<tr>
									<td valign="top" id="cond-list">
										<table width="100%" cellspacing="0" cellpadding="5" border="0" class="cond-line active-line">
											<tbody>
												<tr>
													<td width="100" height="30" class="" style="padding-left:10px;font-family: Verdana,Geneva,Arial,Helvetica,sans-serif;font-size: 12px;">If REQUEST_URI</td>
													<td class="" width="95">
														<select name="wpfc-timeout-rule-prefix">
															<option selected="" value=""></option>
															<option value="all">All</option>
															<option value="homepage">Home Page</option>
										    				<option value="startwith">Starts With</option>
										    				<option value="exact">Is Equal To</option>
										    				<!-- <option value="contain">Contains</option> -->
										    			</select>
										    		</td>
										    		<td width="300">
										    			<div class="wpfc-timeout-rule-line-middle">
										    				<input type="text" name="wpfc-timeout-rule-content" style="width:300px;">
										    			</div>
										    		</td>
										    	</tr>
										    </tbody>
										</table>
									</td>
								</tr>
							</tbody>
						</table>

						<div id="cond-single" style="border-top:1px solid #ccc; margin:10px 0px 5px 0px;"></div>

						<table width="100%" cellspacing="0" cellpadding="0" border="0" height="100%" style="background-color:#FFFFFF;border:1px solid #ccc !important;border-radius:10px;">
							<tbody>
								<tr>
									<td valign="top">
										<table width="100%" cellspacing="0" cellpadding="5" border="0" class="cond-line active-line">
											<tbody>
												<tr>
													<td width="10">Then</td>
													<td class="" width="95">
														<select name="wpfc-timeout-rule-schedule">
															<?php
																$schedules = wp_get_schedules();
																$first = true;
																foreach ($schedules as $key => $value) {
																	if($first){
																		echo "<option value=''>Choose One</option>";
																		$first = false;
																	}
																	echo "<option value='{$key}'>{$value["display"]}</option>";
																}
															?>
														</select> 
										    		</td>
													<td width="300" height="30" class="" style="font-family: Verdana,Geneva,Arial,Helvetica,sans-serif;font-size: 12px;">
														<span class="wpfc-timeout-at-text" style="padding-right:5px;display:none;">at</span>
														<select name="wpfc-timeout-rule-hour" style="display:none;">
															<?php
																for ($i=0; $i < 24; $i++) { 
																	?>
																	<option value="<?php echo $i; ?>"><?php echo str_pad($i, 2, '0', STR_PAD_LEFT); ?></option>
																	<?php
																}
															?>
										    			</select>
										    			<select name="wpfc-timeout-rule-minute" style="display:none;">
															<?php
																for ($i=0; $i < 60; $i++) { 
																	?>
																	<option value="<?php echo $i; ?>"><?php echo str_pad($i, 2, '0', STR_PAD_LEFT); ?></option>
																	<?php
																}
															?>
										    			</select>
										    			<span>delete the files</span>
													</td>
													<td width="100"></td>
										    	</tr>
										    </tbody>
										</table>
									</td>
								</tr>
							</tbody>
						</table>


						<table class="wpfc-server-time-cont" width="100%" cellspacing="0" cellpadding="0" border="0" height="100%" style="background: lightYellow; border: 1px solid #DCDCBC !important;border-radius:10px;margin-top:5px;">
							<tbody>
								<tr>
									<td valign="top" id="cond-list">
										<table width="100%" cellspacing="0" cellpadding="5" border="0" class="cond-line active-line">
											<tbody>
												<tr>
										    		<td class="" width="300" style="text-align: center;">
														<label>Server Time: </label><label class="wpfc-server-time"><?php echo date("H:i:s"); ?></label>
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
	var WpFcTimeout = {
		rules: [],
		schedules: {},
		init: function(rules){
			this.rules = rules;
			this.insert_existing_rules();
			this.click_event_for_add_button();

			setInterval(function(){ WpFcTimeout.server_clock(); }, 1000);
		},
		server_clock: function(){
			jQuery(".wpfc-server-time").each(function(i, e){
		        var time = jQuery(e).text().split(":");

		        time[2]++;

		        if(time[2] > 59){
		            time[2] = "0";
		            time[1]++;
		        }

	        	if(time[1] > 59){
	                time[1] = "0";
	                time[0]++;
	            }

	            if(time[0] > 23){
	                time[0] = "0";
	            }

	            jQuery(time).each(function(i, e){
	            	if((time[i] < 10) && ((time[i] + "").length < 2)){
	            		time[i] = "0" + time[i];
	            	}
	            });

	            jQuery(e).text(time.join(":"));
		    });
		},
		remove_rule: function(clone_modal_id, number){
			jQuery("div.wpfc-timeout-rule-line[wpfc-timeout-rule-number='" + number + "']").remove();
			
			Wpfc_Dialog.remove(clone_modal_id);
			
			this.save(function(){
				jQuery("div.wpfc-timeout-item[wpfc-timeout-item-number='" + number + "']").remove();
			});
		},
		add_item: function(number, e){
			var self = this;
			var item = jQuery(".wpfc-timeout-item").first().clone();

			item.attr("wpfc-timeout-item-number", number);
			item.attr("prefix", e.prefix);
			item.attr("content", e.content);
			item.attr("schedule", e.schedule);

			if(typeof e.hour != "undefined" && typeof e.minute != "undefined"){
				item.attr("hour", e.hour);
				item.attr("minute", e.minute);
			}

			item.find(".wpfc-timeout-item-url").html(self.create_url_description(e.prefix, e.content));

			if(typeof e.hour != "undefined" && typeof e.minute != "undefined"){
				item.find(".wpfc-timeout-item-form-title").html(self.create_title(e.prefix, e.content, e.schedule, e.hour, e.minute));
			}else{
				item.find(".wpfc-timeout-item-form-title").html(self.create_title(e.prefix, e.content, e.schedule));
			}

			item.click(function(){
				var clone_modal = jQuery("#wpfc-modal-timeout").clone();
				var clone_modal_id = "wpfc-modal-timeout-" + new Date().getTime();

				self.hide_input_content(clone_modal);
				self.show_hour_and_minute(clone_modal);

				if(jQuery(this).attr("schedule").match(/^(onceaday|daily)/i)){
					clone_modal.find(".wpfc-timeout-at-text").show();
					clone_modal.find("select[name='wpfc-timeout-rule-hour']").show();
					clone_modal.find("select[name='wpfc-timeout-rule-minute']").show();

					clone_modal.find("select[name='wpfc-timeout-rule-hour']").val(jQuery(this).attr("hour"));
					clone_modal.find("select[name='wpfc-timeout-rule-minute']").val(jQuery(this).attr("minute"));
				}

				if(e.prefix == "homepage" || e.prefix == "all"){
					clone_modal.find("input").attr("name", "wpfc-timeout-rule-content").hide();
				}

				clone_modal.attr("id", clone_modal_id);
				clone_modal.find("select[name='wpfc-timeout-rule-schedule']").val(jQuery(this).attr("schedule"));
				clone_modal.find("select[name='wpfc-timeout-rule-prefix']").val(jQuery(this).attr("prefix"));
				clone_modal.find("input[name='wpfc-timeout-rule-content']").val(jQuery(this).attr("content"));


				jQuery("#wpfc-modal-timeout").after(clone_modal);

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
							var prefix = clone_modal.find("select[name='wpfc-timeout-rule-prefix']").val();
							var content = clone_modal.find("input[name='wpfc-timeout-rule-content']").val();
							var schedule = clone_modal.find("select[name='wpfc-timeout-rule-schedule']").val();
							var hour = clone_modal.find("select[name='wpfc-timeout-rule-hour']").val();
							var minute = clone_modal.find("select[name='wpfc-timeout-rule-minute']").val();
							
							content = self.remove_host_name(content);
							content = content.replace(/^\/|\/$/g, '');

							jQuery("div.wpfc-timeout-rule-line[wpfc-timeout-rule-number='" + number + "']").find("select[name='wpfc-timeout-rule-prefix-" + number + "']").val(prefix);
							jQuery("div.wpfc-timeout-rule-line[wpfc-timeout-rule-number='" + number + "']").find("input[name='wpfc-timeout-rule-content-" + number + "']").val(content);
							jQuery("div.wpfc-timeout-rule-line[wpfc-timeout-rule-number='" + number + "']").find("input[name='wpfc-timeout-rule-schedule-" + number + "']").val(schedule);
							jQuery("div.wpfc-timeout-rule-line[wpfc-timeout-rule-number='" + number + "']").find("input[name='wpfc-timeout-rule-hour-" + number + "']").val(hour);
							jQuery("div.wpfc-timeout-rule-line[wpfc-timeout-rule-number='" + number + "']").find("input[name='wpfc-timeout-rule-minute-" + number + "']").val(minute);

							if(self.is_empty_values(prefix, content, schedule)){
								Wpfc_Dialog.remove(clone_modal_id);

								self.save(function(){
									jQuery("div.wpfc-timeout-item[wpfc-timeout-item-number='" + number + "']").attr("prefix", prefix);
									jQuery("div.wpfc-timeout-item[wpfc-timeout-item-number='" + number + "']").attr("content", content);
									jQuery("div.wpfc-timeout-item[wpfc-timeout-item-number='" + number + "']").attr("schedule", schedule);
									jQuery("div.wpfc-timeout-item[wpfc-timeout-item-number='" + number + "']").attr("hour", hour);
									jQuery("div.wpfc-timeout-item[wpfc-timeout-item-number='" + number + "']").attr("minute", minute);

									jQuery("div.wpfc-timeout-item[wpfc-timeout-item-number='" + number + "']").find(".wpfc-timeout-item-url").html(self.create_url_description(prefix, content));
									
									jQuery("div.wpfc-timeout-item[wpfc-timeout-item-number='" + number + "']").find(".wpfc-timeout-item-form-title").html(self.create_title(prefix, content, schedule, hour, minute));
								});
							}
						}
					});
				}else if(e.editable == false){
					Wpfc_Dialog.dialog(clone_modal_id, {"close" : function(){}});
				}
			});
			
			item.show();

			jQuery(".wpfc-timeout-list").append(item);
		},
		create_title: function(prefix, content, schedule, hour, minute){
			var title = "";

			if(prefix == "exact"){
				title = "Is Equal To: " + content;
			}else if(prefix == "startwith"){
				title = "Start With: " + content;
			}else if(prefix == "contain"){
				title = "Contains: " + content;
			}else if(prefix == "homepage"){
				title = "Home Page";
			}else if(prefix == "all"){
				title = "The All Cache";
			}

			if(typeof this.schedules[schedule] != "undefined"){
				title = title + ' - <span style="color: green;">' + this.schedules[schedule].display + '</span>';
			}else{
				title = title + ' - <span style="color: green;">' + schedule + '</span>';
			}

			if(schedule.match(/^(onceaday|daily)/i)){
				if(typeof hour != "undefined" && typeof minute != "undefined"){
					minute = ((minute + "").length > 1) ? minute : "0" + (minute + "");
					hour = ((hour + "").length > 1) ? hour : "0" + (hour + "");
					
					title = title + '/<span style="color:red;font-size:10px;">' + hour + ":" + minute + '</span>';
				}
			}

			return title;
		},
		create_url_description: function(prefix, content){
				var request_uri = content;
				var b_start = "<b style='font-size:11px;color:#FFA100;'>";
				var b_end = "</b>"

				if(prefix == "exact"){
					request_uri = b_start + content + b_end;
				}else if(prefix == "startwith"){
					request_uri = b_start + content + b_end + '(.*)';
				}else if(prefix == "contain"){
					request_uri = '(.*)' + b_start + content + b_end + '(.*)';
				}else if(prefix == "homepage" || prefix == "all"){
					request_uri = "";
				}

				return "<?php echo home_url();?>" + "/" + request_uri;
		},
		add_line: function(number, e){
			var line = jQuery(".wpfc-timeout-rule-line").first().closest(".wpfc-timeout-rule-line").clone();
			
			line.attr("wpfc-timeout-rule-number", number);

			line.find(".wpfc-timeout-rule-line-add").remove();
			line.find(".wpfc-timeout-rule-line-delete").show();
			line.find("select").attr("name", "wpfc-timeout-rule-prefix-" + number).val(e.prefix);
			line.find("input[name='wpfc-timeout-rule-content']").attr("name", "wpfc-timeout-rule-content-" + number).val(e.content);
			line.find("input[name='wpfc-timeout-rule-schedule']").attr("name", "wpfc-timeout-rule-schedule-" + number).val(e.schedule);
			line.find("input[name='wpfc-timeout-rule-hour']").attr("name", "wpfc-timeout-rule-hour-" + number).val(e.hour);
			line.find("input[name='wpfc-timeout-rule-minute']").attr("name", "wpfc-timeout-rule-minute-" + number).val(e.minute);

			console.log(e);

			jQuery(".wpfc-timeout-rule-container").append(line);
		},
		click_event_for_add_button: function(){
			var self = this;

			jQuery(".wpfc-add-new-timeout-button").click(function(e){
				if(jQuery(e.target).attr("disable-wp-cron")){
					if(jQuery("div[id^='wpfc-modal-disablewpcron-']").length === 0){
						Wpfc_New_Dialog.dialog("wpfc-modal-disablewpcron", {close: "default"});
						Wpfc_New_Dialog.show_button("close");
					}
				}else{
					var clone_modal = jQuery("#wpfc-modal-timeout").clone();
					//var number = jQuery("div.wpfc-timeout-rule-line[wpfc-timeout-rule-number]").length;
					var number = new Date().getTime();
					var clone_modal_id = "wpfc-modal-timeout-" + new Date().getTime();

					clone_modal.attr("id", clone_modal_id);

					self.hide_input_content(clone_modal);
					self.show_hour_and_minute(clone_modal);
					
					jQuery("#wpfc-modal-timeout").after(clone_modal);

					Wpfc_Dialog.dialog(clone_modal_id, {"finish" : 
						function(){
							var prefix = clone_modal.find("select[name='wpfc-timeout-rule-prefix']").val();
							var content = clone_modal.find("input[name='wpfc-timeout-rule-content']").val();
							var schedule = clone_modal.find("select[name='wpfc-timeout-rule-schedule']").val();
							var hour = clone_modal.find("select[name='wpfc-timeout-rule-hour']").val();
							var minute = clone_modal.find("select[name='wpfc-timeout-rule-minute']").val();

							content = self.remove_host_name(content);
							content = content.replace(/^\/|\/$/g, '');

							if(self.is_empty_values(prefix, content, schedule)){
								self.add_line(number + 1, {"prefix" : prefix, "content" : content, "schedule" : schedule, "hour" : hour, "minute" : minute});

								Wpfc_Dialog.remove(clone_modal_id);
								
								self.save(function(){
									self.add_item(number + 1, {"prefix" : prefix, "content" : content, "schedule" : schedule, "hour" : hour, "minute" : minute});
								});
							}
						},
						"close" : 
						function(){
						}
					});
				}
			});
		},
		save: function(callback){
			var rule_number, prefix, schedule, content, hour, minute, rule, rules = [], self = this;

			jQuery("form div.wpfc-timeout-rule-line").each(function(i, e){
				rule_number = jQuery(e).attr("wpfc-timeout-rule-number");
				prefix = jQuery(e).find("select[name^='wpfc-timeout-rule-prefix']").val();
				content = jQuery(e).find("input[name^='wpfc-timeout-rule-content']").val();
				schedule = jQuery(e).find("input[name^='wpfc-timeout-rule-schedule']").val();
				hour = jQuery(e).find("input[name^='wpfc-timeout-rule-hour']").val();
				minute = jQuery(e).find("input[name^='wpfc-timeout-rule-minute']").val();

				content = self.remove_host_name(content);
				content = content.replace(/^\/|\/$/g, '');


				rules.push({"prefix" : prefix, "content" : content, "schedule" : schedule, "hour" : hour, "minute" : minute});
			});

			jQuery("#revert-loader-toolbar").show();

			jQuery.ajax({
				type: 'POST',
				dataType: "json",
				url: ajaxurl,
				data : {"action": "wpfc_save_timeout_pages", "rules" : rules, security: '<?php echo wp_create_nonce( "wpfc-save-timeout-ajax-nonce" ); ?>'},
			    success: function(res){
			    	if(res.success){
			    		jQuery("#revert-loader-toolbar").hide();
			    		callback();
			    		self.set_right_class_for_item();
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

			if(typeof this.rules != "undefined" && this.rules && this.rules.length > 0){
				jQuery.each(self.rules, function(i, e){
					if(i > 0){
					}

					self.add_line(i + 1, e);
					self.add_item(i + 1, e);
				});

				self.set_right_class_for_item();
			}
		},
		set_right_class_for_item: function(){
    		jQuery(".wpfc-timeout-item").each(function(i, e){
    			if(i%2 == 0){
    				jQuery(e).addClass("wpfc-timeout-item-right");
    			}else{
    				jQuery(e).removeClass("wpfc-timeout-item-right");
    			}
    		});
		},
		is_empty_values: function(prefix, content, schedule){
			if(prefix){
				jQuery("#wpfc-wizard-timeout select[name='wpfc-timeout-rule-prefix']").css({'border-color': '#ddd'});
			}else{
				jQuery("#wpfc-wizard-timeout select[name='wpfc-timeout-rule-prefix']").css({'border-color': 'red'});
			}

			if(content){
				jQuery("#wpfc-wizard-timeout input[name='wpfc-timeout-rule-content']").css({'border-color': '#ddd'});
			}else{
				jQuery("#wpfc-wizard-timeout input[name='wpfc-timeout-rule-content']").css({'border-color': 'red'});
			}

			if(schedule){
				jQuery("#wpfc-wizard-timeout select[name='wpfc-timeout-rule-schedule']").css({'border-color': '#ddd'});
			}else{
				jQuery("#wpfc-wizard-timeout select[name='wpfc-timeout-rule-schedule']").css({'border-color': 'red'});
			}

			if(prefix && content && schedule){
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
		hide_input_content: function(clone_modal){
			clone_modal.find("select[name='wpfc-timeout-rule-prefix']").change(function(){
				if(jQuery(this).val() == "homepage" || jQuery(this).val() == "all"){
					clone_modal.find("input").hide();
					clone_modal.find("input").attr("name", "wpfc-timeout-rule-content").val(jQuery(this).val());
				}else{
					clone_modal.find("input").show();
					clone_modal.find("input").attr("name", "wpfc-timeout-rule-content").val("");
				}
			});
		},
		show_hour_and_minute: function(clone_modal){
			clone_modal.find("select[name='wpfc-timeout-rule-schedule']").change(function(e){
				if(jQuery(this).val().match(/^(onceaday|daily)/i)){
					clone_modal.find(".wpfc-timeout-at-text").show();
					clone_modal.find("select[name='wpfc-timeout-rule-hour']").show();
					clone_modal.find("select[name='wpfc-timeout-rule-minute']").show();
					clone_modal.find(".wpfc-server-time-cont").show();
				}else{
					clone_modal.find(".wpfc-timeout-at-text").hide();
					clone_modal.find("select[name='wpfc-timeout-rule-hour']").hide();
					clone_modal.find("select[name='wpfc-timeout-rule-minute']").hide();
					clone_modal.find(".wpfc-server-time-cont").hide();
				}
			});
		}
	};
</script>