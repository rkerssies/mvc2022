<?php
	$optionsArray = null;
	
	
	if(!empty($argv[1]) &&substr($argv[1], 0, 2) == '--')
	{
		if(!is_file('app/lib/artibuild/options.php'))       {
			echo 'file doenst existe; app/lib/artibuild/options.php'."\n\r";
		}
		else        {
			include 'app/lib/artibuild/options.php';
		}
		$optionsArray['activity'] = options($argv[1]);
		$optionsArray['subject'] = null;
		$func = 'options';
	}
	elseif(empty($argv[1]) && !strpos(':', $argv[1]))
	{
		include('app/lib/artibuild/options.php');
		$optionsArray['activity'] =  options('--empty');
		$optionsArray['subject'] = null;
		$func = 'options';
	}
	else
	{
		$partsArtisanCommand=explode(':', $argv[1]);
		$optionsArray=['activity'=>$partsArtisanCommand[0], 'subject'=>$partsArtisanCommand[1], 'args'=>[], 'options'=>[]];
		for($i=2; $i<$argc; $i++)
		{
			if(substr($argv[$i], 0, 2)=='--')
			{
				$optionsArray['options'][]=ltrim($argv[$i], '--');
			}
			else
			{
				$optionsArray['args'][]=$argv[$i];
			}
		}
		if(!is_file('app/lib/artibuild/'.$optionsArray['activity'].'.php'))
		{
			die('cannot read file: '.$optionsArray['activity']."\n\r");
		}
		else
		{
			include('app/lib/artibuild/'.$optionsArray['activity'].'.php');
		}
		$func=$optionsArray['activity'].ucfirst($optionsArray['subject']);
		return call_user_func( $func, $optionsArray);
	}


	

	
