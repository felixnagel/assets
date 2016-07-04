<?php

namespace LuckyNail\Assets;

trait ImplementationTrait{


	protected function _ac__output(
		$sType,
		$aUrls,
		$sPublicPath,
		$iCacheHours = false,
		$bMinify = true
	){
		// Asset-Compressor instanziieren und befüllen
        $oAssetCompressor = new Assets\AssetCompressor($sType, $sPublicPath);
        $oAssetCompressor->add_assets($aUrls);
        if($iCacheHours){
        	$oAssetCompressor->add_cache($iCacheHours);
        }

        $sContent = $oAssetCompressor->get_package($bMinify);

        /*
        header("Cache-Control: max-age=" . $iCacheHours*3600);
        header("Pragma: public");
		header("Last-Modified: " .	gmdate('D, d M Y H:i:s', time()) . " GMT");
		header('Expires: ' . gmdate('D, d M Y H:i:s', time() + $iCacheHours*86400) . ' GMT');
        */
        if($sType === 'js'){
            header('Content-Type: text/javascript');
        }elseif($sType === 'css'){
            header("Content-Type: text/css");
        }

        //ob_start("ob_gzhandler");
       	echo $sContent;
       	//ob_clean();
	}


    /**
     * Fügt Assets hinzu. Dies ist ein Wrapper für "LuckyNail\Assets\AssetBox::global_insert()".
     * Das Wrappen dieser Methode ermöglicht zusätzlichen Debug-Output.
     * 
     * @param   string  $sAssetType     Typ der Assets (js|css)
     * @param   mixed   $aAssets        String oder Array mit angegebenen Asset-Urls oder -Paths
     */
     /*
    protected function _add_assets($sAssetType, $aAssets){
        if(!$aAssets){
            return;
        }
        if(!is_array($aAssets)){
            $aAssets = [$aAssets];
        }
        foreach($aAssets as $sAsset){
            Assets\AssetBox::global_insert($sAssetType, $sAsset);
        }

        // Falls Debug-Mode aktiviert ist, werden alle Controller-Pfade und Methodennamen
        // ausgegeben, aus denen Assets eingebunden sind.
        $aAcSettings = $this->objConfig->Application['asset_compressor'];
        if($aAcSettings['enable_debug_output']){
            $aDebugBacktrace = debug_backtrace()[1];
            Core_Debug::dump(
                [
                    'Path:' => $aDebugBacktrace['file'],
                    'Methode' => $aDebugBacktrace['function'],
                    'Assets:' => $aAssets,
                ],
                'Asset-Compressor|Einbindung aus Backend:'
            );
        }
    }
    */	

    /*
	protected function _init_asset_compressor($oResponse){
		// holt den Base-Path für den in der Config gesetzten Public-Folder
	 	$aDsSettings = $this->objConfig->Application['directory_structure'];
        $sPublicPath = $aDsSettings['public_files']['fullpath'];
		// holt Asset-Compressor-Settings	
		$aAcSettings = $this->objConfig->Application['asset_compressor'];
		// holt die Sprache für den Request
		$sLang = $this->getParam('__LANGUAGE', 'deu');

		// #####
		// CSS #
		// #####
        // setzt CSS-Asset-Hierarchie
        $aJsFileHierarchy = [
    		'scripts' => [
    			'__global.css',
    			'__global_'.$sLang.'.css',
    			$this->_request->getModuleName() => [
    				'__global.css',
    				'__global_'.$sLang.'.css',
    				$this->_request->getControllerName() => [
    					'__global.css',
    					'__global_'.$sLang.'.css',
    					$this->_request->getActionName().'.css',
    				],
    			],
    		],
        ];
		$aCssUrls = $this->_ac__collect_asset_urls('css', $sPublicPath, $aJsFileHierarchy);	
		$sTags = $this->_ac__create_tags('css', $aCssUrls, 'ac', $aAcSettings['enable_merge']);
		$sHeader = $oResponse->getBody('HEADER');
		$sHeader = str_replace($aAcSettings['placeholder_css'], $sTags, $sHeader);
		$oResponse->setBody($sHeader, 'HEADER');
	}
	*/
}
