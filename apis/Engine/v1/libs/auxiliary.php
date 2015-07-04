<?php


	#############################################################################################
	# 								      AUXILIARY   MODULES 
	#############################################################################################



### fatcgi (SOLVED)

class aux
{



  /*#################################################
    @Descri : 
    @Params : 
    @Return : 
  */#################################################
 public  static  function analyseInput( $elements ,$field)  ### module to verify the misssing field in a request 
 {  
    try{
      foreach ( $elements as $key) {
          if( !isset( $field[$key] ) )
            return  "The parameter ".$key." is required";     
      }
    } catch( Exception $e ){  return  "fatal Error";    }
    return true;
 }


 /*#################################################
    @Descri : 
    @Params : 
    @Return : 
  */#################################################
 public  static  function curl2($url,$fields){

  $ch  = curl_init();
  $options = array(
      CURLOPT_URL => $url,
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_CONNECTTIMEOUT => 15,
      CURLOPT_POSTFIELDS => http_build_query($fields),
      CURLOPT_POST => true
    );
    curl_setopt_array($ch,$options);
    $output = curl_exec($ch);
    if (!$output) {
      $output = null;
    }
    curl_close($ch);
  return $output;
}

  /*#################################################
    @Descri : 
    @Params : 
    @Return : 
  */#################################################
	 public  static  function is_json($str){ 
      return json_decode($str) != null;
    }

	#############################################################################################
	# 								  END  AUXILIARY   MODULES 
	#############################################################################################

}
