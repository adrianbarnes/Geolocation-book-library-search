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

 	private  $univ_name= "GTUC";

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
 			

 			//json decode the category 
 			@$category_obj = json_decode($category);
 			
 			
 			//return self::reply(Status::$SUCCESS, array($category_obj)); # Keyword is empty

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
 				 $criteria .="(";
 				foreach ($keywords as $keyword) {

 					 # with or without any specified categories 
 					 $criteria .=" books.title LIKE '%$keyword%' or author.Name LIKE '%$keyword%' or publisher.publisher LIKE '%$keyword%' 
								   or books.category LIKE '%$keyword%' or books.isbn LIKE '%$keyword%' or books.edition LIKE '%$keyword%' ";

					if($i != $num_of_keywords-1 )
						$criteria .=" or ";
					$i =$i+1;
 				}
 				$criteria .=")";

 				# with  specified categories 
 				if(count($category_obj)>0)
 				{
 					$i =0;
 					$num_of_keywords = count($category_obj);
 					$criteria .= " and (";
 					foreach ($category_obj as $category) {
							$criteria .=" books.category = '$category' ";
							 
						# appending or 
						if($i != $num_of_keywords-1 )
							$criteria .=" or ";
						$i = $i + 1;
						}
					$criteria .= " )";

 				}

 				$query = "SELECT  books.category as category, books.title, author.Name as author , publisher.publisher as publisher  , books.isbn as isbn , books.edition as edition
						  FROM books
						  JOIN author JOIN publisher
						  ON books.auth_ID = author.auth_ID  and books.pub_ID = publisher.pub_ID
						  Where $criteria
						  ORDER BY books.title";


			}
			else{ # Exact 

				 # with  or without any specified categories 
 				$criteria .="( books.title = '$keyword' or author.Name = '$keyword' or publisher.publisher = '$keyword' 
							  or books.category = '$keyword' or books.isbn = '$keyword' or books.edition = '$keyword' )";
					
				if( count($category_obj) >0 ){ # with  specified categories 
					$i =0;
 					$num_of_keywords = count($category_obj);
 					$criteria .= " and (";
 					foreach ($category_obj as $category) {
							$criteria .=" books.category = '$category' ";
							 
						# appending or 
						if($i != $num_of_keywords-1 )
							$criteria .=" or ";
						$i = $i + 1;
						}
					$criteria .= " )";
				}

				$query = "SELECT   books.category as category, books.title, author.Name as author , publisher.publisher as publisher  , books.isbn as isbn , books.edition as edition
				FROM books
				JOIN author JOIN publisher
				ON books.auth_ID = author.auth_ID  and books.pub_ID = publisher.pub_ID
				Where  $criteria 
				ORDER BY books.title";
			}

			//return self::reply(Status::$SUCCESS, $query);
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
 						$ressource->university = $this->univ_name;
 					}

 					//reorder by relevence
 					//$ressources = self::reorderByReverence($ressources , $keyword);
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
	

 

 	/**
	*
	*@param  ressouces : array  
	*@return  array	
	*/
 	private static function reorderByReverence($ressources , $keyword){

 		$result = array();

 		$keywords = explode(" ", $keyword);
 		$used_index = array();
 		$count = count($keywords);
 		$formedword =  $keyword;
 		$p =0;
 		$q =1;



 		while( $formedword != "")
 		{
 			$p =0;
	 		foreach ($ressources as $ressource) {
	 			if( array_search($p, $used_index) == -1 )
	 			{
	 				if( preg_match("#".$formedword."#", $ressource->title) )
	 				{
	 					$result[] = $ressource;
	 					$used_index[] = $p;
	 				}
	 				
	 				$p++;
	 			}
	 			
	 		}

	 		//formed word 
	 		$formedword = "";
	 		for( $i = 0 ; $i<$count-$q; $i++)
	 		{
	 			$formedword .= $keywords[$i];
	 		}
	 		$q++;

 		}


 		foreach ($ressources as $ressource) {
 			$p =0;
	 		if( array_search($p, $used_index) == -1 )
	 		{
	 			if( preg_match("#$formedword#", $ressource->title))
	 			{
	 				$result[] = $ressource;
	 				$used_index[] = $p;
	 			}
	 			
	 			$p++;
	 		}
	 	}

 		return $result;
 	}
	

 




}



?>