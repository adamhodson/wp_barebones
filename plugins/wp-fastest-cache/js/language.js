window.wpfc = {};
window.wpfc.translate = function(word){
	return (typeof window.wpfc.dictionary != "undefined" && typeof window.wpfc.dictionary[word] != "undefined" && window.wpfc.dictionary[word]) ? window.wpfc.dictionary[word] : word;
};
jQuery.fn.extend({
    wpfclang: function(){
    	var dictionary = window.wpfc.dictionary || {};
		var el = jQuery(this);
    	var text = el.attr("type") == "submit" ? el.val().trim() : el.text().trim();
    	var converted = typeof dictionary[text] == "undefined" ? text : dictionary[text];

    	if(typeof converted != "undefined" && converted){
	    	if(el.attr("type") == "submit"){
	    		el.val(converted);
	    	}else{
	    		el.html(converted);
	    	}
    	}
    }
});
var Wpfclang = {
	language : "",
	init: function(language){
		this.language = language;
		this.translate();
	},
	translate: function(){
		if(typeof window.wpfc != "undefined" && typeof window.wpfc.dictionary != "undefined"){
			var self = this;
			jQuery('#wpfc-read-tutorial span, #wpfc-plugin-setup-warning h3, #just-h1, #get-now-h1, #new-features-h1, div.wpfc-premium-step-footer ul li a, div.wpfc-premium-step-footer p, div.wpfc-premium-step-content, #wpbody-content label, div.question, .questionCon input[type="submit"], #message p, .wrap h2, #nextVerAct, select option, th, #rule-help-tip h4, #rule-help-tip label, .omni_admin_sidebar h3, #message p, #wpfc-image-static-panel span, #wpfc-statics-right div, #wpfc-image-static-panel p, #container-show-hide-image-list span, #wpfc-image-list th').each(function(){
				if(jQuery(this).children().length === 0){
					jQuery(this).wpfclang();
				}
			});
		}
	}
};