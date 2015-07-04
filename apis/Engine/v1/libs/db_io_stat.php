<?php


/*###################################################################################################
	
	## 	Standard DB Library 
	## 	Made 29/10/2014 by   ..
	## 	Supervised by Christish
	##  v1.0

*###################################################################################################*/



####################################################################################################
#                                              DB CON                                              #
####################################################################################################


class DB_io {




  /*#################################################
      @Descri :  abstract module that selects elements
                 within any given table 
      @Params :  column to select, table name ,
                 condition and array of elements  to bind
   	  @Return :  JSON for status and message 
  */#################################################

	public  static function select( $instance, $sql , $data=array() )
	{

	  # object to get result of query  
		$obj = array();

		if(is_null( $instance ) )
			return self::reply( "500" , "fail to connect to db" );

    	try
    	{
	    	$stmt = $instance->prepare( $sql );

	    	if( count( $data ) != 0 ) #bind params
	        {
	          foreach ($data as $key => $value) 
	          {
	            $stmt->bindParam( $key , $data[$key] );  
	          }    
	        }

	   		  if ( $stmt->execute() )
	     	  {
	        	while( $row =$stmt->fetch( PDO::FETCH_ASSOC) )
	        	$obj[] =$row;
	    	  }
	  
      		return self::reply("200" , $obj); 
      		
    	} catch(PDOException $e) { return self::reply("500" , "error in __Select abstract ".$e->getMessage()); }#exit(" Error in ".__FUNCTION__." ####  description : ".$e);}
	}




   /*#################################################
      @Descri :  Abstract module that insert elements
                 within any given table 
      @Params :  Table name , columns names , values ,
                 condition and array of elements  to bind
   	  @Return :  JSON for status and message 
   */#################################################

   	public static function insert($instance, $sql , $data=array() )
   	{
      	

   		//echo $sql;

		  if(is_null( $instance ) )
			   return self::reply("500" , "fail to connect to db");

    	try
    	{
    	  $stmt = $instance->prepare( $sql );
        if( count( $data ) != 0 ) #bind params
        {
          foreach ($data as $key => $value ) 
          {
          	
            $stmt->bindParam( $key , $data[$key] );  

          }    
        }

   		  $res = $stmt->execute() ;
   		  
        if($res)
  		    return self::reply("200" , "inserted successfully to db");
  		  else
  		    return self::reply("500" , "fail to insert to db");	
      		

    	} catch(PDOException $e) { return self::reply( "500" , "error in __Insert abstract ".$e->getMessage() ) ; }


   	}


  /*#################################################
      @Descri :  abstract module that delete entries
                 within any given table 
      @Params :  table name , condition and array of elements to bind
   	  @Return :  JSON for status and message 
  */#################################################

	public static  function delete( $instance, $sql , $data=array() )
	{
	
		$obj = array();

		if(is_null( $instance) )
			return self::reply( "500" , "fail to connect to db" );

    	try
    	{
	    	$stmt = $instance->prepare( $sql );

	    	if( count( $data ) != 0 ) #bind params
	        {
	          foreach ($data as $key => $value) 
	          {
	            $stmt->bindParam( $key , $data[$key] );  
	          }    
	        }

	   	$res = $stmt->execute( ) ;
   		  
        if($res)
  		    return self::reply("200" , "deleted  successfully from table");
  		  else
  		    return self::reply("500" , "fail to delete  entry from table ");	
      		
    	} catch(PDOException $e) { return self::reply("500" , "error in __Delete abstract ".$e->getMessage()); }#exit(" Error in ".__FUNCTION__." ####  description : ".$e);}
	}


  /*#################################################
      @Descri :  abstract module that update entries
                 within any given table 
      @Params :  table name , condition and array of elements to bind
   	  @Return :  JSON for status and message 
  */#################################################

	public  static function update( $instance, $sql , $data=array() )
	{
		
		$obj = array();

		if(is_null( $instance ) )
			return self::reply( "500" , "fail to connect to db" );

    	try
    	{
	    	$stmt = $instance->prepare( $sql );

	    	if( count( $data ) != 0 ) #bind params
	        {
	          foreach ($data as $key => $value) 
	          {
	            $stmt->bindParam( $key , $data[$key] );  
	          }    
	        }

	   	$res = $stmt->execute( ) ;
   		  
        if($res)
  		    return self::reply("200" , "successfully Updated from table");
  		  else
  		    return self::reply("500" , "fail to update  entry from table ");	
      		
    	} catch(PDOException $e) { return self::reply("500" , "error in __Update abstract ".$e->getMessage()); }
	}





	/*#################################################
    	@Descri :  Module that format Responses in JSON 
                 Format 
    	@Params :  Status code and message to be displayed
   	  @Return :  JSON  of status and Message 
   */#################################################

  	private static function reply($code , $message)
  	{
  	  #ob_clean();
  	  return  json_encode( array('status' =>$code ,'message'=> $message  ) )  ;
  	}



####################################################################################################
#                                         END OF DBCON                                             #
####################################################################################################



}



?>