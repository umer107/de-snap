<?php
/**
 * CommonService class contains all commonly used functions
 * $itemId - Primary key
 * $itemType - Type of code to be generated
 */

namespace De\Service;

class CommonService {
	
	/**
	 * This function generate Item's stock code, order number
	 */
	public static function generateStockCode($itemId, $itemType){
		try{
			$code = strlen($itemId) >= 5 ? $itemId : substr('0000'.$itemId, -5);
				
			if($itemType == 'diamond')
				$code = 'DIA'.$code;
			elseif($itemType == 'weddingring')
				$code = 'WRIN'.$code;
			elseif($itemType == 'engagementring')
				$code = 'ERIN'.$code;
			elseif($itemType == 'earring')
				$code = 'EAR'.$code;
			elseif($itemType == 'pendant')
				$code = 'PEN'.$code;
			elseif($itemType == 'chain')
				$code = 'CHA'.$code;
			elseif($itemType == 'miscellaneous')
				$code = 'MIS'.$code;
				
			return $code;
		}catch(Exception $e){
			\De\Log::logApplicationInfo ( "Caught Exception: " . $e->getMessage () . ' -- File: ' . __FILE__ . ' Line: ' . __LINE__ );
		}
	}
	
	/**
	 * Commonluused function to uoload file
	 * $file (array) = $_FILES
	 * $targetFolder (string) = Where the file willbe uloaded
	 * $config (array) = Configuration object
	 * $fileTypes (array) = ist of file extensions tobe uploaded
	 */
	
	public static function uploadFile($file, $targetFolder, $config, $fileTypes = null){
		try{
			$targetFileName = rand(999999, 9999999999).'_'.time();			
			$tempFile = $file['tmp_name'];
			
			$objFileInfo = new \SplFileInfo($file['name']);
			$ext = $objFileInfo->getExtension();
			
			$targetPath = $config['documentRoot'] . $targetFolder;
			
			$targetFile = rtrim($targetPath,'/') . '/' . $targetFileName.'.'.$ext;
			
			// Validate the file type
			if (!empty($fileTypes) && !in_array($ext, $fileTypes)) {
				return 1;
			}
			
			if(move_uploaded_file($tempFile, $targetFile))
				return $targetFileName.'.'.$ext;
			else
				return 2;
		}catch(Exception $e){
			\De\Log::logApplicationInfo ( "Caught Exception: " . $e->getMessage () . ' -- File: ' . __FILE__ . ' Line: ' . __LINE__ );
		}
	}
}