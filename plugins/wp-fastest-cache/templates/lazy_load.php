<div template-id="wpfc-modal-lazyload" style="top: 10.5px; left: 226px; position: absolute; padding: 6px; height: auto; width: 560px; z-index: 10001;display:none;">
	<div style="height: 100%; width: 100%; background: none repeat scroll 0% 0% rgb(0, 0, 0); position: absolute; top: 0px; left: 0px; z-index: -1; opacity: 0.5; border-radius: 8px;">
	</div>
	<div style="z-index: 600; border-radius: 3px;">
		<div style="font-family:Verdana,Geneva,Arial,Helvetica,sans-serif;font-size:12px;background: none repeat scroll 0px 0px rgb(255, 161, 0); z-index: 1000; position: relative; padding: 2px; border-bottom: 1px solid rgb(194, 122, 0); height: 35px; border-radius: 3px 3px 0px 0px;">
			<table width="100%" height="100%">
				<tbody>
					<tr>
						<td valign="middle" style="vertical-align: middle; font-weight: bold; color: rgb(255, 255, 255); text-shadow: 0px 1px 1px rgba(0, 0, 0, 0.5); padding-left: 10px; font-size: 13px; cursor: move;">Lazy Load Settings</td>
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

				<?php
					$wpFastestCacheLazyLoad_type_exceptcontent = "";
					$wpFastestCacheLazyLoad_type_all = "";

					if(isset($this->options->wpFastestCacheLazyLoad_type)){
						if($this->options->wpFastestCacheLazyLoad_type == "exceptcontent"){
							$wpFastestCacheLazyLoad_type_exceptcontent = 'checked="checked"';
						}else if($this->options->wpFastestCacheLazyLoad_type == "all"){
							$wpFastestCacheLazyLoad_type_all = 'checked="checked"';
						}
					}else{
						$wpFastestCacheLazyLoad_type_exceptcontent = 'checked="checked"';
					}
				?>
				<div class="wpfc-cdn-pages-container">
					<div wpfc-cdn-page="1" class="wiz-cont" style="">
						<h1>Choose Images</h1>		
						<p>Please choose which images are loaded via Lazy Load.</p>
						

						<div class="wiz-input-cont">
							<label class="mc-input-label" style="margin-right: 5px;"><input type="radio" <?php echo $wpFastestCacheLazyLoad_type_all; ?> action-id="wpFastestCacheLazyLoad_type_all" id="wpFastestCacheLazyLoad_type_all" name="wpFastestCacheLazyLoad_type" value="all"></label>
							<label for="wpFastestCacheLazyLoad_type_all">All Images</label>
						</div>

						<div class="wiz-input-cont" style="margin-top:10px !important;">
							<label class="mc-input-label" style="margin-right: 5px;"><input type="radio" <?php echo $wpFastestCacheLazyLoad_type_exceptcontent; ?> action-id="wpFastestCacheLazyLoad_type_exceptcontent" id="wpFastestCacheLazyLoad_type_exceptcontent" name="wpFastestCacheLazyLoad_type" value="exceptcontent"></label>
							<label for="wpFastestCacheLazyLoad_type_exceptcontent">All Images except images in content</label>
						</div>



					</div>
				</div>





			</div>
		</div>
		<?php include WPFC_MAIN_PATH."templates/buttons.html"; ?>
	</div>
</div>
<script type="text/javascript">
	jQuery("#wpFastestCacheLazyLoad").click(function(){
		if(typeof jQuery(this).attr("checked") != "undefined"){
			if(jQuery("div[id^='wpfc-modal-lazyload-']").length === 0){
				Wpfc_New_Dialog.dialog("wpfc-modal-lazyload", {close: function(){
					Wpfc_New_Dialog.clone.find("div.window-content input").each(function(){
						if(jQuery(this).attr("checked")){
							var id = jQuery(this).attr("action-id");
							jQuery("div.tab1 div[template-id='wpfc-modal-lazyload'] div.window-content input#" + id).attr("checked", true);
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


