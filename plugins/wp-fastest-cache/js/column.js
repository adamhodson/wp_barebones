if(window.attachEvent) {
    window.attachEvent('onload', wpfc_column_button_action);
} else {
    if(window.onload) {
        var curronload_1 = window.onload;
        var newonload_1 = function(evt) {
            curronload_1(evt);
            wpfc_column_button_action(evt);
        };
        window.onload = newonload_1;
    } else {
        window.onload = wpfc_column_button_action;
    }
}
function wpfc_column_button_action(){
    jQuery("#wpfc_column_clear_cache").css('width', '50px');
    jQuery("#wpfc_column_clear_cache").css('text-align', 'center');

	jQuery(document).ready(function(){
        jQuery("button.button.wpfc-clear-column-action:visible").click(function(e){

            jQuery(e.currentTarget).attr("disabled", true);

            jQuery.ajax({
                type: 'GET',
                url: ajaxurl,
                data : {"action": "wpfc_clear_cache_column", "id" : jQuery(e.currentTarget).attr("wpfc-clear-column")},
                dataType : "json",
                cache: false, 
                success: function(data){
                    if(typeof data.success != "undefined" && data.success == true){
                        jQuery(e.currentTarget).attr("disabled", false);
                    }else{
                        alert("Clear Cache Error");
                    }
                }
            });

            return false;
        });
	});
}