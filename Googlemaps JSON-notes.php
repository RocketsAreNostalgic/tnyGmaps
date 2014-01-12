<?php

// Phrase json from api3
//http://stackoverflow.com/a/9991851/362445
// not sure about how they are iterating over the array

$geocode=file_get_contents('http://maps.googleapis.com/maps/api/geocode/json?latlng=48.283273,14.295041&sensor=false');

        $output= json_decode($geocode);

    for($j=0;$j<count($output->results[0]->address_components);$j++){
                echo '<b>'.$output->results[0]->address_components[$j]->types[0].': </b>  '.$output->results[0]->address_components[$j]->long_name.'<br/>';
            }



// 


//http://stackoverflow.com/a/4343691/362445 iterate over a mulidimentional array
$jsonIterator = new RecursiveIteratorIterator(
    new RecursiveArrayIterator(json_decode($json, TRUE)),
    RecursiveIteratorIterator::SELF_FIRST);

foreach ($jsonIterator as $key => $val) {
    if(is_array($val)) {
        echo "$key:\n";
    } else {
        echo "$key => $val\n";
    }
}

// check the current post for the existence of a short code
function has_shortcode($shortcode = '', $template ) {
	// false because we have to search through the post content and templates first
	$found = false;
	// if no short code was provided, return false
	if (!$shortcode) {
		return $found;
	}

	function post_search($shortcode){
		$post_to_check = get_post(get_the_ID());
		// check the post content for the short code
		// preg_match( '#\[ *shortcode([^\]])*\]#i', $content ); // http://wordpress.stackexchange.com/a/20867/13551
		if ( stripos($post_to_check->post_content, '[' . $shortcode) !== false ) {
			// we have found the short code
			$found = true;
		}
	}
	add_action('template_include','prefix_template_check_shortcode');
	function template_search( $template ){
		$shortcode = 'exmple'
		$files = array( $template, get_stylesheet_directory() . DIRECTORY_SEPARATOR . 'header.php', get_stylesheet_directory() . DIRECTORY_SEPARATOR . 'footer.php' );
		foreach( $files as $file ) {
    		if( file_exists($file) ) {
       		 $contents = file_get_contents($file);
        		if( strpos( $contents, '[' . $shortcode )  ) {
        			// we have found the short code
        			$found = true;
        		}
    		}
		}
	}

	function widget_search($shortcode){

	}
	add_action('widget_title', 'widget_search', 11); // 11 will occur after default widget_title filters
// return our final results
return $found;
}

// check in template first
function prefix_template_check_shortcode( $template ) {
	$shortcode = ''
// This assumes the templates are here, and not in parts?
$files = array( $template, get_stylesheet_directory() . DIRECTORY_SEPARATOR . 'header.php', get_stylesheet_directory() . DIRECTORY_SEPARATOR . 'footer.php' );
foreach( $files as $file ) {
    if( file_exists($file) ) {
        $contents = file_get_contents($file);
        if( strpos( $contents, '[' . $shortcode )  ) {}
    }
}
return $template;
}
add_action('template_include','prefix_template_check_shortcode' );

