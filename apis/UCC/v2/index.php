<?php

/*###################################################################################################
	
	## 	API to interface with the  External Libraries 
	## 	Made 24/05/2015 by   
	## 	Supervised by ...
	##  v1.0

*###################################################################################################/

/*
	METHODS
*/

include("lib/Class/modules.php");
include("lib/api_core.php");

include("lib/Class/std/auxiliary.php");



##############################################################################################

# Initialise Modules
$mod = new Modules();


	################################################################
	#		    	GET  PARAMS 								   #
	################################################################

# Validate Params
$res =aux::analyseInput( array('keyword') ,$_REQUEST) ;

if( $res!==true)
	exit(json_encode(array("code"=>"04","msg"=>$res)));

# Get Params
$keyword        = trim($_REQUEST['keyword']);
$is_exact_match = isset($_REQUEST['is_exact_match'])?(string)trim($_REQUEST['is_exact_match']):"false";
$category       = isset($_REQUEST['category'])      ?(string)trim($_REQUEST['category'])      :null;


#Process request
$res = $mod->search($keyword,  $category ,$is_exact_match);


#expose Result
echo $res;
?>