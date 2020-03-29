jQuery(function($){
	
		//bootstrap4 tooltip
		$('[data-toggle="tooltip"]').tooltip()
	
		//clear query
		$('form#search').cleanQuery();
	
		// form input for many to many relation
		$('form .manytomany').each(function(){
	
			var myRoot = $(this);
			var myModal = $(this).find('.manytomany-modal');
	
			// set chekcbox click event
			$(this).find('.manytomany-trigger').on('click', function(e){
	
				var opener_name = $(this).attr('name').slice(0,-4);	// ex) pivots[beautiful_tag][1]
	
				if( $(this).prop("checked") ){	// case - checkbox off -> on
	
					// remove disabled attr
					myRoot.find('input[type="hidden"][parent_name="' + opener_name + '"][disabled]').removeAttr('disabled');
	
					myModal.attr( 'opener-name', opener_name );	
					myModal.modal('show');
	
					//load data
					myModal.find('.manytomany-pivot-input').each( function(){
	
						var opener_name = myModal.attr('opener-name');
						var pivot_input_name = $(this).attr('name').replace('pivots-option[','').replace(']','')
						var hidden_name = opener_name + '[' + pivot_input_name + ']';
						var myHidden = myRoot.find('input[name="' + hidden_name + '"]');
	
						if( myHidden.length ){
							$(this).val( myHidden.attr('value') );
						}
						
						// errors
						var error_name = 'errors.' + opener_name.replace(/\[/g,'.').replace(/\]/g,'') + '.' + pivot_input_name;
						var error = myRoot.find('input[name="' + error_name + '"]');
						if(error.length){
							$(this).addClass("is-invalid");
							$(this).siblings('.invalid-feedback').html(error.val());
						}else{
							$(this)[0].checkValidity();
							$(this).removeClass("is-invalid");
							$(this).siblings('.invalid-feedback').html('Invalid!');
						}
					});
	
				}else{	// case - checkbox is on -> off
	
					// add disabled attr
					myRoot.find('input[type="hidden"][parent_name="' + opener_name + '"]').attr('disabled','disabled');
				}
			});
	
			// set save button click event
			myModal.find('button.save').on('click', function(e){
	
				var opener_name = myModal.attr('opener-name');
	
				myModal.find('.manytomany-pivot-input').each( function(){
	
					var pivot_input_name = $(this).attr('name').replace('pivots-option[','').replace(']','')
					var hidden_name = opener_name + '[' + pivot_input_name + ']';
	
					// manual validate
					if( $(this)[0].checkValidity() === false ){	// has error
						$(this).addClass('has-error');
					}else{										// has no error
						if( $(this).hasClass('has-error') ){
							$(this).removeClass('has-error');
						}
	
						//exist-check
						var target = myRoot.children('input[name="' + hidden_name + '"]');
		
						if( target.length ){
		
							//update
							target.val( $(this).val() );
							
						}else{
							//add
							myRoot.append('<input type="hidden" name="' + hidden_name + '" value="' + $(this).val() + '" parent_name="' + opener_name + '">');
						}
					}
				});
				
				if( myModal.find('.has-error').length ){
					if( myModal.hasClass('needs-validation-with-save') ){
						myModal.addClass('was-validated');
					}
					return false;
				}else{
					if( myModal.hasClass('was-validated') ){
						myModal.removeClass('was-validated');
					}
					myRoot.find('[name="' + opener_name + '[id]"]').addClass('save-is-pushed');
					myModal.modal('hide');
				}
	
	
			});
	
			// set modal close event
			myModal.on('hide.bs.modal', function(e){
	
				var opener_name = myModal.attr('opener-name');
	
				//clear input
				myModal.find('.manytomany-pivot-input').val('');
				
				//check off input in case of cancel
				var opener_checkbox = myRoot.find('[name="' + opener_name + '[id]"]');
				if( opener_checkbox.hasClass('save-is-pushed') ){
					opener_checkbox.removeClass('save-is-pushed');
				}else{
					myHidden = myRoot.find('input[type="hidden"][parent_name="' + opener_name + '"]');
	
					if( !myHidden.length ){	// in case of create
	
						// turn checkbox off
						opener_checkbox.prop("checked", false);
						
						// add disabled attr
						myHidden.attr('disabled','disabled');
					}
	
				}
			});
		});
	
		// delete needless input for many to many relationship
		$('form').submit(function(){
	
			if( $(this).find('.manytomany').length ){
	
				// disable input,select,textarea in modal
				$(this).find('.manytomany-modal input, .manytomany-modal select, .manytomany-modal textarea').attr('disabled','disabled');
				
				if( !$(this)[0].checkValidity() ){ // in case of validation false
	
					// remove disable input,select,textarea in modal
					$(this).find('.manytomany-modal input, .manytomany-modal select, .manytomany-modal textarea').removeAttr('disabled');
					
				}
			}
		});
	});
		
	// sort by column
	function sortByColumn(column){
		
		var search = decodeURI( window.location.search );
		var search_array = search.substring(1).split('&');
		
		var search_pair_array = [];
	
		for(var i=0;search_array[i];i++) {
			var kv = search_array[i].split('=');
			search_pair_array[kv[0]] = kv[1];
		}
	
		if( "q[s]" in search_pair_array && search_pair_array["q[s]"] == column + '_desc' ){
			search_pair_array["q[s]"] = column + '_asc';
		}else{
			search_pair_array["q[s]"] = column + '_desc';
		}
	
		var query_txt = '?';
	
		for( var key in search_pair_array ){
			query_txt += key + '=' + search_pair_array[key] + '&';
		}
		query_txt = encodeURI( query_txt.slice( 0, -1 ) );
	
		window.location.href = window.location.pathname + query_txt;
	}
	
	// bootstrap4 form validation
	(function() {
	  'use strict';
	  window.addEventListener('load', function() {
		// Fetch all the forms we want to apply custom Bootstrap validation styles to
		var forms = document.getElementsByClassName('needs-validation');
		// Loop over them and prevent submission
		var validation = Array.prototype.filter.call(forms, function(form) {
		  form.addEventListener('submit', function(event) {
			if (form.checkValidity() === false) {
			  event.preventDefault();
			  event.stopPropagation();
			}
			form.classList.add('was-validated');
		  }, false);
		});
	  }, false);
	})();