 <?php 
require_once(dirname(__FILE__).'/std/config.php') ;
require_once(dirname(__FILE__).'/std/db_io_stat.php') ;


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

 	private static $univ_name= "UCC";

	/**
	*	Methods use 
	*@param  status code : String 
	*@param  description details  : String 
	*@return  Json Object 
	*/
 	public  function search($keyword ,$category=false, $is_exact_match="false"  ){
 		try{
 			#initialise input
 			global $con;
 			$criteria = "";
 			$query    = "";
 			$category= isset($category)? trim($category) :null;


 			# Validate input 
 			$keyword = addslashes(trim($keyword));
 			if($keyword == "")
 				return self::reply(Status::$ERROR, "fatal error occured @X1, Please contact admin"); # Keyword is empty

 			if( is_null($con) )
 				return self::reply(Status::$ERROR, "fatal error occured @X1, Please contact admin"); # Error on DB 
 			
 			# Format query 

 			# Case : Exact match or not 

 			if($is_exact_match== "false"){ # not exact 
 				$keywords = explode(" ", $keyword);
 				$num_of_keywords = count($keywords);
 				$i=0;

 				foreach ($keywords as $keyword) {

 					if(!$category){ #without any specified categories 
 						$criteria .=" books.title LIKE '%$keyword%' or author.Name LIKE '%$keyword%' or publisher.publisher LIKE '%$keyword%' 
								  	  or books.category LIKE '%$keyword%' or books.isbn LIKE '%$keyword%' or books.edition LIKE '%$keyword%' ";
					}
					else{ # without  specified categories 
					 	$criteria .=" $category LIKE '%$keyword%' ";
					}

					if($i != $num_of_keywords-1 )
						$criteria .=" or ";
					$i =$i+1;
 				}
 				$query = "SELECT   books.category as category, books.title, author.Name as author , publisher.publisher as publisher  , books.isbn as isbn , books.edition as edition
						  FROM books
						  JOIN author JOIN publisher
						  ON books.auth_ID = author.auth_ID  and books.pub_ID = publisher.pub_ID
						  Where $criteria
						  ORDER BY books.title";
			}
			else{ # Exact 

				if(!$category){ #without any specified categories 
 					$criteria .=" books.title = '$keyword' or author.Name = '$keyword' or publisher.publisher = '$keyword' 
								or books.category = '$keyword' or books.isbn = '$keyword' or books.edition = '$keyword'";
					}
					else{ # without  specified categories 
					 $criteria .=" $category = '$keyword' ";
					}

				$query = "SELECT   books.category as category, books.title, author.Name as author , publisher.publisher as publisher  , books.isbn as isbn , books.edition as edition
				FROM books
				JOIN author JOIN publisher
				ON books.auth_ID = author.auth_ID  and books.pub_ID = publisher.pub_ID
				Where  $criteria 
				ORDER BY books.title";
			}


 			$res = DB_io::select($con, $query);

 			# Case : response format is corrupted 	
 			if( !($res = json_decode($res)) )
 				return self::reply(Status::$ERROR, "fatal error occured@X2, Please contact admin");

 			if( $res->status =="200" )
 			{
 				#if result  is empty 
 				if(count($res->message) == 0 )
 					return self::reply(Status::$NOTFOUND, "No result found ");
 				else 
 				{
 					$ressources = $res->message;
 					foreach ($ressources as $ressource) {
 						$ressource->university = self::$univ_name;
 					}
 					return self::reply(Status::$SUCCESS, $ressources);
 				}

 			}else
 			{
 				return self::reply(Status::$ERROR, "fatal error occured @X3, Please contact admin".$res->message); # Error on DB 
 			}


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
 	}
	

 }







?>