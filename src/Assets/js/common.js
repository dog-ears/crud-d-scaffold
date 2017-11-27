jQuery(function($){
	
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
							$(this).parent().addClass("has-error");
							$(this).siblings('.help-block').show().html(error.val());
						}else{
							$(this).parent().removeClass("has-error");
							$(this).siblings('.help-block').hide();
						}
					});
	
				}else{	// case - checkbox is on -> off
	
					// add disabled attr
					myRoot.find('input[type="hidden"][parent_name="' + opener_name + '"]').attr('disabled','disabled');
				}
			});
	
			// set save button click event
			myModal.find('button.save').on('click', function(e){
	
				myModal.find('.manytomany-pivot-input').each( function(){
	
					var opener_name = myModal.attr('opener-name');
					var pivot_input_name = $(this).attr('name').replace('pivots-option[','').replace(']','')
					var hidden_name = opener_name + '[' + pivot_input_name + ']';
	
					//exist-check
					var target = myRoot.children('input[name="' + hidden_name + '"]');
	
					if( target.length ){
	
						//update
						target.val( $(this).val() );
						
					}else{
	
						//add
						myRoot.append('<input type="hidden" name="' + hidden_name + '" value="' + $(this).val() + '" parent_name="' + opener_name + '">');
					}
				});
	
				myModal.modal('hide');
			});
	
			// set modal close event
			myModal.on('hide.bs.modal', function(e){
	
				//clear input
				myModal.find('.manytomany-pivot-input').val('');
			});
		});
	
		// delete needless input for many to many relationship
		$('form').submit(function(){
	
			if( $(this).find('.manytomany').length ){
	
				// disable input,select,textarea in modal
				$(this).find('.manytomany-modal input, .manytomany-modal select, .manytomany-modal textarea').attr('disabled','disabled');
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