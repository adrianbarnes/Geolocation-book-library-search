<?php

/*###################################################################################################
	
	## 	API of GTUC Search Engine  v1
	## 	Made 24/05/2015 by   
	## 	Supervised by ...
	##  v1.0

*###################################################################################################/

/*
	Dependencies
*/
require_once( dirname(__FILE__)."/core/modules.php");
require_once( dirname(__FILE__)."/libs/auxiliary.php");


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
$is_exact_match = isset($_REQUEST['is_exact_match']) and $_REQUEST['is_exact_match'] == true?(string)trim($_REQUEST['is_exact_match']):"false";
$category       = isset($_REQUEST['category'])      ?(string)trim($_REQUEST['category'])      :null;


#Process request
$res = $mod->search($keyword,  $category ,$is_exact_match);


#expose Result
echo $res;
?>