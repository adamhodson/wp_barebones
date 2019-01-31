$.fn.change_select = function(data) {
    //this.css( "color", "green" );
	this.val(data)
};

$.fn.reset_select = function(data) {
    //this.css( "color", "green" );
	this.val(' ')
};

jQuery(function($){

	//THEME SETUP SECTION 
	//Includes Select Options		
	//Reset the selected value of a select option
	function reset_sel_val(el){
		$(el).val(' ')
	}

	//Select an option on select element
	function set_sel_val(el, val){	
		$(el).val(val)
	}

	function close_all(){
		$('.ui-formselect.active').removeClass('opened')
		$('.ui-formselect.active').find('.links a').removeClass('on')
		$('.ui-formselect .select-trigger').removeClass('active')
	}

	function close_drop(open, target){
		$(open).removeClass('opened')
		$(target).removeClass('active')
		$(open).find('.links a').removeClass('on')
	}

	function open_drop(cur, target){

		close_all();
		
		$(cur).addClass('opened')
		$(target).addClass('active')
		
		$(cur).find('.links a').each(function(){
			$(this).addClass('on')
		})				
		sel_opened = true;				
	}
		

	//create select dropdown
	function proper_dropdown(el){

		var classes = '';
		var  select_id = $(el).attr('id');

		if($(el).hasClass('browser-default')){

		}
		else{			
			
			if($(el).hasClass('border')){
				classes += ' border ';				
			}
			if($(el).hasClass('shadow')){
				classes += ' shadow ';
			}
			
			var new_div = $('<div class="ui-formselect active proper_select '+ classes +'" data-sel-el="'+ select_id +'"><a href="#" onclick="return false" class="caret"></a><div class="links"></div></div>').insertBefore($(el));

			if($(el).hasClass('disabled')){
				$(new_div).addClass('disabled')
			}

			$(el).hide();			
				
			var cur_select = $('.ui-formselect').before($(el));
			var cur_links = $(cur_select).find('.links');				
			var i = 0;

			$(el).find('option').each(function(){
				var  option_title = $(this).text();
				var  option_val = $(this).val();			

				if(i == 0){
					var item = "<a href='#' class='select-trigger' onclick='return false' data-val="+ option_val +">"+ option_title +"</a>";
				}
				else{					
					var item = "<a href='#' class='sel-val-option' data-val="+ option_val +">"+ option_title +"</a>";	
				}			

				$(new_div).find('.links').append(item)	
									
				i++;
			})			
		}
	}

	//Setup Select Options Tagegd UI Formselect
	$('.ui-formselect').each(function(){
		proper_dropdown($(this))
	})

	//Open & Close Selects	
	var sel_opened = false;
	var cur_sel_opened; 
	var cur_select_el;
	$('body').on('click', function(e){

		if($(e.target).hasClass('select-trigger active')){
			reset_sel_val(cur_select_el)
			close_drop(cur_sel_opened, e.target)
		}
		else if($(e.target).hasClass('sel-val-option')){
			
			var cur_index = $(e.target).index()
			var cur_val = $(e.target).attr('data-val')			

			set_sel_val(cur_select_el, cur_val)				
			$(e.target).parent().find('.select-trigger').removeClass('select-trigger').addClass('sel-val-option')
			$(e.target).addClass('select-trigger').removeClass('sel-val-option')


			close_drop(cur_sel_opened, e.target)

		}
		else{
			if($(e.target).hasClass('select-trigger')){
				cur_sel_opened = $(e.target).parent().parent();			
				cur_select_el = $('select#' + $(cur_sel_opened).attr('data-sel-el'))				
				
				open_drop(cur_sel_opened, e.target)
				
			}

			else if($(e.target).hasClass('caret')){

				cur_sel_opened = $(e.target).parent();			
				cur_select_el = $('select#' + $(cur_sel_opened).attr('data-sel-el'))				
				
				// $(cur_sel_opened).addClass('opened')
				// $(e.target).addClass('active')
				
				// $(cur_sel_opened).find('.links a').each(function(){
				// 	$(this).addClass('on')
				// })				
				// sel_opened = true;	

				open_drop(cur_sel_opened, e.target)

			}
			//&& !$(e.target).hasClass('caret)')
			else if(!$(e.target).hasClass('caret') && !$(e.target).hasClass('select-trigger') && !$(e.target).hasClass('ui-formselect active)') && !$(e.target).hasClass('sel-val-option)') ){
					
				$(cur_sel_opened).removeClass('opened')
			}
		}		
		
	})
	//END THEME SETUP SECTION 


	//Background Image Blocks - Get BG Image and Add To Background
	$('.bg-block').each(function(){
		var bg = $(this).attr('data-bg-img')
		$(this).css('background-image', 'url("'+ bg +'")')
	})

	$('.or-sep').each(function(){
		var h = $(this).parent().height();
		$(this).height(h);
	})
})