$(document).ready(function(){

	function _search() {
		
		var search = $("#book").val();
		var is_matched_checked  = document.getElementById("match").checked ;
		var selectedvalues = $("#femi ").val() == null ? "":JSON.stringify( $("#femi ").val());

		if(is_matched_checked == true)
			is_matched_checked ="true";
		else
			is_matched_checked ="false";
		
		$.ajax({
			type: "POST",
			//url: "http://teknolojiagh.com/glib/search.php",
			url:"http://localhost/goodluck/apis/engine/v1/index.php",
			data:  {"keyword":search,"is_exact_match":is_matched_checked, "category":selectedvalues},
			success: function(res) {
				//$("#result").html(res);
				//$("#book").val("");
				res = JSON.parse(res);

				if(res['code'] == "00") // if succesful 
					processData(res);
				else
					$("#result").html("No result Found");


			console.log(res);
				//console.log(JSON.stringify(selectedvalues));
			},
			error: function(err)
			{
				$("#result").html("No result Found");
				$("#book").val("");
				console.log(err);
			}
		});
		
		
	}



	$(".btn-search").click(function(){
		$( "ul" ).empty();
	                  	 _search();
	});


	function processData(data){
				    	//alert(data.msg);

				    	$.each(data.msg, function(i, item){

					    	var listings = "<div class='row' style='margin-top:0px'>";
					    		listings += "<div id='eachBook' class='span12 tile eachBook'></div>";
								listings += "<div class='tile-content' style='padding-left:1%'>";

								listings += "<h2 class='details' style='font-size: 2rem; width: 63%'><b>" + item.title + "<b></h2>";
								listings += "<div style='height: 28px'></div>";

								listings += "<h2 class='details' style='font-size: 1.4rem'>Author: " + item.author + "</h2>";
								listings += "<h2 class='details' style='font-size: 1.4rem; left: 52.5%; width: 30%'>Publisher: " + item.publisher + "</h2>";
								listings += "<div style='height: 28px'></div>";

								listings += "<h2 class='details' style='font-size: 1.3rem;'>ISBN: " + item.isbn +"</h2>";
								listings += "<h2 class='details' style='font-size: 1.4rem; left: 52.5%; width: 30%''>Edition: " + item.edition + "</h2>";
								listings += "<div style='height: 28px'></div>";

								listings += "<h2 class='details' style='font-size: 1.3rem;'>Categories: " + item.category + "</h2";
								listings += "<h2 class='details' style='font-size: 1.4rem; left: 52.5%; width: 30%''>Library: " + item.edition + "</h2>";
								listings += "<div style='height: 28px'></div>";

								listings += "</div></div>";


				    		$('#result').append(listings);
				    	});// end each
				    }// end processDat
});



