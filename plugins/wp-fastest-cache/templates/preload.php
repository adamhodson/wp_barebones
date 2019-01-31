<style type="text/css">
	div[id^="wpfc-modal-preload"] .wiz-input-cont{
		margin-top: 0 !important;
		margin-bottom: 10px !important;
		float: left !important;
		width: 108px !important;
	}
	.wiz-input-cont label{
		margin-right: 0 !important;
	}
</style>
<div template-id="wpfc-modal-preload" style="display:none;top: 10.5px; left: 226px; position: absolute; padding: 6px; height: auto; width: 300px; z-index: 10001;">
	<div style="height: 100%; width: 100%; background: none repeat scroll 0% 0% rgb(0, 0, 0); position: absolute; top: 0px; left: 0px; z-index: -1; opacity: 0.5; border-radius: 8px;">
	</div>
	<div style="z-index: 600; border-radius: 3px;">
		<div style="font-family:Verdana,Geneva,Arial,Helvetica,sans-serif;font-size:12px;background: none repeat scroll 0px 0px rgb(255, 161, 0); z-index: 1000; position: relative; padding: 2px; border-bottom: 1px solid rgb(194, 122, 0); height: 35px; border-radius: 3px 3px 0px 0px;">
			<table width="100%" height="100%">
				<tbody>
					<tr>
						<td valign="middle" style="vertical-align: middle; font-weight: bold; color: rgb(255, 255, 255); text-shadow: 0px 1px 1px rgba(0, 0, 0, 0.5); padding-left: 10px; font-size: 13px; cursor: move;">Preload</td>
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
				<div class="wiz-input-cont" style="padding-right: 8px;margin-right: 10px;">
					<label class="mc-input-label" style="margin-right: 5px;"><input type="checkbox" <?php echo $wpFastestCachePreload_homepage; ?> id="wpFastestCachePreload_homepage" name="wpFastestCachePreload_homepage"></label>
					<label for="wpFastestCachePreload_homepage">Homepage</label>
				</div>
				<div class="wiz-input-cont">
					<label class="mc-input-label" style="margin-right: 5px;"><input type="checkbox" <?php echo $wpFastestCachePreload_post; ?> id="wpFastestCachePreload_post" name="wpFastestCachePreload_post"></label>
					<label for="wpFastestCachePreload_post">Posts</label>
				</div>
				<div class="wiz-input-cont" style="padding-right: 8px;margin-right: 10px;">
					<label class="mc-input-label" style="margin-right: 5px;"><input type="checkbox" <?php echo $wpFastestCachePreload_category; ?> id="wpFastestCachePreload_category" name="wpFastestCachePreload_category"></label>
					<label for="wpFastestCachePreload_category">Categories</label>
				</div>
				<div class="wiz-input-cont">
					<label class="mc-input-label" style="margin-right: 5px;"><input type="checkbox" <?php echo $wpFastestCachePreload_page; ?> id="wpFastestCachePreload_page" name="wpFastestCachePreload_page"></label>
					<label for="wpFastestCachePreload_page">Pages</label>
				</div>
				<div class="wiz-input-cont" style="padding-right: 8px;margin-right: 10px;">
					<label class="mc-input-label" style="margin-right: 5px;"><input type="checkbox" <?php echo $wpFastestCachePreload_tag; ?> id="wpFastestCachePreload_tag" name="wpFastestCachePreload_tag"></label>
					<label for="wpFastestCachePreload_tag">Tags</label>
				</div>
				<div class="wiz-input-cont">
					<label class="mc-input-label" style="margin-right: 5px;"><input type="checkbox" <?php echo $wpFastestCachePreload_attachment; ?> id="wpFastestCachePreload_attachment" name="wpFastestCachePreload_attachment"></label>
					<label for="wpFastestCachePreload_attachment">Attachments</label>
				</div>

				<div class="wiz-input-cont" style="width: 91% !important;margin-bottom: 10px !important;">
					<label class="mc-input-label" style="margin-right: 5px;"><input type="checkbox" <?php echo $wpFastestCachePreload_customposttypes; ?> id="wpFastestCachePreload_customposttypes" name="wpFastestCachePreload_customposttypes"></label>
					<label for="wpFastestCachePreload_customposttypes">Custom Post Types</label>
				</div>

				<div class="wiz-input-cont" style="width: 91% !important;margin-bottom: 10px !important;">
					<label class="mc-input-label" style="float:left;">
						<table id="wpfc-form-spinner-preload" class="wpfc-form-spinner" cellpadding="0" cellspacing="0" border="0" height="20" width="70" style="border: 1px solid rgb(204, 204, 204); border-collapse: collapse; background: rgb(255, 255, 255);">
							<tbody>
								<tr>
									<td class="wpfc-form-spinner-input-td" rowspan="2" style="padding-right: 2px;height: 100%;">
										<div class="wpfc-form-spinner-number" style="height: 100%; width: 100%; border: none; padding: 0px; font-size: 14px; text-align: center; outline: none;padding-top:7px;"><?php echo $wpFastestCachePreload_number; ?></div>
										<input type="hidden" class="wpfc-form-spinner-input" name="wpFastestCachePreload_number" value="<?php echo $wpFastestCachePreload_number; ?>" />
									</td>
									<td class="wpfc-form-spinner-up" style="height: 15px; cursor: default; text-align: center; width: 12px; font-size: 9px; padding-left: 4px; padding-right: 8px; border: 1px solid rgb(204, 204, 204); background: rgb(245, 245, 245);">
										<img src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAcAAAAFCAQAAAAjkz5TAAAACXBIWXMAAAsTAAALEwEAmpwYAAAABGdBTUEAANjr9RwUqgAAACBjSFJNAABtmAAAc44AAPfgAACDwQAAbsQAAOKFAAAxZAAAGGNUHM53AAAAVklEQVR42gTBIQqAMACG0f8YSwPvZRIMxtVlESwWk8EyUJSFIR7Eg1h1xs/3hNBp4h0sQkLJrF9kzL1FiiY8GwcTPrtC87uQ2Al01JcGWjyOhooy/wMANWktnmvt+MQAAAAASUVORK5CYII=" align="right">
									</td>
								</tr>
								<tr>
									<td class="wpfc-form-spinner-down" style="height: 15px; cursor: default; text-align: center; width: 12px; font-size: 9px; padding-left: 4px; padding-right: 8px; border: 1px solid rgb(204, 204, 204); background: rgb(245, 245, 245);">
										<img src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAcAAAAFCAQAAAAjkz5TAAAACXBIWXMAAAsTAAALEwEAmpwYAAAABGdBTUEAANjr9RwUqgAAACBjSFJNAABtmAAAc44AAPfgAACDwQAAbsQAAOKFAAAxZAAAGGNUHM53AAAAWElEQVR42gBLALT/AMD/uv+z/6v/ov+Y/472ALf1uv+//7n/s/+c/3/PAKsYovSo/7H/mf969nEhAP8AkxiJ95T/dvZtHv8AAP8A/wB6GHHraR7/AP8AAwCoUy51Bie9nwAAAABJRU5ErkJggg==" align="right">
									</td>
								</tr>
							</tbody>
						</table>
					</label>
					<label style="float:left;margin-left:8px;padding-top:4px;">pages per minute</label>
				</div>

				<div class="wiz-input-cont" style="width: 91% !important;margin-bottom: 0px !important;">
					<label class="mc-input-label" style="margin-right: 5px;"><input type="checkbox" <?php echo $wpFastestCachePreload_restart; ?> id="wpFastestCachePreload_restart" name="wpFastestCachePreload_restart"></label>
					<label for="wpFastestCachePreload_restart">Restart After Completed</label>
					<a style="margin-left:5px;" target="_blank" href="http://www.wpfastestcache.com/features/restart-preload-after-completed/"><img src="<?php echo plugins_url("wp-fastest-cache/images/info.png"); ?>"></a>
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