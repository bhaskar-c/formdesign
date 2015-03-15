<?php $prefill = 'http://knowpapa.com/
http://knowpapa.com/num2words/
http://knowpapa.com/visualizing-music/ 
http://knowpapa.com/sitemap/';
?>
<!DOCTYPE html>
<html lang="en">
	<head>
		<title>Simple Ajax Form</title>
		<script src="http://ajax.googleapis.com/ajax/libs/jquery/2.0.3/jquery.min.js"></script>

		<script>
			$(document).ready(function() {
		    	$('form').submit(function(event) { //Trigger on form submit
		    		$('#urls + .throw_error').empty(); //Clear the messages first
		    		$('#success').empty();
		    
		    		var postForm = { //Fetch form data
		    			'urls' 	: $('textarea[name=urls]').val() //Store name fields value
		    		};
					$("#LoadingImage").show();
		    		$.ajax({ //Process the form using $.ajax()
		    			type 		: 'POST', //Method type
		    			url 		: 'process.php', //Your form processing file url
		    			data 		: postForm, //Forms name
		    			dataType 	: 'json',
		    			success 	: function(data) {
		    				$("#LoadingImage").hide();
		    			if (!data.success) { //If fails
							if (data.errors.urls) { //Returned if any error from process.php
		    					$('.throw_error').fadeIn(1000).html(data.errors.urls); //Throw relevant error
		   					}
		   				} else {
							var datarecd = $.each( data.posted, function(index,val){ index + ": " + val+ '<br>';});
							$('#success').fadeIn(1000).append(datarecd); //If successful, than throw a success message
		    				}
		    			}
		    		});
		    	    event.preventDefault(); //Prevent the default submit
		    	});
		    });
		</script>
		<style>
			ul {
				font-family: Arial;
				list-style-type: none;
			}

			#success, #LoadingImage {
				display: none;
				font-family: Arial;
				color: green;
				margin: 15px 85px;
				font-size: 14px;
			}

			textarea {
				padding: 5px;
				box-shadow: inset 0 0 5px #eee;
				border: 1px solid #eee;
			}

			input[type=submit] {
				padding: 3px 8px;
				background: #eee;
				margin-left: 85px;
				cursor: pointer;
				border: 1px solid #aaa;
				font-size: 12px;
			}

			.throw_error {
				color:tomato;
				font-size: 12px;
				display: none;
			}

			label {
				font-size: 13px;
			}
		</style>
	</head>
	<body>
		<form method="post" name="postForm">
			<ul>
				<li>
					<label for="urls">Name</label>
					<textarea name="urls" id="urls" rows="5" cols="40"><?php echo $prefill; ?></textarea>
					<span class="throw_error"></span>
				</li>
			</ul>
			<input type="submit" value="Send" />
		</form>
		<div id="LoadingImage" style="display: none">Fetching URL:  <img src="ajax-loader.gif" /></div>

		<div id="success"></div>
	</body>
</html>
