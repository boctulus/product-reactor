<?php

/*
	@author boctulus
*/

namespace reactor\libs;

class Files
{
	static function dump($object, $filename = 'log.txt', $append = false){
		$path = __DIR__ . '/../logs/'. $filename; 

		if ($append){
			file_put_contents($path, var_export($object,  true), FILE_APPEND);
		} else {
			file_put_contents($path, var_export($object,  true));
		}		
	}

}




