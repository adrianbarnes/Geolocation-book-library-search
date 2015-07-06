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

				if(res['code'] == "00"){
					processData(res);
				} // if succesful 
				else{
					$("#result").html("No result Found");

					$('#status').fadeOut(); // will first fade out the loading animation
					$('#preloader').delay(350).fadeOut('slow'); // will fade out the white DIV that covers the website.
					$('body').delay(350).css("display","");
				}


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
		$("body").css("display","none");
		$("#preloader, #status").css("display","");
		

		$( "ul#result" ).empty();
		$('#other-universities').empty();
	    _search();
	});

	function refreshEvent(){

		$('.expand').click(function(){
	    	$(this).parents().eq(1).addClass('details-open');
	    	return false;
	    });//refresh the event of adding the details-open class, when details is clicked

	    $('.close-details').click(function(){
	    	$(this).parents().eq(1).removeClass('details-open');
	    });//refreshh the event of enabling the close button remove class open-details
	}


	function processData(data){
    	//alert(data.msg);
	    var gtuc=[],
			legon=[],
			ucc=[],
			knust=[];

		$('#status').fadeOut(); // will first fade out the loading animation
		$('#preloader').delay(350).fadeOut('slow'); // will fade out the white DIV that covers the website.
		$('body').delay(350).css("display","");
    	$.each(data.msg, function(i, item){

	    	/*var listings = "<div class='row' style='margin-top:0px'>";
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

				listings += "</div></div>";*/


			var listings2 = "<figure>";

				listings2 += "<div class='perspective'><div class='book' data-book='book-1'><div class='cover'>"; 
				listings2 += "<div class='front'></div> <div class='inner inner-left'></div> </div> <div class='inner inner-right'>";
				listings2 += "</div> </div> </div>";

				listings2 += "<div class='buttons'><a href=''></a><a href='#' class='expand'>Details</a></div>";
				listings2 += "<figcaption><h2>" + item.title + "<span>" + item.author + "</span></h2></figcaption>";
				listings2 += "<div class='details'><ul>";
				listings2 += "<li>(Book Description) Lorem ipsum dolor sit amet, consectetur adipisicing elit,";
				//listings2 += " sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.";
				listings2 += "</li>";
				listings2 += "<li>Publisher: " + item.publisher + "</li>";
				listings2 += "<li>Date of Release</li>";
				listings2 += "<li>Edition: " + item.edition + "</li>";
				listings2 += "<li>ISBN: " + item.isbn + "</li>";
				listings2 += "<li>Categories: " + item.category + "</li>";
				listings2 += "</ul>";
				listings2 += "<span class='close-details'></span>";
				listings2 += "</div></figure>";
			 

			if(item.university=="GTUC"){
				$('#result').append(listings2);
				gtuc.push(listings2);
			}else if(item.university=="LEGON"){
				legon.push(listings2);
			}else if(item.university=="UCC"){
				ucc.push(listings2);
			}else if(item.university=="KNUST"){
				knust.push(listings2);
			}//show results for GTUC alone and hide results for all other universities

			
    	});// end each
		$('#bookshelf').append('<div id="other-universities"><button id="show-gtuc" class="button-blue">Results for GTUC</button><button id="show-legon" class="button-blue">Results for LEGON</button><button id="show-ucc" class="button-blue">Results for UCC</button><button id="show-knust" class="button-blue">Results for KNUST</button><br /></div> ');
		refreshEvent();

		$('#show-gtuc').click(function(){
			$('ul#result').empty();
			$.each(gtuc, function(i, item){
				$('#result').append(gtuc[i]);
			})// end each
			refreshEvent();
		});// end show legon
		$('#show-legon').click(function(){
			$('ul#result').empty();
			$.each(legon, function(i, item){
				$('#result').append(legon[i]);
			})// end each
			refreshEvent();
		});// end show legon
		$('#show-ucc').click(function(){
			$('ul#result').empty();
			$.each(ucc, function(i, item){
				$('#result').append(ucc[i]);
			})// end each
			refreshEvent();
		});// end show ucc
		$('#show-knust').click(function(){
			$('ul#result').empty();
			$.each(knust, function(i, item){
				$('#result').append(knust[i]);
			})// end each
			refreshEvent();
		});// end show legon

		


    }// end processData
});



