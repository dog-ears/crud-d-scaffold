$(function(){

	//clear query
	$('form#search').cleanQuery();

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