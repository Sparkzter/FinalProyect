$( document ).on('ready', function() {
	$('#homeTab').show();
	$("#home").css('background-color', '#737373');
	$("#DissorderSpace").load("data/Dissorder.html");
	$("#SupportSpace").load("data/Support.html");

	$('.navTab').on('click', function(){
		var tabPressed = $(this).attr('id');
		switch(tabPressed){
			case 'home':
				$('.navTab').css('background-color', '#CCCCCC');
				$(this).css('background-color', '#737373');

				$('.contentTab').hide();
				$('#homeTab').show();
				break;

			case 'AboutDis':
				$('.navTab').css('background-color', '#CCCCCC');
				$(this).css('background-color', '#737373');

				$('.contentTab').hide();
				$('#AboutDisTab').show();
				break;

			case 'supporting':
				$('.navTab').css('background-color', '#CCCCCC');
				$(this).css('background-color', '#737373');

				$('.contentTab').hide();
				$('#SupportTab').show();
				break;
			
			case 'comments':
				$('.navTab').css('background-color', '#CCCCCC');
				$(this).css('background-color', '#737373');

				$('.contentTab').hide();
				$('#CommentTab').show();
				break;
				
			case 'Meetings':
				$('.navTab').css('background-color', '#CCCCCC');
				$(this).css('background-color', '#737373');

				$('.contentTab').hide();
				$('#MeetingTab').show();
				break;
			
			case 'MyMeetings':
				$('.navTab').css('background-color', '#CCCCCC');
				$(this).css('background-color', '#737373');

				$('.contentTab').hide();
				$('#MyMeetingTab').show();
				break;
			
			case 'login':
				window.location.replace('login.html');
				break;

			case 'logout':
				$.ajax({
                    type: 'POST',
                    url: 'data/applicationLayer.php',
                    dataType: 'json',
                    data: {'action':'END_SES'},
                    headers: {'Content-Type': 'application/x-www-form-urlencoded'},
                    success: function(jsonData) {
                        window.location.replace('home.html');
                    },
                    error: function(errorMsg){
                        alert(errorMsg.statusText);
                        window.location.replace('home.html');
                    }
                });
				break;
		}
	});
	$("#sort").on('click',function(){
		loadMarketplace();
	});
});

function loadMarketplace(){
	var sort = "NONE";
	if( $('input[type=radio]:checked').size() > 0 ){
		sort = $('input[type=radio]:checked').val();
	}
	jsonObject = {"action":"LOAD_MARKETPLACE", "sort":sort};

	$.ajax({
	    type: 'POST',
	    url: 'data/applicationLayer.php',
	    dataType: 'json',
	    data: jsonObject,
	    headers: {'Content-Type': 'application/x-www-form-urlencoded'},
	    success: function(jsonData) {
	    	$("#marketplaceContainer").html("");
            var auxAppend = "";
            for(var i = 0; i < jsonData.data.length; i++){
            	auxAppend += '<div class="itemContainer">';
            		auxAppend += '<h2>' + jsonData.data[i].item + '</h2>';
            		auxAppend += '<img src="img/' + jsonData.data[i].image + '" class="itemImage">';
            		auxAppend += '<div style="padding:5px;">';
            			auxAppend += '<p>';
            				auxAppend += jsonData.data[i].description;
            			auxAppend += '</p>';
	            		auxAppend += '<div style="text-align: right;">'
	            			auxAppend += '<strong>$' + jsonData.data[i].price + '</strong><br>';
	            			auxAppend += 'Qty: '+jsonData.data[i].price + '<br>';
	            			auxAppend += 'Buy:<input class="quantBuy" type="number" value="0">'
            			auxAppend += '</div>';
            		auxAppend += '</div>';
            	auxAppend += '</div><br>';
            }
            auxAppend += '<button type="button" id="buy" class="buttonStyle">Add to cart</input>';
            $("#marketplaceContainer").append(auxAppend);
	    },
	    error: function(errorMsg){
	        alert(errorMsg);
	    }
	});
}

function validateUserSession(){
	//Validate user session
    $.ajax({
        type: 'POST',
        url: 'data/applicationLayer.php',
        dataType: 'json',
        data: {'action':'GET_SES'},
        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
        success: function(jsonData) {
            $("#userLogout").html(jsonData.lastName + '?');
        },
        error: function(errorMsg){
            console.log(errorMsg.statusText);
            window.location.replace('login.html');
        }
    });
}