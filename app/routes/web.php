<?php
	/**
	 * Project: MVC2022.
	 * Author:  InCubics
	 * Date:    29/06/2022
	 * File:    routes\web.php
	 */
	
	
/**
* USAGE routes;
*	eq methods before @-sign:  get, get-post, put-get   (separator is:  -  )
*
*   wildcard alphanum and - _        = *
*	wildcard characters              = %
*	wildcard numbers                 = #
*	var for action                   = $<name-var>
*	make wildcard/var optional       = <name-var|wildcard>?    append wildecard or var, eq: $bla? |  #?
*
* route definde by array: controller-name, action and optional array with middleware-classnames
 **/
	
	return
	[
		'get@/articles'			        => ['article','index'],
		'get@/article/#?'		        => ['article','show'],      // param with optional number
		
		'get@/gallery'			        => ['gallery','photos'],
		
		'get@/fruits'                   => ['fruit','index', ['login', 'rbac' ]], // middleware; login and rbac are called
		'get@/fruits2'                  => ['fruit','index2', ['login', 'rbac'=> ['value1', 'value2'] ]],
		'get-post@/fruit_add'           => ['fruit','add',   ['login' ]],
		'get-post@/fruit_update/$id'    => ['fruit','update',['login' ]],
		'get@/fruit_delete/$id'	        => ['fruit','delete',['login' ]],
		
		'get@/fruity'                   => ['fruit2', 'index'],      // usage of Eloquent-alike
		'get-post@/fruity_add'          => ['fruit2','add'],         // usage of Eloquent-alike and csrf
		'get-post@/fruity_update/$id'   => ['fruit2','update'],
		'get@/fruity_delete/$id'	    => ['fruit2','delete'],
		
		'get@/user'	                    => ['user','show', ['login']],
		'get-post@/login'	            => ['login','show'],
		
		'get@/'					        => ['article','index'],     // last option and request on domain-url
	];


	// possible routes (not all are working);
	//		'get@/fruits'		        => ['fruit','index'],
	//      'get@/fruits/$bla/$bla2?'   => ['fruit','index',['login', 'rbac' ]], // middleware; login and rbac are called
	//		'get@/fruits/#/%/*'		    => ['fruit','index'],        // test with variety of params with wild-cards
	//		'get@/fruits/$bla/$bla2?'   => ['fruit','index',['mWare1'=> ['value1','value2']], 'mWare2' ],
