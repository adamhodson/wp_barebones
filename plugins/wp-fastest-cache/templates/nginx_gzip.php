<div template-id="wpfc-modal-nginxgzip" style="top: 10.5px; left: 226px; position: absolute; padding: 6px; height: auto; width: 360px; z-index: 10001;display:none;">
	<div style="height: 100%; width: 100%; background: none repeat scroll 0% 0% rgb(0, 0, 0); position: absolute; top: 0px; left: 0px; z-index: -1; opacity: 0.5; border-radius: 8px;">
	</div>
	<div style="z-index: 600; border-radius: 3px;">
		<div style="font-family:Verdana,Geneva,Arial,Helvetica,sans-serif;font-size:12px;background: none repeat scroll 0px 0px rgb(255, 161, 0); z-index: 1000; position: relative; padding: 2px; border-bottom: 1px solid rgb(194, 122, 0); height: 35px; border-radius: 3px 3px 0px 0px;">
			<table width="100%" height="100%">
				<tbody>
					<tr>
						<td valign="middle" style="vertical-align: middle; font-weight: bold; color: rgb(255, 255, 255); text-shadow: 0px 1px 1px rgba(0, 0, 0, 0.5); padding-left: 10px; font-size: 13px; cursor: move;">Enable Gzip</td>
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
				<div class="wpfc-cdn-pages-container">
					<div class="wiz-cont" style="">
						<h1>How to Enable Gzip?</h1>		
						<p>Nginx is used in the server so you need to enable the Gzip manually. Please take a look at the following tutorial.</p>
						
						<div class="wiz-input-cont" style="margin-top:10px !important;">
							<label class="mc-input-label" style="margin-right: 5px;">
								<a href="http://www.wpfastestcache.com/tutorial/how-to-enable-gzip-on-nginx/" target="_blank">http://www.wpfastestcache.com/tutorial/how-to-enable-gzip-on-nginx/</a>
							</label>
						</div>
					</div>
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
	jQuery("#wpFastestCacheGzip").click(function(){
		if(jQuery("#wpFastestCacheGzip").is(':checked')){
			if(jQuery("div[id^='wpfc-modal-nginxgzip-']").length === 0){
				Wpfc_New_Dialog.dialog("wpfc-modal-nginxgzip", {close: function(){
					Wpfc_New_Dialog.clone.remove();
				}});

				Wpfc_New_Dialog.show_button("close");
			}
		}
	});
</script>