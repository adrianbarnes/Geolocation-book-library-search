<?php

/*###################################################################################################
	## 	UCC SIMULATION API  
	## 	Made 15/06/2015 by ...
	##  Last Update on 15/06/2015
	##  v1.0
#####################################################################################################*/

require_once("Class/std/file_io.php");

class API
{

####################################################### 

######################################################## STATUS CODE 
private  $SUCCESS = '201';
private  $ERROR   = '400';
########################################################
private $uri ;
private $method ;
private $route_state ;
private $req ;
private $funct_name ;
private $default_class;
private $root ;

private $api_res_status;
private $api_res_message;

private $is_debug;
private $is_report;
private $is_encrypted;
private $format;
private $api_author;
private $api_name;
private $api_contact;
private $version;
private $api_doc_path;
private $error_log_path ="./Log/Error_Log.txt";


private $allowed_clients_list      = array();
private $api_client                = null ;
private $api_valid_keys            = array();
private $is_all_client_allow       = true;
private $is_client_credetial_valid = false;

private $route_flag = false;

#########################################################

public function __construct($_default_class="", $_root="")
{

	try
	{

	ob_start();
	$this->status=0;
	self::set_default_class($_default_class);

	if(isset($_root))
	self::set_root($_root);
	

	
	$this->is_debug     = false;
	$this->is_report    = false;
	$this->is_encrypted = false;
	$this->api_author   = "UCC UNIV"; 
	$this->api_name     = "UCC UNIV";
	$this->version      = "v1";
	$this->format       = "JSON";
	$this->api_contact  = "www.ucc.com";
	$this->api_doc_path = "./Doc/doc.pdf";
	
   }catch( Exception $e){ self::error_logger(__FUNCTION__ , $e->getMessage()); }


}


public function set_root($new_root)
{
	$this->root = $new_root;
}
public function set_default_class($_default_class)
{
	$this->default_class = $_default_class;
}


####################################################################################################
#							 	    MAIN FUNCTION 												   #				
####################################################################################################


private function init()
{
	try
	{

 	 $this->uri = $_SERVER['REQUEST_URI'];  
	 $this->method = $_SERVER['REQUEST_METHOD'];
	 $this->route_state =0;
	 $this->req ='';
	 $this->funct_name ='';


	 $prime_root = dirname($_SERVER['SCRIPT_NAME']);
	 $this->root = preg_quote($prime_root."/" );

	$root_parttern = "#".$this->root."#i";

	#reformat uri 
	if(preg_match( $root_parttern, $this->uri) && $this->root!="" ) 
		$this->uri  = preg_split( $root_parttern, $this->uri)[1];

	#separate parameters from url in case of GET Method 
	if( strtoupper(trim($this->method)) == "GET" && count($_GET)>0 )
		$this->uri  =explode( "?", $this->uri)[0];



	#validate uri and route it the appropriate call back function 
	if( self::validate_root_uri( $this->uri ) )
	{

		self::router( $this->method ); 
		

		if($this->route_state == 0) { return self::_reply($this->ERROR , 'Invalid API Route. Refer to Documentation. ' ) ; }
	}
	else
	{
		return  self::_reply($this->ERROR , 'Invalid API Route. Refer to Documentation. ' ) ;
	}

	}catch( Exception $e){ self::error_logger(__FUNCTION__ , $e->getMessage()); }

} 




####################################################################################################
#							 	 SYSTEM MODULES													   #				
####################################################################################################



  /*#################################################
    @Descri :  
    @Params : 
    @Return : 
  */#################################################

private function router($method)
	{

		switch( strtolower($method)  )
		{
			case 'get'    :
			case 'post'   :
			case 'put'    :
			case "delete" :
				$this->status=1;
				break;

			default :
				return self::_reply($this->ERROR , 'Unkwnown method');

		}
	}



  /*#################################################
    @Descri : 
    @Params : 
    @Return : 
  */#################################################

public  function route( $_method ,$_str , $_funct, $class_name="" )
{

# authentify clients
if($this->route_flag == false)
{
	$this->route_flag =true;
	self::init();

	self::authentify_clients();
	
}
	

	#break  if client credentials are not valid
	if( $this->is_client_credetial_valid == false)
		return ;

	# if($this->status ==0)

	  #set class if exist
	  if(trim($class_name) =="" )
	  $class_name =  $this->default_class;


	if($this->route_state == 0)
	{

	if(strtolower($_method) ==  strtolower( $this->method )  &&  self::match_route( $_str, $this->uri )  )
	{
	try
	{   # set global flag to 1;
		$this->route_state =1;

		#ckeck existence of services 
		if(!$this->is_all_client_allow)
		{
			$res = $this->api_client->is_service_exist(strtoupper($_method), $_str);

			if(  $res === false )
			 return self::_reply('300',"This service is not activated , Please contact admin");
			else
			{
				if(isset($this->api_client->get_services()[intval($res)]['class']) &&  $this->api_client->get_services()[intval($res)]['class'] != 'null')

				$class_name = $this->api_client->get_services()[intval($res)]['class']; 
			}

		} # end ckeck existence of services 
		
		

		if( self::validate_callback_function($_funct, $class_name) )
		{	
			ob_clean();
			self::prepare_route($_method ,$_str , $_funct);   // return function name and  all parameters to be sent to the function a a class
		}
		else
		{
			return false;
		}
		
		
		$res= call_user_func_array(array($class_name, $this->funct_name), $this->req) ;
		self::reply($res);

		if(isset($res['status']))
		self::access_logger	(array($res['status'] , $res['detail-code'],$res['detail-message']) );
		else
		self::access_logger	('');


	 }catch( Exception $e){ self::error_logger(__FUNCTION__ , $e->getMessage()); }

   }

  }

}


####################################################################################################
#							 	 AUXILLIARY  MODULES											   #				
####################################################################################################


  /*#################################################
    @Descri : 
    @Params : 
    @Return : 
  */#################################################

private function validate_root_uri( $uri )
{

	

	return true;
}

 /*#################################################
    @Descri : 
    @Params : 
    @Return : 
  */#################################################

 private function  validate_callback_function( $call_back, $class_name )
 {

 	try
 	{

	 		$syntax_flag =false;

	 	  	 if (!preg_match("#[^.]+\([^.]*\)$#", trim($call_back))) { $syntax_flag = true ;}  # case start with anithing and end with ) 
	 	else if ( preg_match("#[\,]{2,}#", $call_back) )  { $syntax_flag = true; } # case duplicate commas , 
	 	
	 	if( $syntax_flag == true ) { self::_reply($this->ERROR ,'The callback function -- '.$call_back.' -- has has got a syntax error  '); return false;}

	 	# check existence of function called 
	 	if(preg_match('#\(#',$call_back))
	 	$funct_name = trim( preg_split('#\(#',$call_back)[0] );
	 		
	 	if(!method_exists( $class_name , $funct_name ))
	 	{
	 		self::_reply($this->ERROR ,'The callback function -- '.$funct_name.' -- does not exit ! ' );
		    return false;
	 	}

	 	return true;

 	}catch( Exception $e){ self::error_logger(__FUNCTION__ , $e->getMessage()); }

 }

 /*#################################################
    @Descri : 
    @Params : 
    @Return : 
  */#################################################
    private function validateClient()
    {
    		
	  $ip = trim( self::get_client_ip() );


	  foreach ($this->allowed_clients_list as $client) {
	  	   if( $client->get_ip() == $ip) 
	  	   {
	  	   		$this->api_client = $client;
	  	   		return true;
	  	   		break;
	  	   }
	  }

	
		return array('300', 'Access Denied. Please contact admin '.COUNT($this->allowed_clients_list));

	

    }
   /*#################################################
    @Descri : 
    @Params : 
    @Return : 
  */#################################################
    private function validateAPI_KEY()
    {

	    $hd = getallheaders();

		  if( isset($hd['API_KEY']) )
		  {
			if( $hd['API_KEY']!= $this->api_client->get_key()   )
				return array('300', 'Invalid API_KEY Access Denied. Please contact admin ');
		  }
		  else
		  {
		  	return array('300', 'Invalid API_KEY Access Denied. Please contact admin ');
		  }
		  

		return true;

    }
   /*#################################################
    @Descri : 
    @Params : 
    @Return : 
  */#################################################
    private function validateAPI_VERSION()
    {
			$hd = getallheaders();

		# validate clients API VERSION [ if any ]

		if( $this->version !=null){
		  if( isset($hd['API_VERSION']) )
		  {
			if( strtolower(trim($hd['API_VERSION'])) !=  strtolower(trim($this->version))  )
				return array('300', 'Invalid API_VERSION Access Denied. Please contact admin ');
		  }
		  else
		  {
		  	return array('300', 'Invalid API_VERSION Access Denied. Please contact admin ');
		  }
		  
		}
		
		return true;

    }


  /*#################################################
    @Descri : 
    @Params : 
    @Return : 
  */#################################################
   private function authentify_clients()
   {
   	try
   	{
   		if(!$this->is_all_client_allow)
   		{
   			#validate clients
	$vl_res = self::validateClient() ;
	if($vl_res !== true){  return self::_reply($vl_res[0],$vl_res[1]);  }
		
	# validate clients API KEYS [ if any ]
	$vl_res = self::validateAPI_KEY() ;
	if($vl_res !== true){  return self::_reply($vl_res[0],$vl_res[1]);  }

	# validate clients API VERSION [ if any ]
	$vl_res = self::validateAPI_VERSION() ;
	if($vl_res !== true){  return self::_reply($vl_res[0],$vl_res[1]);  }
		}
	$this->is_client_credetial_valid =true;
	return true;


   	}catch( Exception $e){ self::error_logger(__FUNCTION__ , $e->getMessage()); }

   }

  /*#################################################
    @Descri : 
    @Params : 
    @Return : 
  */#################################################

private function prepare_route($_method ,$_str , $_funct)
{


	try{
	// get parameters 
		$params = self::getParams( $_str ,$this->uri);

		// activate $_funct
		$this->funct_name = preg_split('#\(#',$_funct)[0];

		# possible type of parameters :  constant , url_params ,  Request 

		$par_nb = preg_replace('#'.$this->funct_name.'\(#','', trim($_funct)) ;
		$par_nb = substr($par_nb, 0, strlen($par_nb) -1);#
		if(  trim($par_nb) =='' ){ $n = array( );}
		else{
		$n = preg_split('#,#',$par_nb);
		}

		$this->req =array();
		$call_Params =   preg_split('#,#',$par_nb);


		 for ($i = 0; $i< count($call_Params) ;$i++) {

		#request_params

		 	if( preg_match( "#[ _PUT | _GET | _POST | _DELETE ]#", $call_Params[$i]) )
		 	{
		 		$tmp =$call_Params[$i]; 
		 		$tmp = preg_replace("#\"#",'',$tmp );
		 		$tmp = preg_replace("#\'#",'',$tmp );

		 			#if(preg_match("#\[#",$tmp)== true)
		 			$tmp = preg_split("#\[#",$tmp)[1];

		 			#if(preg_match("#\]#",$tmp) == true)
		 			$tmp = preg_split("#\]#",$tmp)[0];

		 		if( isset(  $_REQUEST[$tmp]) )
		 		{
					$this->req[] =  $_REQUEST[$tmp] ;
		 		}
		 		else{ /*throw PDOException $e; */}
		 		
		 	}

		 	#url_params
		 else if (preg_match( "#params\[[0-9]+\]#", $call_Params[$i]))
		 {
		 		$tmp = $call_Params[$i];
				$par_nb = preg_replace('#params\[|\]#','',trim($tmp ) );
				$this->req[] = $params[$par_nb]; 
				#throw exception 
		 }
		 else  #constant 
		 {
		 	$tmp =$call_Params[$i]; 
		 	if(preg_match("#\"#", $tmp) || preg_match("#\'#", $tmp))
		 	{
		 		$tmp = preg_replace("#\"#",'',$tmp );
		 		$tmp = preg_replace("#\'#",'',$tmp );

		 		$this->req[] = $tmp; 
		 	} else{ /* throw Exeption */ $this->req[]=' ';}
		 }
		 #echo $req[$i]."  ";

	}
  }catch( Exception $e){ self::error_logger(__FUNCTION__ , $e->getMessage()); }


}

  /*#################################################
    @Descri : 
    @Params : 
    @Return : 
  */#################################################

private function match_route( $pattern, $url )
{
	try 
	{

	$copy_pattern = $pattern;
	$copy_uri =  $url;

	$copy_pattern = preg_replace("#\{:[a-zA-Z0-9 _@]*\}#","[a-zA-Z0-9_ @]*", $copy_pattern);

	if( preg_match('#^'.$copy_pattern.'$#', $copy_uri))
	{	
		return TRUE;
	
	}
	else
	{	
		return FALSE ;
	}

	}catch( Exception $e){ self::error_logger(__FUNCTION__ , $e->getMessage()); }
}


  /*#################################################
    @Descri : 
    @Params : 
    @Return : 
  */#################################################

private function getParams( $pattern ,$url )  // get all params included in  url
{
	
	try{

		

	$arr_pattern = preg_split('#/#', $pattern);
	$arr_uri= preg_split('#/#', $url);

	$params = array( );


	if(count($arr_pattern)  == count($arr_uri))
	{
		$l = count($arr_pattern);
		for ( $i = 0 ; $i < $l ; $i++ )
		{
		
		if( preg_match("#^\{#",$arr_pattern[ $i ]) )
		{
			$params[] = $arr_uri[ $i ];

		}

		}
	}

	return $params;
}catch( Exception $e){ self::error_logger(__FUNCTION__ , $e->getMessage()); }

}

  /*#################################################
    @Descri : 
    @Params : 
    @Return : 
  */#################################################

  public function config($arr)
  {
  	
  		foreach ($arr as $key => $value) {
  			 if(gettype($value) == 'string')
  			$value = trim(strtoupper($value));

  			switch( strtoupper( trim($key)) )
   			{

  				case "DEBUG":
  				
  					if($value == "TRUE")
  					{
  						echo "DEBUB MODE ACTIVATED\n";
						$this->is_debug =true;
  					}
  					
  					else
  					$this->is_debug =false;
  					break;

  				case "FORMAT":
  					if($value == "XML")
  					$this->format= $value;
  				break;

  				case "CLIENTS":
  					if($value == 'TRUE')
  					{
  				    require("Class/Local_db/List_api_clients.php");
  					
  					$this->allowed_clients_list = $list_api_clients ;
  					$this->is_all_client_allow  = false;

  				    }
  				break; 
  				case "API_KEYS":
  				    $value = explode(';', $value);
  					 $arr =array();
  					 foreach ($value as $key2 ) {
  					 	$arr[] = trim($key2);
  					 }
  					 if(count($arr))
  					$this->api_valid_keys = $arr;

  				break;

  				case "API_VERSION":
  					 $this->version = trim($value);
  					 
  				break;

  				default:
  				break;
  			}
  		}

  }

   /*#################################################
    @Descri : 
    @Params : 
    @Return : 
  */#################################################
    public function get_API_Documetation()
    {
    	$file_name = $this->api_doc_path;
    	if( !file_exists($file_name ) ) die("File not found");
    	
    	header("Content-Disposition: attachment; filename='api.pdf'");
		header("Content-Length: " . filesize($file_name ));
		header("Content-Type: application/octet-stream;");
		// The PDF source is in original.pdf
		readfile($file_name );
		
    }

  /*#################################################
    @Descri : 
    @Params : 
    @Return : 
  */#################################################
    public function get_API_Info()
    {
    	$msg="";

    	$msg .="
    			# Welcome to ".$this->api_name."#<br>
    			# Version        : ".$this->version."
    			# Creation Date  : ".self::get_access_time("creation")."
    			# Last Update    : ".self::get_access_time("modify")."
    			# Documentation  : "."/cmd/doc"."
    			# Author         : ".$this->api_author."
    			# Contact        : ".$this->api_contact;
    	$msg.= preg_match("#API#", strtoupper($this->api_name) ) ?"":"API";

    	return self::_reply($this->SUCCESS, $msg);
    	
    }

   /*#################################################
    @Descri : 
    @Params : 
    @Return : 
  */#################################################
    public function get_API_Log()
    {


     	try
    	{
    		date_default_timezone_set('UTC');
			$actual_date= date('Y/m/d H:i:s') ;
			$filename = "./Log/".str_replace("/", "_",explode(" ",$actual_date)[0]).".txt";

    	if (file_exists($filename)) 
		{
			if($fh= fopen($filename, "r+"))
    		{
    			$content = " Log  Report ----( ".$actual_date."  )-----  \n";
    			while( $row =fgets($fh))
    			{
    				$content .= $row;
    			}
    			return self::_reply($this->SUCCESS, strip_tags( $content) );
    			
    		}
    	} 
    	else
    	{
    		return self::_reply($this->SUCCESS, " Today's  Log is empty");
    	}
    	
    	
    	}catch(Exception $e){ echo $e;}

    	
    }

  /*#################################################
    @Descri : 
    @Params : 
    @Return : 
  */#################################################
    public function get_access_time($tag="modify")
    {
    	$filename = __FILE__;

    	if (file_exists($filename)) 
		{
    	switch(strtolower($tag) )
    	{
    		case "modify" :
    			return  date("F d Y H:i:s.", filemtime($filename));		
				break;

			default:
				return date("F d Y H:i:s.", filectime($filename));
		
    	}
    	}
    	
    }
  /*#################################################
    @Descri : 
    @Params : 
    @Return : 
  */#################################################
    private function get_client_ip()
    {
    	$ip ="";

    	if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
		    $ip = $_SERVER['HTTP_CLIENT_IP'];
		} elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
		    $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
		} else {
		    $ip = $_SERVER['REMOTE_ADDR'];
		}
		return $ip;

    }

    /*#################################################
    @Descri : 
    @Params : 
    @Return : 
  */#################################################
    public function access_logger($message="Unknown")
    {
    	include("Class/Local_db/List_month.php");
    	try
    	{
    	date_default_timezone_set('UTC');
		$actual_date= date('Y/m/d H:i:s') ;

		$date = explode(" ",$actual_date)[0];
		$time = explode(" ",$actual_date)[1];

		$year = trim( explode("/",$date)[0] );
		$month = explode("/",$date)[1];
		$month = trim( $list_month[ intval($month) -1 ]);

    	$filename = "./Log/".$year.'/'.$month.'/'.str_replace("/", "_",$date).".txt";
    	$ip = self::get_client_ip();
    	$uri = $_SERVER['REQUEST_URI'];

	    $hd = getallheaders();

    	$content   = array();
    	$content[] = $time;
    	if(isset( $hd['API_KEY']) ) 
    	$content[] = $hd['API_KEY'];

    	$content[] = $ip;
    	$content[] = $uri;
    	if(gettype($message))
    		$message = implode(" | ", $message);
    	$content[] = $message;// RESPONSE

 		File_io::appendData($filename , $content);

    	}catch(Exception $e){ echo $e;}

    }

  /*#################################################
    @Descri : 
    @Params : 
    @Return : 
  */#################################################
    public function error_logger($from , $message)
    {
      try
      {
        $time = File_io::getServerTime();
        File_io::appendData($this->error_log_path , array($time,$from ,$message ));

      } catch( Exception $e){ echo $e;}

    }

  /*#################################################
    @Descri : 
    @Params : 
    @Return : 
  */#################################################
    public function formatter($obj)
    {
  


    	switch (strtoupper($this->format)) {
    		case 'XML':
    			# code...
    			break;
    		
    		default:
    			# code...
    			break;
    	}

    	return $obj;
    }


  /*#################################################
    @Descri : 
    @Params : 
    @Return : 
  */#################################################


  private function reply($obj )
  {
  	$message_type = gettype($obj);
  	$res ="";

  	if($this->is_debug == false)
    ob_clean();
    
    if( $message_type != 'array' )
    $res =  array('status' =>$this->ERROR ,'data'=>array(),'detail-code'=>'00' ,'detail-message'=> "response is not formatted accordingly"  );
    else
    {   
    	$obj = self::formatter($obj);
    	$res =  $obj;
    }

		echo    json_encode( $res ) ;
    	return  $res ;
  }

  /*#################################################
    @Descri : api_system response 
    @Params : 
    @Return : 
  */#################################################

 private function _reply($code , $message)
 {
 	header('Content-Type: application/json');
 	$res =  array('status' =>$code,'data'=>array(),'detail-code'=>'00' ,'detail-message'=> $message  );

 	self::access_logger(array($code , '00',$message ) );
 	
    return self::reply($res);
 }


####################################################################################################
#							 	 END OF API_CORE  						     					   #				
####################################################################################################
}






?>