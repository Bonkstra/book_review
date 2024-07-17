<?php

function debug($value, $die = false, $overwrite = false)
{
	if(App::environment('local') || $overwrite) {
	    echo '<pre>';
	    print_r($value);
	    echo '</pre>';

	    if($die) {
	        die;
	    }
	}
}
