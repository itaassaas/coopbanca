<?php

use App\Models\Currency;
use App\Models\Generalsetting;



if(!function_exists('showPrice')){
  function showPrice($price, $currency){
      $gs = Generalsetting::first();
      
      // Calculate price with currency value and round to 2 decimals
      $price = round(($price) * $currency->value, 2);
      
      // Format number with thousand separator and 2 decimal places
      $formatted_price = number_format($price, 2, '.', ',');
      
      // Return based on currency format setting
      if($gs->currency_format == 0){
          return $currency->sign . $formatted_price;
      } else {
          return $formatted_price . $currency->sign;
      }
  }
  

  if(!function_exists('convertedPrice')){
    function convertedPrice($price,$currency){
      return $price = $price * $currency->value;
    }
  }

  if(!function_exists('defaultCurr')){
    function defaultCurr(){
      return Currency::where('is_default','=',1)->first();
    }
  }


?>
