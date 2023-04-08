<?php
	/**
	 * Project: MVC2022.
	 * Author:  InCubics
	 * Date:    29/06/2022
	 * File:    routes\web.php
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
	
	/// make url-paths without params/options/wildcards must be UNIQUE !

	return
	[
		'get@/articles'			        => ['article','index'],
		'get@/article/#?'		        => ['article','show'],      // param with optional number
		
		'get@/gallery'			        => ['gallery','photos'],
		

		'get-post@/fruity_add'          => ['fruit2','add'],         // usage of Eloquent-alike with bind-params and csrf
		'get-put@/fruity_update/$id'    => ['fruit2','update'],
		'get@/fruity_delete/$id'	    => ['fruit2','delete'],
		'get@/fruity'                   => ['fruit2', 'index'],      // usage of Eloquent-alike with bind-params
		
		'get-post@/fruit_add'           => ['fruit','add',   ['login' ]],
		'get-put@/fruit_update/$id'    => ['fruit','update',['login' ]],
		'get@/fruit_delete/$id'	        => ['fruit','delete',['login' ]],
		'get@/fruits2'                  => ['fruit','index2', ['login', 'rbac'=> ['value1', 'value2'] ]],
		'get@/fruits'                   => ['fruit','index', ['login', 'rbac' ]], // middleware; login and rbac are called
		
		'get@/user'	                    => ['user','show', ['login']],
		'get-post@/login'	            => ['login','show'],
		'get-post@/renew/$hash?'	    => ['login','changePass'],        // renew password show form
		'get-post@/forgot'	            => ['login','forgotPass'],        // forgot password
		
		'get@/'					        => ['article','index'],     // last option and request on root domain-url
		
	];
	
	
	//////////////////////////////////////////////////////////////////////
	///// examples of routes (not implemented to be working in this framework) /////
	//		'get@/fruits'		        => ['fruit','index'],
	//      'get@/fruits/$bla/$bla2?'   => ['fruit','index',['login', 'rbac' ]], // middleware; login and rbac are called
	//		'get@/fruits/#/%/*'		    => ['fruit','index'],        // test with variety of params with wild-cards
	//		'get@/fruits/$bla/$bla2?'   => ['fruit','index',['mWare1'=> ['value1','value2']], 'mWare2' ],
	//		'get-post@/testinurl/#/%/*/$key/#?/$id?'   => ['fruit2','index'],

