<?php
	/**
	 * Project: MVC2022.
	 * Author:  InCubics
	 * Date:    28/06/2022
	 * File:    lib\files\getFiles.php
	 */
	
	namespace lib\files;
	
	class getFiles
	{
		public function files($path ='/', $ext = null)
		{
			if( is_dir('./'.$path) && is_string($ext))
			{
				return (array) glob('./'.$path.'/*.{'.$ext.'}',GLOB_BRACE);
			}
			return [];
		}
	}
?>
