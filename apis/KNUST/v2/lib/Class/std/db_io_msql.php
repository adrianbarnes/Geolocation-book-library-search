<?php
/*###################################################################################################
	
	## 	Standard DB Library 
	## 	Made 29/10/2014 by  ...
	##  v1.0

*###################################################################################################*/


####################################################################################################
#                                              DB CON                                              #
####################################################################################################


class DB_io_msql {


	private $host;
	private $db_name;
	private $db_username;
	private $db_password;
	private $dbh = null;
  private $db_type="mysql";
	private static $instance = null;




	/*#################################################
    	  @Descri :  Contructor of DBCON Class 
    	  @Params :  Hostname , Name of databasse, 
                   Username and Password.
   	    @Return :  JSON for status and message of the 
                   response. 
   */#################################################
	public function __construct( $host , $db_name, $db_username="", $db_password="",$type_of_db="mysql"){

		try {
      
     
      if( $type_of_db="mssql")
         $this->dbh = new PDO("sqlsrv:Server=".$host.";Database=".$db_name,$db_username, $db_password);
      else
        $this->dbh = new PDO("mysql:host=".$host.";dbname=".$db_name,$db_username, $db_password);  
      
      $this->dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

      $this->host        = $host ;
		  $this->db_name     = $db_name ;
      $this->db_username = $db_username;
      $this->db_password = $db_password;
      $this->db_type     = trim($type_of_db);


			 if(!$this->dbh)
       {
         self::Error_Log(__FUNCTION__,$e->getMessage());
         return self::reply("500" , "No datbase connection was initiated" );
       }
        
         return self::reply("200" , "Connection to db : ".$db_name." was successful" );

		} catch (PDOException $e) {
			
			$this->dbh = NULL;
      self::Error_Log(__FUNCTION__,$e->getMessage());
			return self::reply("500" , "error in __Select abstract ".$e->getMessage());
		}
		return $this->dbh;
	}




	/*#################################################
      @Descri :  Module that access the attrivb dbh
      @Params :  void 
   	  @Return :  PDO object 
    */#################################################

	public   function get_instance()
	{
		try {
				if(null == self::$instance)  
            self::$instance = new DB_io($this->host,$this->db_name,$this->db_username, $this->db_password,$this->db_type);
					
				return self::$instance->dbh;
					
			} catch (Exception $e) { self::Error_Log(__FUNCTION__,$e->getMessage()); }
	}

	/*#################################################
      @Descri :  Abstract module that create a table
      @Params :  Table name , columns names and description 
      			  taken from export of table 
   	  @Return :  JSON for status and message 
    */#################################################
   	  private  function createTable($table_name , $description_column )
   	  {
   	  	$obj = array();
   	  	$sql = "";
   	  	$table_name = trim(strip_tags($table_name ));
   	  	try
   	  	{

   	  		$sql = "CREATE TABLE IF NOT EXISTS `$table_name` (".$description_column.")
   	  			    ENGINE=InnoDB DEFAULT CHARSET=latin1";

   	  	} catch( PDOException $e ) {  return self::reply("500" , "error in __Create abstract ".$e->getMessage()); }#exit(" Error in ".__FUNCTION__." ####  description : ".$e);}
   	  	
   	  	try
   	  	{
   	  		if(is_null( $this->dbh ) )
			return self::reply("500" , "fail to connect to db");

			$stmt = $this->dbh->prepare( $sql );
			$res = $stmt->execute( ) ;
   		  
        	if($res)
  		   	 	return self::reply("200" , "table : ".$table_name."_tbl was  Successfully created ");
  		  	else
  		    	return self::reply("500" , "fail to create table : ".$table_name."_tbl");	


      		
    	} catch( PDOException $e ) { return self::reply("500" , "error in __Create abstract ".$e->getMessage()); }#exit(" Error in ".__FUNCTION__." ####  description : ".$e);}

   	  }



   	/*#################################################
      @Descri :  Abstract module that create a table
      @Params :  Table name , columns names and description 
      			  taken from export of table 
   	  @Return :  JSON for status and message 
    */#################################################
   	 private function dropTable( $table_name )
   	  {
   	  	$sql = "DROP TABLE $table_name";
   	  	$table_name = trim(strip_tags($table_name ));
   	  		try
   	  		{
   	  			if(is_null( $this->dbh ) )
				return self::reply("500" , "fail to connect to db");

				$stmt = $this->dbh->prepare( $sql );
      			$res = $stmt->execute( ) ;
   		  
        		if($res)
  		    		return self::reply("200" , "Successfully dropped table  :".$table_name."_tbl");
  		  		else
  		    		return self::reply("500" , "fail to drop table ".$table_name."_tbl" );	

			} catch( PDOException $e ) { return self::reply("500" , "error in __Drop abstract ".$e->getMessage()); }
   	  }

    /*#################################################
      @Descri :  Abstract module that create a table
      @Params :  Table name , columns names and description 
      			  taken from export of table 
   	  @Return :  JSON for status and message 
    */#################################################
   	  private function emptyTable( $table_name )
   	  {
   	  	$sql = "TRUNCATE TABLE $table_name";
   	  	$table_name = trim(strip_tags($table_name ));
   	  		try
   	  		{
   	  			if(is_null( $this->dbh ) )
				return self::reply("500" , "fail to connect to db");

				$stmt = $this->dbh->prepare( $sql );
      			$res = $stmt->execute( ) ;
   		  
        		if($res)
  		    		return self::reply("200" , "table ".$table_name."_tbl was successfully truncated");
  		  		else
  		    		return self::reply("500" , "fail to empty Table ".$table_name."_tbl" );	

			} catch( PDOException $e ) { return self::reply("500" , "error in __EmptyTable abstract ".$e->getMessage()); }
   	  }



    /*#################################################
      @Descri :  Abstract module that rename a table
      @Params :  Table name , columns names and description 
      			  taken from export of table 
   	  @Return :  JSON for status and message 
    */#################################################
   	  private function renameTable( $old_table_name, $new_table_name )
   	  {
   	  	$old_table_name = trim(strip_tags($old_table_name ));
   	  	$sql = "RENAME TABLE $old_table_name TO $new_table_name";
   	  	

   	  	try
   	  	{
			if(is_null( $this->dbh ) )
				return self::reply("500" , "fail to connect to db");

				$stmt = $this->dbh->prepare( $sql );
      			$res = $stmt->execute( ) ;
   		  
        		if($res)
  		    		return self::reply("200" , "table ".$old_table_name."_tbl  was  successfully renamed");
  		  		else
  		    		return self::reply("500" , "fail to rename Table ".$table_name."_tbl" );	

   	  	}catch( PDOException $e ) { return self::reply("500" , "error in __RenameTable abstract ".$e->getMessage()); }

	  }

  /*#################################################
      @Descri :  abstract module that selects elements
                 within any given table 
      @Params :  column to select, table name ,
                 condition and array of elements  to bind
   	  @Return :  JSON for status and message 
  */#################################################

	public  function select( $attribute , $table , $condition="", $data=array() )
	{

    if($this->db_type=="mssql" and trim($condition)=="WHERE 1")
      $condition=" ";


		$sql = "SELECT ".$attribute." FROM ".$table.' '.$condition;
		$obj = array();

		if(is_null( $this->dbh ) )
			return self::reply( "500" , "fail to connect to db" );

    	try
    	{
	    	$stmt = $this->dbh->prepare( $sql );

	    	if( count( $data ) != 0 ) #bind params
	        {
	          foreach ($data as $key => $value) 
	          {
	            $stmt->bindParam( $key , $value );  
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

   	private function insert($table , $keys , $values ,$data= array() )
   	{
      	if( gettype( $values)== 'array' )
   		foreach ($values as $key => $value) { 
   			if( !preg_match("#^:#", trim($value)) )
   			$values[ $key]="'".trim($values[ $key])."'";

   		}
   		else if(gettype( $values)== 'string')
   		{

   			$values = explode(",", $values);
   			foreach ($values as $key => $value) { 
   				if( !preg_match("#^:#", trim($value)) )
   				$value="'".trim($value)."'";
   			}
   		}


   		if(gettype( $keys)== 'array' )
   			$keys = implode(',', $keys);
   		if(gettype( $values)== 'array' )
   			$values = implode(',', $values);

   		$sql = "INSERT INTO ".$table."(".$keys.") VALUES(".$values." ) ";

   		echo $sql;

		  if(is_null( $this->dbh ) )
			   return self::reply("500" , "fail to connect to db");


    	try
    	{
    	  $stmt = $this->dbh->prepare( $sql );
        if( count( $data ) != 0 ) #bind params
        {
          foreach ($data as $key => $value ) 
          {
            $stmt->bindParam( $key , $value );  
          }    
        }

   		  $res = $stmt->execute( ) ;
   		  
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

	private  function delete( $table , $condition=" WHERE 1 ", $data=array() )
	{

    if($this->db_type="mssql")
      $condition=" ";


		$sql = "DELETE FROM ".$table." ".$condition;
		$obj = array();

		if(is_null( $this->dbh ) )
			return self::reply( "500" , "fail to connect to db" );

    	try
    	{
	    	$stmt = $this->dbh->prepare( $sql );

	    	if( count( $data ) != 0 ) #bind params
	        {
	          foreach ($data as $key => $value) 
	          {
	            $stmt->bindParam( $key , $value );  
	          }    
	        }

	   	$res = $stmt->execute( ) ;
   		  
        if($res)
  		    return self::reply("200" , "deleted  successfully from table");
  		  else
  		    return self::reply("500" , "fail to delete  entry from table ".$table_name);	
      		
    	} catch(PDOException $e) { return self::reply("500" , "error in __Delete abstract ".$e->getMessage()); }#exit(" Error in ".__FUNCTION__." ####  description : ".$e);}
	}


  /*#################################################
      @Descri :  abstract module that update entries
                 within any given table 
      @Params :  table name , condition and array of elements to bind
   	  @Return :  JSON for status and message 
  */#################################################

	private  function update( $table , $set, $condition=" WHERE 1 ", $data=array() )
	{

    if($this->db_type="mssql")
        $condition=" ";

		if( gettype($set) == "array")
		{
			$tmp = array();
			foreach ($set as $key => $value) 
			{
				if(!preg_match("#^:#", $value))
				$tmp[] = $key." = '".$value."'";
				else
				$tmp[] = $key." =".$value;

			}
			$set = implode(" , ", $tmp);
		}
	

		$sql = "UPDATE  ".$table." SET  ".$set." ".$condition;
		
		$obj = array();

		if(is_null( $this->dbh ) )
			return self::reply( "500" , "fail to connect to db" );

    	try
    	{
	    	$stmt = $this->dbh->prepare( $sql );

	    	if( count( $data ) != 0 ) #bind params
	        {
	          foreach ($data as $key => $value) 
	          {
	            $stmt->bindParam( $key , $value );  
	          }    
	        }

	   	$res = $stmt->execute( ) ;
   		  
        if($res)
  		    return self::reply("200" , "successfully Updated from table");
  		  else
  		    return self::reply("500" , "fail to update  entry from table ".$table_name);	
      		
    	} catch(PDOException $e) { return self::reply("500" , "error in __Update abstract ".$e->getMessage()); }#exit(" Error in ".__FUNCTION__." ####  description : ".$e);}
	}






	/*#################################################
    	  @Descri : check wether an id is unique in a 
                  given table
    	  @Params : id , and the table name 
   	    @Return : Boolean
   */#################################################

   	 private function check_unique_id($id,$table)
   	 {
   	  	$res = $this->select(" COUNT(*) as total ", $table," WHERE id= '".$id."'" );
   	 	  $res = json_decode( $res );
 	
   	 	  if( $res->status == '200' )
   	  	{
   	 		   $result =  $res->message[0]->total;
   	 		   if( $result == 0 )
   	 		     return true;
   	 		   else
   	  	     return  false;
   	 	  }
   	 	  else
   	 	  {
   	 		   return false;	
   	 	  }

   	 }


  /*#################################################
        @Descri :  
        @Params : 
          @Return : 
  */#################################################
    public static function check_file_existence_and_create( $destination )
    {
      try
      {
        if( file_exists($destination) )
        { 
          return true;
        }
        else
        {
          $path_element = explode("/", $destination);
          $res="";
          for( $i =0 ; $i < count($path_element) ; $i++ )
          { 
            if( $i == count($path_element) -1 )
              break;

            $res .= $path_element[$i].'/';
            if(!is_dir($res))
            {
              self::createDirectory($res);
            }
          }

          $fp = fopen($destination, 'w') or die("can't open file : ".$destination) ;
          fwrite($fp , "");
          fclose($fp);
          return true;

        }
      }catch(Exception $e) { var_dump( $e ) ;}
      
      return false;
    }


    /*#################################################
        @Descri :  
        @Params : 
        @Return : 
    */#################################################
    private function appendData( $destination , $message, $glue=null )
    {
      try
      {
        $glue =is_null( $glue ) && gettype($message)=="array"? "  | " : $glue;

        if(self::check_file_existence_and_create( $destination ))
        {
          $fh = fopen($destination, 'a') or die("can't open file : ".$destination); 
          if(gettype($message) == "array")
          {
            $message = implode($glue, $message);
          } 
                fwrite($fh, "\n".$message);
                fclose($fh);
        }
    
      }catch(Exception $e) { var_dump( $e ) ; }

    } 


  /*#################################################
      @Descri :  Module that format Responses in JSON 
                 Format 
      @Params :  Status code and message to be displayed
      @Return :  JSON  of status and Message 
   */#################################################

    private function Error_Log($from,$message)
    {
      
      date_default_timezone_set('UTC');
      $time = (string)date('Y/m/d H:i:s');
      $db_source="Unknown";
      try
      {
        if( gettype( $message) == "array")
        $message = implode("  | ",  $message);

        if($this->db_name)
          $db_source = $this->db_name;


        self::appendData('db_errors.txt', array( $time, "DB_NAME[ ".$db_source." ]", "MODULE[ ".$from." ]",$message) );
        echo "An Error Ocurred ";
        
      } catch( Exception $e){ var_dump( $e->getMessage() ); }

      return false;
    }



  /*#################################################
      @Descri :  Module that format Responses in JSON 
                 Format 
      @Params :  Status code and message to be displayed
      @Return :  JSON  of status and Message 
   */#################################################

    private function analyse_params($params)
    {
     
      try
      {

      } catch( Exception $e){ }

      return  json_encode( array('status' =>$code ,'message'=> $message  ) )  ;
    }

	/*#################################################
    	@Descri :  Module that format Responses in JSON 
                 Format 
    	@Params :  Status code and message to be displayed
   	  @Return :  JSON  of status and Message 
   */#################################################

  	private function reply($code , $message)
  	{
  	  #ob_clean();
  	  return  json_encode( array('status' =>$code ,'message'=> $message  ) )  ;
  	}

}



####################################################################################################
#                                         END OF DBCON                                             #
####################################################################################################

#test db 

//$db = new DB_io("localhost","vms_test","roodt","");
//$db->get_instance();
/*
$res = $db->createTable("wow56" , "`detail_id` mediumint(9) DEFAULT NULL,
							  `issued_amount` mediumint(9) DEFAULT NULL,
							  `com_amount` mediumint(9) DEFAULT NULL,
							  `bill_type` text,
							  `recurring_period` text,
							  `product_detail` text,
							  `pay_status` text"
				   );

*/
/*
$res = $db->dropTable("wow");
*/


#$res = $db->insert("callito",array("serial","pin"),array(":serial","ee3hwe") , array(":serial"=>'14') );//,array("11102","eew33wer"));
//$res = $db->insert("callito","serial , pin",":serial,'eeccll3hwe'" , array(":serial"=>'123233') );//,array("11102","eew33wer"));


#$res = $db->select("serial","callito" );//,array("11102","eew33wer"));


#$res = $db->emptyTable("callito" );//,array("11102","eew33wer"));
/*
$res = $db->renameTable("n", "callito");//,array("11102","eew33wer"));
*/


#$res = $db->update("callito", array('pin'=> 'lasdsa') ,"WHERE serial = 11102" );//,array("11102","eew33wer"));

//var_dump($res);

?>