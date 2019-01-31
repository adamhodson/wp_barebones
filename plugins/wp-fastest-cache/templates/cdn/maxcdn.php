<div id="wpfc-modal-maxcdn" style="top: 10.5px; left: 226px; position: absolute; padding: 6px; height: auto; width: 560px; z-index: 10001;">
	<div style="height: 100%; width: 100%; background: none repeat scroll 0% 0% rgb(0, 0, 0); position: absolute; top: 0px; left: 0px; z-index: -1; opacity: 0.5; border-radius: 8px;">
	</div>
	<div style="z-index: 600; border-radius: 3px;">
		<div style="font-family:Verdana,Geneva,Arial,Helvetica,sans-serif;font-size:12px;background: none repeat scroll 0px 0px rgb(255, 161, 0); z-index: 1000; position: relative; padding: 2px; border-bottom: 1px solid rgb(194, 122, 0); height: 35px; border-radius: 3px 3px 0px 0px;">
			<table width="100%" height="100%">
				<tbody>
					<tr>
						<td valign="middle" style="vertical-align: middle; font-weight: bold; color: rgb(255, 255, 255); text-shadow: 0px 1px 1px rgba(0, 0, 0, 0.5); padding-left: 10px; font-size: 13px; cursor: move;">StackPath Settings</td>
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


				<div id="wpfc-wizard-maxcdn" class="wpfc-cdn-pages-container">
					<div wpfc-cdn-page="1" class="wiz-cont">
						<h1>Let's Get Started</h1>		
						<p>Hi! If you don't have a <strong>StackPath</strong> account, you can create one. If you already have, please continue...</p>
						<div class="wiz-input-cont" style="text-align:center;">
							<a href="//tracking.stackpath.com/c/149801/470088/8285" target="_blank">
								<button class="wpfc-green-button">Create a StackPath Account</button>
							</a>
					    </div>
					    <p class="wpfc-bottom-note" style="margin-bottom:-10px;"><a target="_blank" href="https://www.maxcdn.com/one/tutorial/implementing-cdn-on-wordpress-with-wp-fastest-cache/">Note: Please read How to Integrate StackPath into WP Fastest Cache</a></p>
					</div>
					<div wpfc-cdn-page="2" class="wiz-cont" style="display:none">
						<h1>Enter CDN Url</h1>		
						<p>Please enter your <strong>StackPath CDN Url</strong> below to deliver your contents via StackPath.</p>
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
						<p>Your static contents will be delivered via MaxCDN.</p>
					</div>


					<img style="border-radius:100%;" class="wiz-bg-img" src=" data:image/png;base64,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"/>
				</div>
			</div>
		</div>
		<?php include WPFC_MAIN_PATH."templates/buttons.html"; ?>
	</div>
</div>



