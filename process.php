<?php
	
	require_once 'simple_html_dom.php';

	$errors = array(); //To store errors
    $form_data = array(); //Pass back the data to `form.php`
    $html_filename_array = array();
    $html_content = null;
    
    $MAX_NUM_OF_URLS_ALLOWED = 5;
    $urls = sanitise_input($_POST['urls']);
    (empty($urls)) ? ($errors['urls'] = 'URLs cannot be blank'):null;
    $urls_array = explode("\n", $urls);
	(count($urls_array) > $MAX_NUM_OF_URLS_ALLOWED) ? ($errors['urls'] = "Max allowed number of URLs: ".$MAX_NUM_OF_URLS_ALLOWED):null;
	$urls_array = array_filter($urls_array, 'trim');		
	foreach($urls_array as $url){
			is_valid_url($url) ? null : ($errors['urls'] = "Invalid url:".$url);}
	
	//get html data
	foreach($urls_array as $url){
			$html_content = file_get_contents($url);
			$file_name = clean($url);
			if($html_content !=null){ 
				file_put_contents($file_name, $html_content);
				array_push($html_filename_array, $file_name);
				}
			else {
				$errors['urls'] =  "could not fetch url ".$url; break;
				}
			//dont forget to delete files once all analyzed
			$html_content = null;
		}
	
	
    if (!empty($errors)) { //If errors in validation
    	$form_data['success'] = false;
    	$form_data['errors']  = $errors;
    } else { //If not, process the form, and return true on success
    	$form_data['success'] = true;
    	$form_data['posted'] = $html_filename_array;
    }
    //Return the data back to form.php
    echo json_encode($form_data);


	/* Util Functions*/

    function is_valid_url($entered_url){
		$pattern = "#\b(([\w-]+://?|www[.])[^\s()<>]+(?:\([\w\d]+\)|([^[:punct:]\s]|/)))#i";
		return preg_match($pattern,$entered_url);
		}

	function sanitise_input($data) {
	   $data = trim($data);
	   $data = stripslashes($data);
	   $data = htmlspecialchars($data);
	   return $data;
	}

	function clean($string) {
	   return preg_replace('/[^A-Za-z0-9]/', '', $string); // Removes special chars.
	}
