 <?php 

 /*###################################################################################################
	
	## 	API of GTUC Search Engine
	## 	Made 24/05/2015 by   
	## 	Supervised by ...
	##  v1.0

*###################################################################################################/

/*
	Dependencies
*/

require_once(dirname(dirname(__FILE__)).'/libs/config.php') ;
require_once(dirname(dirname(__FILE__)).'/libs/db_io_stat.php') ;
require_once(dirname(dirname(__FILE__)).'/libs/auxiliary.php') ;

# All the status code of the University Library  
class Status
{
	public static $SUCCESS   = "00";
	public static $ERROR     = "01";   
	public static $NOTFOUND  = "05";
}


# All the services of the University Library 
 class Modules
 {

 	private static $GTUC_endpoint = "http://localhost/goodluck/apis/GTUC/v2/index.php";

	/**
	*	Methods use 
	*@param  status code : String 
	*@param  description details  : String 
	*@return  Json Object 
	*/
 	public  function search($keyword ,$category=false, $is_exact_match="false"  ){
 		try{
 			#initialize input
 			global $con;
 			$criteria = "";
 			$query    = "";
 			$responses = array();
 			$category= isset($category)? trim($category) :null;

 			# Validate  params 
 			$keyword = trim($keyword);
 			if($keyword == "")
 				return self::reply(Status::$ERROR, "fatal error occured @X1, Please contact admin"); # Keyword is empty

 			if( is_null($con) )
 				return self::reply(Status::$ERROR, "fatal error occured @X1, Please contact admin"); # Error on DB 
 			
 			# Perform search in GTUC 
 			$field = $_REQUEST;
 			
 			$res = aux::curl2( self::$GTUC_endpoint, $field );
 			if(!aux::is_json($res))
 			    return self::reply(Status::$ERROR, "fatal error occured @X2, Please contact admin"); # Error on DB 

 			$res = json_decode($res);
 			if( $res->code ==Status::$SUCCESS and is_array($res->msg) )
 			{
 			    $univ_ressources = $res->msg;
 			    foreach ($univ_ressources as $ressource ) {
 			    	$responses[] = $ressource;
 			    }
 			}

 			# Perform search  for partners universities 
 			 $res = $this->load_partners_universities();
 			 
 			 if(!aux::is_json($res))
 			 	return self::reply(Status::$ERROR, "fatal error occured @X1, Please contact admin"); # Keyword is empty

 			 $res = json_decode($res);
 			 if($res->code == Status::$SUCCESS)
 			 {
 			 	$universities = $res->msg;

 			 	# Proccess  search in all the partner universities 
 			    foreach ($universities  as $university ) {
 			 		
 			 		# Perfom search in each partner university by order of  proximity
 			 		$url = $university->endpoint;
 			 		$shorname = $university->short_name;
 			 		$fullname = $university->institution_name;
 			 		$field = $_REQUEST;
 			    	$res = aux::curl2( $url, $field );
 			    	
 			    	if(!aux::is_json($res))
 			    		continue;
 			    	$res = json_decode($res);

 			    	if( $res->code ==Status::$SUCCESS and is_array($res->msg) )
 			    	{
 			    		$univ_ressources = $res->msg;
 			    		foreach ($univ_ressources as $ressource ) {
 			    			$responses[] = $ressource;
 			    		}
 			    	}
 			    		
 				}

 			 }

 			 if(count($responses) == 0 )
 					return self::reply(Status::$NOTFOUND, "No result found ");
 				else 
 					return self::reply(Status::$SUCCESS, $responses);

 		}catch(Exception $e){ return self::reply(Status::$ERROR, "fatal error occured@X4, Please contact admin"); }
 			
 	}



	/**
	*
	*@param  status code : String 
	*@param  description details  : String 
	*@return  Json Object 
	*/
 	private static function reply($code , $message){
 		return json_encode(array("code"=>$code,"msg"=>$message));
 	}#


 	/**
	*
	*@param  void
	*@return  JSON Object 
	*/
 	public static function load_partners_universities(){

 		global $con;
 		$query =" SELECT * FROM univ_partners WHERE status = 1 ORDER BY distance ASC";
 		$res = DB_io::select($con, $query);

 		# Case : response format is corrupted 	
 		if( !($res = json_decode($res)) )
 			return self::reply(Status::$ERROR, "fatal error occured@X2, Please contact admin");

 		if( $res->status =="200" )
 		{
 			#if result  is empty 
 			if(count($res->message) == 0 )
 				return self::reply(Status::$NOTFOUND, array());
			else 
 				return self::reply(Status::$SUCCESS, $res->message);
 		}
 		else
 			return self::reply(Status::$ERROR, "fatal error occured @X3, Please contact admin".$res->message); # Error on DB 
 		return json_encode(array("code"=>$code,"msg"=>$message));
 	}
	

 }


?>