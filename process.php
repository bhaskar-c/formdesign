<?php
	
	   require_once 'simple_html_dom.php';
       require_once 'cssparser.php';

	$urls = sanitise_input($_POST['urls']);
	$cssurl = sanitise_input($_POST['cssurl']);

	$errors = array(); //To store errors
    $form_data = array(); //Pass back the data to `form.php`
    $html_content_array = array();
    $html_content = null;
    $MAX_NUM_OF_URLS_ALLOWED = 5;

	$used = array();
	$unused = array();
    
    //initial validation
    (empty($urls)) ? ($errors['urls'] = 'URLs cannot be blank'):null;
    $urls_array = explode("\n", $urls);
	(count($urls_array) > $MAX_NUM_OF_URLS_ALLOWED) ? ($errors['urls'] = "Max allowed number of URLs: ".$MAX_NUM_OF_URLS_ALLOWED):null;
	$urls_array = array_filter($urls_array, 'trim');		
	foreach($urls_array as $url)
			is_valid_url($url) ? null : ($errors['urls'] = "Invalid url:".$url);
	are_from_same_domain($urls_array) ? null:($errors['urls'] = "Multiple domain queries not allowed");
	is_valid_css_url($cssurl) ? null:($errors['urls'] = "Invalid css url");
	
	//apparantly OK - so start processing. there may be more errors as you proceed
	if (empty($errors)) {
		foreach($urls_array as $url){
			$html_content = file_get_html($url);
			if($html_content !=null){ 
				array_push($html_content_array, $html_content);
				}
				else {
					$errors['urls'] =  "could not fetch url ".$url; break;
					}
			$html_content = null;
		}
		//colect used and unsed items
		$css =  parse($cssurl);
		//start with assumption that every thing is unused
		foreach(array_keys($css) as $cssitem)
			array_push($unused, $cssitem);
			
		//now as you find it is used, push to used, pop from unused
		foreach( $html_content_array as $html) {
			foreach($unused as $u){
				if(null !=($html->find(trim(explode(':', $u, 2)[0]), 0))){
					array_push($used, $u);
					$unused = array_diff($unused, array($u));
					//array_splice($unused, $u, 1);
				}
				
				
			}
			
		}
	}
		
		
		
			
	if (!empty($errors)) { //If errors in validation
    	$form_data['success'] = false;
    	$form_data['errors']  = $errors;
    } else { //If not, process the form, and return true on success
    	$form_data['success'] = true;
    	$form_data['posted'] = $unused;
    }
    //Return the data back to form.php
    echo json_encode($form_data);


	/* Util Functions*/
	function get_domain($url){
	  $pieces = parse_url($url);
	  $domain = isset($pieces['host']) ? $pieces['host'] : '';
	  if (preg_match('/(?P<domain>[a-z0-9][a-z0-9\-]{1,63}\.[a-z\.]{2,6})$/i', $domain, $regs)) {
		return $regs['domain'];
	  }
	  return false;
	}
	
	
	function are_from_same_domain($urlarray){
		$domainname = 	get_domain($urlarray[0]);
		foreach($urlarray as $url)
			if(get_domain($url) != $domainname) {return false;}
		return true;	
		}
	
	
	
	function is_valid_css_url($entered_url){
		return (is_valid_url($entered_url) and (stripos(strrev(reset(explode('?', $entered_url))), "ssc.") === 0)); //valid url + ends with .css reversed needle
		}

	
	
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
