<div id="wpfc-modal-other" style="top: 10.5px; left: 226px; position: absolute; padding: 6px; height: auto; width: 560px; z-index: 10001;">
	<div style="height: 100%; width: 100%; background: none repeat scroll 0% 0% rgb(0, 0, 0); position: absolute; top: 0px; left: 0px; z-index: -1; opacity: 0.5; border-radius: 8px;">
	</div>
	<div style="z-index: 600; border-radius: 3px;">
		<div style="font-family:Verdana,Geneva,Arial,Helvetica,sans-serif;font-size:12px;background: none repeat scroll 0px 0px rgb(255, 161, 0); z-index: 1000; position: relative; padding: 2px; border-bottom: 1px solid rgb(194, 122, 0); height: 35px; border-radius: 3px 3px 0px 0px;">
			<table width="100%" height="100%">
				<tbody>
					<tr>
						<td valign="middle" style="vertical-align: middle; font-weight: bold; color: rgb(255, 255, 255); text-shadow: 0px 1px 1px rgba(0, 0, 0, 0.5); padding-left: 10px; font-size: 13px; cursor: move;">CDN Settings</td>
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


				<div id="wpfc-wizard-other" class="wpfc-cdn-pages-container">
					<div wpfc-cdn-page="1" class="wiz-cont">
						<h1>Let's Get Started</h1>		
						<p>Hi! If you don't have a <strong>MaxCDN</strong> account, you can create one. If you already have, please continue...</p>
						<div class="wiz-input-cont" style="text-align:center;">
							<a href="http://tracking.maxcdn.com/c/149801/3982/378" target="_blank">
								<button class="wpfc-green-button">Create a MaxCDN Account</button>
							</a>
					    </div>
					    <p class="wpfc-bottom-note" style="margin-bottom:-10px;"><a target="_blank" href="https://www.maxcdn.com/one/tutorial/implementing-cdn-on-wordpress-with-wp-fastest-cache/">Note: Please read How to Integrate MaxCDN into WP Fastest Cache</a></p>
					</div>
					<div wpfc-cdn-page="2" class="wiz-cont" style="display:none">
						<h1>Enter CDN Url</h1>		
						<p>Please enter your <strong>CDN Url</strong> below to deliver your contents via CDN.</p>
						<div class="wiz-input-cont">
							<label class="mc-input-label" for="cdn-url" style="padding-right: 12px;">CDN Url:</label><input type="text" name="" value="" class="api-key" id="cdn-url">
					    	<div id="cdn-url-loading"></div>
					    	<label class="wiz-error-msg"></label>
					    </div>
					    <div class="wiz-input-cont">
							<label class="mc-input-label" for="origin-url">Origin Url:</label><input type="text" name="" value="" class="api-key" id="origin-url">
					    </div>
					</div>
					<div wpfc-cdn-page="3" class="wiz-cont" style="display:none">
						<h1>File Types</h1>		
						<p>Specify the file types within the to host with the CDN.</p>
						
						<?php include WPFC_MAIN_PATH."templates/cdn/file_types.php"; ?>
					</div>
					<div wpfc-cdn-page="4" class="wiz-cont" style="display:none">
						<?php include WPFC_MAIN_PATH."templates/cdn/specify_sources.php"; ?>
					</div>
					<div wpfc-cdn-page="5" class="wiz-cont" style="display:none">
						<h1>Ready to Go!</h1>
						<p>You're all set! Click the finish button below and that's it.</p>
					</div>
					<div wpfc-cdn-page="6" class="wiz-cont" style="display:none">
						<h1>Integration Ready!</h1>
						<p>Your static contents will be delivered via CDN.</p>
					</div>


					<img class="wiz-bg-img" src="data:image/png;base64,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"/>
				</div>
			</div>
		</div>
		<?php include WPFC_MAIN_PATH."templates/buttons.html"; ?>
	</div>
</div>



