<?php

require_once POSTSAINT_PLUGIN_PATH . 'vendor/Orhanerday/OpenAi/OpenAi.php';      


$open_ai = new OpenAi( get_site_option('postsaint_settings_openai_api_key') );

$sandbox = null;

// live - make openai API calls
if( $sandbox == null ){


   // if GPT
   if( $openai_model == 'gpt-3.5-turbo' || $openai_model == 'gpt-4' ){

       $complete = $open_ai->chat([
            'model' => $openai_model,
            'messages' => [
                [
                    "role" => "system",
                    "content" => "You are a helpful assistant."
                ],
                [
                  "role" => "user",
                  "content" => $completion_prompt
                ],
            ],

            'temperature' => (float)$openai_temperature,
            'max_tokens' => intval($openai_max_tokens),
            'frequency_penalty' => (float)$openai_frequency_penalty,
            'presence_penalty' => (float)$openai_presence_penalty,
       ]);

    $openai_api_response = json_decode($complete , true); // Converting it into an associative array

      // success
      if( isset($openai_api_response['choices'][0]['message']['content']) ){
         $openai_api_text = $openai_api_response['choices'][0]['message']['content'];
      }    

   } else { // Completion

      $complete = $open_ai->completion([
         'model' => $openai_model,
         'prompt' => $completion_prompt,
         'temperature' => (float)$openai_temperature,
         'max_tokens' => intval($openai_max_tokens),
         'frequency_penalty' => (float)$openai_frequency_penalty,
         'presence_penalty' => (float)$openai_presence_penalty,
      ]);

      $openai_api_response = json_decode($complete , true); // Converting it into an associative array

      // success
      if( isset($openai_api_response['choices'][0]['text']) ){
         $openai_api_text = $openai_api_response['choices'][0]['text'];
      }
   }


   if( isset($openai_api_response['usage']['prompt_tokens']) ){
      $prompt_tokens = $openai_api_response['usage']['prompt_tokens'];
   }

   if( isset($openai_api_response['usage']['completion_tokens']) ){
      $completion_tokens = $openai_api_response['usage']['completion_tokens'];
   }

   if( isset($openai_api_response['usage']['total_tokens']) ){
      $total_tokens = $openai_api_response['usage']['total_tokens'];
   }

   // error
   if( isset($openai_api_response['error']['message']) ){

      $openai_api_error_message = $openai_api_response['error']['message'];

      $openai_error = 1;
   }

   // full JSON response, for logging
   $openai_api_json_response = $complete;

   # image
   if( $image_generator == 'dalle'){

      if( !empty($image_prompt) ){

         $image_response = $open_ai->image([
            "prompt" => $image_prompt,
            "n" => 1,
            "size" => $openai_image_size,
            "response_format" => "url",
         ]);

         $openai_api_image_response = json_decode($image_response , true); 

         //success
         if( isset($openai_api_image_response['data'][0]['url']) ){

            $featured_image_url = $openai_api_image_response['data'][0]['url'];
         }

         // error
         if( isset($openai_api_image_response['error']['message']) ){

            $openai_api_image_error_message = $openai_api_image_response['error']['message'];

            $openai_image_error = 1;
         }   

         // for logging
         $openai_api_json_image_response = $image_response;
      }
   }

} else { // sandbox - return fake responses

   
   // return completion_prompt as API result
   $openai_api_text = $completion_prompt;


   $prompt_tokens = 5;
   $completion_tokens = 7;
   $total_tokens = 12;
   $openai_api_json_response = '{DUMMY JSON TEXT}';


   #image
   if( !empty($image_prompt) ){

      $openai_api_json_image_response = "{IMAGE RESPONSE}";   
   }
   
}
