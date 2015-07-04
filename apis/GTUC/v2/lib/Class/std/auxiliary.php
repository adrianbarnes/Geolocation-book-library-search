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
	 public  static  function is_json($str){ 
      return json_decode($str) != null;
    }

	#############################################################################################
	# 								  END  AUXILIARY   MODULES 
	#############################################################################################

}
