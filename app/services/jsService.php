<?php
	/**
	 * Project: MVC2022.
	 * Author:  InCubics
	 * Date:    02/07/2022
	 * File:    jsService.php
	 */

	namespace services;

	use lib\files\getFiles;

	class jsService  extends \stdClass
	{
		public function call()
		{
			// activities to create a string with all required js-tags from folder and/or array with online-sources

			$jsCDN  = include('../app/config/js_cdn_resources.php');
			$js ='';
			if(!empty($jsCDN))
			{
				foreach($jsCDN as $arrayResource)
				{
					if(!empty($arrayResource['comment']))
					{
						$js.='<!--'.$arrayResource['comment'].'-->';
					}
					$js.='<script src="'.$arrayResource['src'].'" ';
					if(!empty($arrayResource['integrity']))
					{
						$js.='integrity="'.$arrayResource['integrity'].'" ';
					}
					if(!empty($arrayResource['crossorigin']))
					{
						$js.='crossorigin="'.$arrayResource['crossorigin'].'" ';
					}
					$js.='></script>';
				}
			}
            $files = (new getFiles())->files('js', 'js');
            foreach($files as $file){
                $js .= '<script src="http';
                if( isset($_SERVER['HTTPS'] ) ){
                    $js .= 's';
                }
                $js.= '://'.$_SERVER['SERVER_NAME'].'/';
                if(!empty($this->config->base_path)) {
                    $js .= rtrim(ltrim($this->config->base_path, '/'), '/') . '/';
                }
                $js .= ltrim($file, './').'"></script>';
            }
			return (string) $js;

		}

	}
