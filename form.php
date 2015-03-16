<?php $prefill = 'http://knowpapa.com/
http://knowpapa.com/num2words/';
?>
<!DOCTYPE html>
<html lang="en">
	<head>
		<title>Simple Ajax Form</title>
		<script src="http://ajax.googleapis.com/ajax/libs/jquery/2.0.3/jquery.min.js"></script>

		<script>
			$(document).ready(function() {
		    	$('form').submit(function(event) { //Trigger on form submit
		    		$('.throw_error').empty(); //Clear the messages first
		    		$('#unuseditemresult').empty();
		    
		    		var postForm = { //Fetch form data
		    			'urls' 	: $('textarea[name=urls]').val(),
		    			'cssurl' : $('input[name=cssurl]').val()
		    		
		    		};
					$("#loadingimage").show();
		    		$.ajax({ //Process the form using $.ajax()
		    			type 		: 'POST', //Method type
		    			url 		: 'process.php', //Your form processing file url
		    			data 		: postForm, //Forms name
		    			dataType 	: 'json',
		    			success 	: function(data) {
		    				$("#loadingimage").hide();
		    			if (!data.success) { //If fails
							if (data.errors.urls) { //Returned if any error from process.php
		    					$('.throw_error').fadeIn(1000).html(data.errors.urls); //Throw relevant error
		   					}
		   				} else {
							
							$('#unuseditemresult').fadeIn(1000).append( "<h2>Unused Items</h2>");
							$.each(data.unused, function(index, value) {
									$('#unuseditemresult').fadeIn(1000).append( value + ", ");
								});
							
							$('#useditemresult').fadeIn(1000).append( "<h2>Used Items</h2>");
							$.each(data.used, function(index, value) {
									$('#useditemresult').fadeIn(1000).append( value + ", ");
								});
							
							
							
							}
		    			}
		    		});
		    	    event.preventDefault(); //Prevent the default submit
		    	});
		    });
		</script>
		<style>
			#useditemresult, #unuseditemresult, #loadingimage, .throw_error {
				display: none;
				margin: 5px 2px;
			}
			#unuseditemresult, .throw_error {
				color:tomato;
				}

			#useditemresult, #loadingimage {
				color: green;
				}
			
			textarea {
				padding: 5px;
				box-shadow: inset 0 0 5px #eee;
				border: 1px solid #eee;
			}

			input[type=submit] {
				padding: 3px 8px;
				cursor: pointer;
				
			}
		</style>
	</head>
	<body>
		<form method="post" name="postForm">
					<label for="urls">URLs</label><br>
					<textarea name="urls" id="urls" rows="5" cols="40"><?php echo $prefill; ?></textarea><br>
					<span class="throw_error"></span><br>
					<input type="text" name="cssurl"><br>
					<input type="submit" value="Send" /><br>
		</form>
		<div id="loadingimage">Analysing CSS:  <img src="ajax-loader.gif" /></div>

		<div id="unuseditemresult"></div>
		<div id="useditemresult"></div>
		
	</body>
</html>
