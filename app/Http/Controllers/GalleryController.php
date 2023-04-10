<?php
	/**
	 * Project: MVC2022
	 * Author:  InCubics
	 * Date:    20/12/2022
	 */

	namespace Http\Controllers;
	
	use lib\files\getFiles;
	
	class GalleryController
	{
		public function photos(getFiles $oFile)
		{
			//			$oFile = new getFiles();  // alternative to make object without call objct in method-param
			$this->data     = $oFile->files('img/gallery', 'jpg,jpeg,gif,png');
			$this->title    = 'Gallery';
			$this->useView  = 'photos.show';
		}
	}
