<div id="wpfc-modal-photon" style="top: 10.5px; left: 226px; position: absolute; padding: 6px; height: auto; width: 560px; z-index: 10001;">
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


				<div id="wpfc-wizard-photon" class="wpfc-cdn-pages-container">
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
							<label class="mc-input-label" for="cdn-url" style="padding-right: 12px;">CDN Url:</label><select id="cdn-url">
																											    	 	<option value="http://i0.wp.com/<?php echo str_replace("www.", "", $_SERVER["HTTP_HOST"]); ?>">i0.wp.com</option>
																											    	 	<option value="http://i1.wp.com/<?php echo str_replace("www.", "", $_SERVER["HTTP_HOST"]); ?>">i1.wp.com</option>
																											    	 	<option value="http://i2.wp.com/<?php echo str_replace("www.", "", $_SERVER["HTTP_HOST"]); ?>">i2.wp.com</option>
																											    	 	<option value="http://i3.wp.com/<?php echo str_replace("www.", "", $_SERVER["HTTP_HOST"]); ?>">i3.wp.com</option>
																											    	 	<option value="random">Random</option>
																											    	 </select> 
					    	<div id="cdn-url-loading"></div>
					    	<label class="wiz-error-msg"></label>
					    </div>
					    <div class="wiz-input-cont" style="display:none;">
							<label class="mc-input-label" for="origin-url">Origin Url:</label><input type="text" name="" value="<?php echo str_replace("www.", "", $_SERVER["HTTP_HOST"]); ?>" class="api-key" id="origin-url">
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



