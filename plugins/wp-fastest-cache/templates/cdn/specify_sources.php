<h1>Specify Sources</h1>		
<p>If you want some of the sources instead of all the sources to be served via CDN, you can specify the sources. If a source url contains any keyword below, it is served via CDN.</p>
<div class="wiz-input-cont" style="padding:8px 18px;border-radius:0;text-align:center; background: #fff none repeat scroll 0 0;">
	
	<style type="text/css">
		.wiz-input-cont{
			box-shadow:0 2px 6px 0 rgba(0, 0, 0, 0.15) !important;
			
		}
		
		.wpfc-textbox-con{position:absolute;left:0;top:0;-webkit-border-radius:3px;-moz-border-radius:3px;background:#fff;-webkit-box-shadow:0 2px 6px 2px rgba(0,0,0,0.3);box-shadow:0 2px 6px 2px rgba(0,0,0,0.3);-moz-box-shadow:0 2px 6px 2px rgba(0,0,0,0.3);float:left;z-index:444;width:150px;border:1px solid #adadad;}
		.keyword-item-list:after{box-shodow:0 2px 6px 0 rgba(0, 0, 0, 0.15);content:'';clear:both;height:0;visibility:hidden;display:block}
		.keyword-item{width:auto;float:left;line-height:22px;position:relative;background:rgba(0,0,0,0.15);margin:0 5px 0 0;-webkit-border-radius:3px;-moz-border-radius:3px;border-radius:3px}
		.fixed-search input{width:100%;padding:6px 9px;line-height:20px;-moz-box-sizing:border-box;-webkit-box-sizing:border-box;box-sizing:border-box;margin:0;border:none;border-bottom:1px solid #ccc;-webkit-box-shadow:0 2px 6px 0 rgba(0,0,0,0.1);box-shadow:0 2px 6px 0 rgba(0,0,0,0.1);-moz-box-shadow:0 2px 6px 0 rgba(0,0,0,0.1);-webkit-border-radius:3px 3px 0 0;-moz-border-radius:3px 3px 0 0;border-radius:3px 3px 0 0;font-weight:bold}.fixed-search input:focus{outline:0}
		.keyword-item{width:auto;float:left;line-height:22px;position:relative;background:rgba(0,0,0,0.15);margin:0 5px 5px 0;-webkit-border-radius:3px;-moz-border-radius:3px;border-radius:3px;}
		.wpfc-add-new-keyword, .keyword-item a.keyword-label{
			background-color: #ffa100;
			color:#ffffff;
			text-decoration:none;
			padding:7px 15px;
			display:block;
			text-shadow:none;
			-webkit-transition:all .1s linear;
			-moz-transition:all .1s linear;
			-o-transition:all .1s linear;
			transition:all .1s linear;
			cursor: pointer;
		}
		.keyword-item a.keyword-label:hover{
			padding-left: 4px;
			padding-right: 26px;
		}
		.keyword-item a.keyword-label:hover:after{
			width:16px;
			height:16px;
			background-image: url('data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABAAAAAQCAQAAAC1+jfqAAAAAnNCSVQICFXsRgQAAAAJcEhZcwAAAIAAAACAAc9WmjcAAAAZdEVYdFNvZnR3YXJlAHd3dy5pbmtzY2FwZS5vcmeb7jwaAAAA4klEQVQokWXOMSuFcRQH4EcZZFB3w0BZfIC7mLl3MaGu2cAtqyLJwMQiRuObidFG4hNQJrlFJBlMFgzKMfi/vPe9/ZbT+T11jvAbq2nI8k3aB1MymStPTrTcyWTmi2BDdCQrgprtjjQKIJgw15aZth+Co9KB6zLYL4GLMthqq7/slcFKoVzTa1ClHTT/wKwxt0I4N/wPGqk+NuTNqQ/PLt3oyUEtgQWbQl3NqHVhOgfVBCYdCC3dhnwKSzkYSWDXslDVNG5RqOegksCDPg/ufXv34kxXAkF/SpcBh1492tEbwg+6YscxiN7TegAAAABJRU5ErkJggg==');
			content:"";
			position:absolute;
			top:10px;
			right:4px;
		}

		.wpfc-add-new-keyword{
			cursor:pointer;
			text-decoration:none;
			background-color:#fff !important;
			color:#ccc !important;
			padding:5px 12px !important;
			border:2px dashed #ccc;
			line-height:21px;
		}
		.wpfc-add-new-keyword:before{display:inline-block;content:"+";margin:-1px 4px 0 -6px;}
		.wpfc-add-new-keyword:hover{color:#589b43 !important;border-color:#589b43;}
		
	</style>
	
	<ul class="keyword-item-list">
        <li class="keyword-item">
            <a class="wpfc-add-new-keyword">Add Keyword</a>
            <div class="wpfc-textbox-con" style="display:none;">
                <div class="fixed-search"><input type="text" placeholder="Add Keyword"></div>
            </div>
        </li>
    </ul>
</div>