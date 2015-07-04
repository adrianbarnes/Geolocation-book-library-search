<?php

 class API_Client
    {
    	private $ip;
      //private $reference_code;
    	private $key                       = null;
    	private $api_version               = 'v1';
    	private $client_name               = "Unknown";
    	private $client_email              = array();
    	private $client_contact            = array();
    	private $client_physical_address   = array();
        private $list_of_services          = array();
        private $customized_class          = null;
        private $status                    = false;
        private $api_doc_ref               = null;


		/*#################################################
    	  @Descri :  
    	  @Params : 
   	      @Return : 
  		*/#################################################

    	public function __construct( $_client_name,  $_client_ip, $_client_key=null , $_api_version='v1'  )
    	{
    		#initialize members 

    		$this->client_name = $_client_name;
    		$this->ip          = $_client_ip;
    		$this->key         = $_client_key;
    		$this->api_version = $_api_version;
    	}


		/*#################################################
    	  @Descri :  
    	  @Params : 
   	      @Return : 
  		*/#################################################
    	public function set_services($services )
    	{
    		$this->list_of_services = $services;
    	}

       /*#################################################
    	  @Descri :  
    	  @Params : 
   	      @Return : 
  		*/#################################################
    	public function add_service($method ,$route,$class=null)
    	{
    		$route = trim( $route );

    		if(!is_null($route) )
    		$this->list_of_services[] = array('method' => $method, 'route'=>$route,'class'=>$class);
    	}


        /*#################################################
    	  @Descri :  
    	  @Params : 
   	      @Return : 
  		*/#################################################
    	public function set_customized_class( $_customized_class )
    	{

    		$this->customized_class = $_customized_class;
    	}

    	/*#################################################
    	  @Descri :  
    	  @Params : 
   	      @Return : 
  		*/#################################################
    	public function set_client_api_doc_ref( $_api_doc_ref )
    	{

    		$this->api_doc_ref = $_api_doc_ref;
    	}
    	

        /*#################################################
    	  @Descri :  
    	  @Params : 
   	      @Return : 
  		*/#################################################
    	public function set_client_info($_msisdns, $_emails, $_client_physical_address )
    	{
    		$this->client_contact           = $msisdns;
    		$this->client_email             = $_emails;
    		$this->client_physical_address  = $_client_physical_address;

    	}
    	/*#################################################
    	  @Descri :  
    	  @Params : 
   	      @Return : 
  		*/#################################################

    	public function get_ip()
    	{
    		return trim($this->ip);
    	}

    	/*#################################################
    	  @Descri :  
    	  @Params : 
   	      @Return : 
  		*/#################################################

    	public function get_key()
    	{
    		return trim( $this->key ) ;
    	}

    	/*#################################################
    	  @Descri :  
    	  @Params : 
   	      @Return : 
  		*/#################################################

    	public function get_version()
    	{
    		return trim($this->api_version);
    	}


		/*#################################################
    	  @Descri :  
    	  @Params : 
   	      @Return : 
  		*/#################################################

    	public function get_services()
    	{
    		return $this->list_of_services;
    	}

		/*#################################################
    	  @Descri :  
    	  @Params : 
   	      @Return : 
  		*/#################################################

    	public function get_customized_class(  )
    	{
    		return $this->customized_class;
    	}


    	/*#################################################
    	  @Descri :  
    	  @Params : 
   	      @Return : 
  		*/#################################################
    	public function get_client_api_doc_ref( )
    	{

    		return $this->api_doc_ref ;
    	}
    	

		/*#################################################
    	  @Descri :  
    	  @Params : 
   	      @Return : 
  		*/#################################################

    	public function get_client_info()
    	{
    		$contact = implode('/', $this->client_contact );
    		$emails  = implode('/', $this->client_email);
    		$address = $this->client_physical_address;

    		$data = array('msisdns'=>$contact,
    					  'emails' =>$emails,
    					  'address'=>$address );

    		return $data;
    	}


		/*#################################################
    	  @Descri :  
    	  @Params : 
   	      @Return : 
  		*/#################################################

    	public function is_service_exist($method, $route)
    	{
    		$method = strtoupper( trim($method) );
    		$route  = trim($route);

    		
    		for ($i=0; $i<count( $this->list_of_services ) ; $i++) {
    			if( $this->list_of_services[$i]['method'] == $method  &&  $this->list_of_services[$i]['route'] == $route)
    				return $i;
    			
    		}
    		return false;
    	}

		/*#################################################
    	  @Descri :  
    	  @Params : 
   	      @Return : 
  		*/#################################################

    	public function activate()
    	{
    		$this->status = true;
    	}

		/*#################################################
    	  @Descri :  
    	  @Params : 
   	      @Return : 
  		*/#################################################

    	public function desactivate()
    	{
    		$this->status = false;
    	}

       

    }


?>