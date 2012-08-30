function dataTable(params){
	
	var element = false;
	if(!$.isPlainObject(params)){
		throw "Invalid parameters applied to the dataTable function";
	}
	// Get the datatable's Table element
	if( !params.id ){
		throw "Please provide ID of table for dataTables's Grid";
	} else {
		if(typeof(params.id) == "object" && $(params.id).attr("id") ){
			element =  "#" + $(params.id).attr("id");
		} else if (typeof(params.id) == "string" && $("#"+params.id).attr("id")){
			element = $( "#" + params.id).attr("id");
		} else {
			throw "Please provide ID to the table you want to apply dataTables";
		}
	}
	
	//Filter Form
	var filterForm = false;
	if( params.filterForm ){
		if(typeof(params.filterForm) == "object" && $(params.filterForm).attr("id") ){
			filterForm = "#" + $(params.filterForm).attr("id");
		} else if (typeof(params.filterForm) == "string" && $("#"+params.filterForm).attr("id")){
			filterForm = "#" + params.filterForm;
		} else {
			throw "Please provide prper ID for filter form to the table you want to apply dataTables";
		}
	}
	
	// Check for server side enabled and ajax source
	if ( params.bServerSide || params.bServerSide == true ){
		if( !params.sAjaxSource ){
			throw "Invalid sAjaxSoure defined for the datagrid"; 
		}
	} 
	
	if ( !params.aoColumns ){
		throw "No aoColumns define for the grid";
	}
	
	var gridObject = {
    	"aoColumns": params.aoColumns,
		"bPaginate" : params.bPaginate ? params.bPaginate : true,
		"bDestroy": params.bDestroy ? params.bDestroy : true,	
        "bProcessing": params.bProcessing ? params.bProcessing : false,
        "bServerSide": params.bServerSide ? params.bServerSide : true,
        "bSortClasses": params.bSortClasses ? params.bSortClasses : false,       
        "sAjaxSource": params.sAjaxSource,
        "fnServerData": function( sSource, aoData, fnCallback ){
        	var blockElement = $(element).parent();
            $(document).queue(function(next){ 
                blockElement.block({
                    message: "Loading Grid Data...",
                	onBlock: next
                });
			}).queue(function(next){
				$.ajax({
	                type: "POST",
	                url : sSource,
	                data: aoData,
	                cache: false,
	                dataType: "json",
	                success: function(json){fnCallback(json);}
	            }).complete(next);
			}).queue(function(next){
				blockElement.unblock({
					onUnblock: next
				});
			});
        },
        "fnServerParams": function ( aoData ) {
        	if(filterForm){
        		tmData= $(filterForm).serializeArray();
               	$(tmData).each(function(){
                   	name= "search_" + $(this).attr("name");
        			value= $(this).attr("value");                
                   	aoData.push( { "name": name, "value": value } );
    			});
        	}
		},
		"fnDrawCallback":function(){
			$(element+"_length , " + element + "_filter").hide();
		}
	};
	return $(element).dataTable(gridObject);
}