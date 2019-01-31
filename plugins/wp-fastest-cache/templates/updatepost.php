<style type="text/css">
	div[id^="wpfc-modal-updatepost"] .wiz-input-cont{
		margin-top: 0 !important;
		margin-bottom: 5px !important;
	}
	.wiz-input-cont label{
		margin-right: 0 !important;
	}
</style>
<div template-id="wpfc-modal-updatepost" style="display:none;top: 10.5px; left: 226px; position: absolute; padding: 6px; height: auto; width: 330px; z-index: 10001;">
	<div style="height: 100%; width: 100%; background: none repeat scroll 0% 0% rgb(0, 0, 0); position: absolute; top: 0px; left: 0px; z-index: -1; opacity: 0.5; border-radius: 8px;">
	</div>
	<div style="z-index: 600; border-radius: 3px;">
		<div style="font-family:Verdana,Geneva,Arial,Helvetica,sans-serif;font-size:12px;background: none repeat scroll 0px 0px rgb(255, 161, 0); z-index: 1000; position: relative; padding: 2px; border-bottom: 1px solid rgb(194, 122, 0); height: 35px; border-radius: 3px 3px 0px 0px;">
			<table width="100%" height="100%">
				<tbody>
					<tr>
						<td valign="middle" style="vertical-align: middle; font-weight: bold; color: rgb(255, 255, 255); text-shadow: 0px 1px 1px rgba(0, 0, 0, 0.5); padding-left: 10px; font-size: 13px; cursor: move;">Update Post</td>
						<td width="20" align="center" style="vertical-align: middle;"></td>
						<td width="20" align="center" style="vertical-align: middle; font-family: Arial,Helvetica,sans-serif; color: rgb(170, 170, 170); cursor: default;">
							<div title="Close Window" class="close-wiz"></div>
						</td>
					</tr>
				</tbody>
			</table>
		</div>
		<div class="window-content-wrapper" style="padding: 15px;">
			<div class="window-content" style="z-index: 1000; height: auto; position: relative; display: inline-block; width: 100%;">
				<p style="color:#666;margin-top:0 !important;">What do you want to happen after update a post or a page?</p>

				<?php
					$wpFastestCacheUpdatePost_type_post = "";
					$wpFastestCacheUpdatePost_type_all = "";

					if(isset($this->options->wpFastestCacheUpdatePost_type)){
						if($this->options->wpFastestCacheUpdatePost_type == "post"){
							$wpFastestCacheUpdatePost_type_post = 'checked="checked"';
						}else if($this->options->wpFastestCacheUpdatePost_type == "all"){
							$wpFastestCacheUpdatePost_type_all = 'checked="checked"';
						}
					}else{
						$wpFastestCacheUpdatePost_type_post = 'checked="checked"';
					}
				?>


				<div class="wiz-input-cont">
					<label class="mc-input-label" style="margin-right: 5px;"><input type="radio" <?php echo $wpFastestCacheUpdatePost_type_all; ?> action-id="wpFastestCacheUpdatePost_type_all" id="wpFastestCacheUpdatePost_type_all" name="wpFastestCacheUpdatePost_type" value="all"></label>
					<label for="wpFastestCacheUpdatePost_type_all">Clear All Cache</label>
				</div>
				<div class="wiz-input-cont">
					<label class="mc-input-label" style="margin-right: 5px;"><input type="radio" <?php echo $wpFastestCacheUpdatePost_type_post; ?> action-id="wpFastestCacheUpdatePost_type_post" id="wpFastestCacheUpdatePost_type_post" name="wpFastestCacheUpdatePost_type" value="post"></label>
					<label for="wpFastestCacheUpdatePost_type_post">Clear Cache of Post / Page</label><br>
					<label style="margin-left:24px;" for="wpFastestCacheUpdatePost_type_post">Clear Cache of Post Categories</label><br>
					<label style="margin-left:24px;" for="wpFastestCacheUpdatePost_type_post">Clear Cache of Post Tags</label><br>
					<label style="margin-left:24px;" for="wpFastestCacheUpdatePost_type_post">Clear Cache of Homepage</label>
				</div>

			</div>
		</div>
		<div class="window-buttons-wrapper" style="padding: 0px; display: inline-block; width: 100%; border-top: 1px solid rgb(255, 255, 255); background: none repeat scroll 0px 0px rgb(222, 222, 222); z-index: 999; position: relative; text-align: right; border-radius: 0px 0px 3px 3px;">
			<div style="padding: 12px; height: 23px;text-align: center;">
				<button class="wpfc-dialog-buttons buttons" type="button" action="close">
					<span>OK</span>
				</button>
			</div>
		</div>
	</div>
</div>
<script type="text/javascript">
	jQuery("#wpFastestCacheUpdatePost").click(function(){
		if(typeof jQuery(this).attr("checked") != "undefined"){
			if(jQuery("div[id^='wpfc-modal-updatepost-']").length === 0){
				Wpfc_New_Dialog.dialog("wpfc-modal-updatepost", {close: function(){
					Wpfc_New_Dialog.clone.find("div.window-content input").each(function(){
						if(jQuery(this).attr("checked")){
							var id = jQuery(this).attr("action-id");
							jQuery("div.tab1 div[template-id='wpfc-modal-updatepost'] div.window-content input#" + id).attr("checked", true);
						}

						//.attr("checked"
					});

					Wpfc_New_Dialog.clone.remove();
				}});

				Wpfc_New_Dialog.show_button("close");
			}
		}
	});
</script>