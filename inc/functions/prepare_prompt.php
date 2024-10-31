<?php

# completion prompt

// prepend prompt
if( !empty($prepend_prompt) ){

	$completion_prompt = $prepend_prompt.' '.$completion_prompt;
}

// append prompt
if( !empty($append_prompt) ){

	$completion_prompt = $completion_prompt.' '.$append_prompt;
}

// writing style
if( $writing_style != 'unspecified'){

	$completion_prompt = $completion_prompt.' in a '.$writing_style.' writing style';
}

// writing tone
if( $writing_tone != 'unspecified' ){

	$completion_prompt = $completion_prompt.' in a '.$writing_tone.' writing tone';
}

// keywords
if( !empty($keywords) ){

	$completion_prompt = $completion_prompt.' keywords:'.$keywords;
}


# image prompt

// image style
if( !empty($image_prompt) && $image_style != 'unspecified'){

	$image_prompt = $image_prompt.' in '.$image_style.' style';
}

// image style
if( !empty($image_prompt) && $artist_style != 'unspecified'){

	$image_prompt = $image_prompt.' in '.$artist_style.' style';
}

?>