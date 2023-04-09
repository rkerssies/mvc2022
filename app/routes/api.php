<?php
	/**
	 * Project: MVC2022.
	 * Author:  InCubics
	 * Date:    04/04/2023
	 * File:    routes\api.php
	 */
	
	
/**
*   USAGE routes and special-char's;
*	eq. methods before @-sign:  get, get-post, put-get   (separator is:  -  )
*
*
*   wildcard alphanum and - _        = *
*	wildcard characters              = %
*	wildcard numbers                 = #
*	var for action                   = $<name-var>
*	make wildcard/var optional       = <name-var|wildcard>?    append wildecard or var, eq: $bla? |  #?
*
* route definde by array: controller-name, action and optional array with middleware-classnames
 **/

	//// PREFIX on evert API-url is:  api/
	/// make url-paths without params/options/wildcards must be UNIQUE !
	return
	[
		'post@/token'                => ['api','token'],    // request a token by user-account and password :: first otherwise
		
		'post@/$model/add'		     => ['api','add',    ['oauth', 'rbac' ]], // check middleware "oAuth" & "rbac"
		'delete@/$model/delete/#'    => ['api','delete', ['oauth', 'rbac' ]], // param p2 with id, check middleware "oAuth" & "rbac"
		'put@/$model/update/#'		 => ['api','update', ['oauth', 'rbac' ]], // param p2 with id, check middleware "oAuth" & "rbac"
		'get@/$model/#'		         => ['api','find',   ['oauth', 'rbac' ]], // param p2 with id, check middleware "oAuth" & "rbac"
		'get@/$model/first'		     => ['api','first'],                      // p1 contains Model-name
		'get@/$model'			     => ['api','all'],                        // p1 contains Model-name
	];
	
	
	//////////////////////////////////////////////////////////////////////
	///// examples of routes (not implemented to be working in this framework) /////
	//		'get@/fruits'		        => ['fruit','index'],
	//      'get@/fruits/$bla/$bla2?'   => ['fruit','index',['login', 'rbac' ]], // middleware; login and rbac are called
	//		'get@/fruits/#/%/*'		    => ['fruit','index'],        // test with variety of params with wild-cards
	//		'get@/fruits/$bla/$bla2?'   => ['fruit','index',['mWare1'=> ['value1','value2']], 'mWare2' ],
	//		'get-post@/testinurl/#/%/*/$key/#?/$id?'   => ['fruit2','index'],

