<?php
	/**
	 * Project: PhpStorm.
	 * Author:  InCubics
	 * Date:    29/06/2022
	 * File:    css_cdn_resources.php
	 */
	
	// all requires CSS CDN-resources
return
	[
		 [
			'comment' => 'usage of https://www.bootstrapcdn.com CDN',
			'href' => 'https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css',
//			'integrity'=>   '',
//			'crossorigin'  =>  '',
		],
		[
			'comment' => 'usage of https://www.bootstrapcdn.com/fontawesome/ CDN',
			'href' => 'https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free@6.1.1/css/fontawesome.min.css',
			'integrity'=>   'sha384-zIaWifL2YFF1qaDiAo0JFgsmasocJ/rqu7LKYH8CoBEXqGbb9eO+Xi3s6fQhgFWM',
			'crossorigin'  =>  'anonymous',
		],
		 [
			'comment' => 'usage of https://www.bootstrapcdn.com/bootstrapicons/ CDN',
			'href' => 'https://cdn.jsdelivr.net/npm/bootstrap-icons@1.8.2/font/bootstrap-icons.css',
			'integrity'=>   'sha384-eoTu3+HydHRBIjnCVwsFyCpUDZHZSFKEJD0mc3ZqSBSb6YhZzRHeiomAUWCstIWo',
			'crossorigin'  =>  'anonymous',
		],
	];
