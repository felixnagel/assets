<?php

namespace Core\Traits;

use LuckyNail\Assets;

trait AssetCompressor{
	protected function _ac__collect_asset_urls($sType, $sPublicPath, $aHierarchy = []){
        $aJsHierarchicFiles = \LuckyNail\Helper\Path::collect_hierarchic_files(
        	$aHierarchy, $sPublicPath
        );
		$oAssetBox = new Assets\AssetBox($sType, $sPublicPath);
       	$oAssetBox->add_assets($aJsHierarchicFiles);
		return $oAssetBox->get_asset_urls();
	}

	/**
     * Erstellt link- oder script-Tags für den Asset-Compressor. Die somit erstellten Urls 
     * leiten auf einen Controller, welcher die Assets komprimiert und ausgibt.
     * 
     * @param   string  $sType      	Art der Assets (js|css)
     * @param   array   $aUrls      	Die Urls der gewünschten Assets
     * @param   string	$sControllerUrl	Die Controller-Url 	
     * @param   boolean $bMerge     	Flag, ob die Assets gemerged werden sollen
     * @param   string  $sResult    	Die auszugebenden Tags (wird rekursiv zusammengefügt)
     * @return  string              	Siehe $sResult
     */
	protected function _ac__create_tags(
		$sType,
		$aUrls,
		$sControllerUrl = '',
		$bMerge = true,
		$sResult = ''
	){
		// Erstellt Tag-Prototypen für sprintf()
        $aReplacementProtos = [
            'js' => '<script src="%s" type="text/javascript"></script>'."\r\n",
            'css' => '<link href="%s" type="text/css" rel="stylesheet" />'."\r\n",
        ];

        // Falls die Assets zusammengefügt werden sollen, werden als Urls-Parameter gleich alle
        // Urls als Array gesetzt und der Url-Pool wird sofort geleert.
        if($bMerge){
            $aQueryUrls = $aUrls;
            $aUrls = [];
        // Falls die Assets einzeln geladen werden sollen, wird immer nur ddie erste Url übergeben.
        }else{
            $aQueryUrls = array_shift($aUrls);
        }

        /*
        // Falls Debug-Mode aktiviert ist, werden alle erzeugten Tags ausgegeben
        $aAcSettings = $this->objConfig->Application['asset_compressor'];
        if($aAcSettings['enable_debug_output']){
            Core_Debug::dump($aQueryUrls, 'Asset-Compressor|Verarbeitung der Urls:');
        }
        */
       
		// Erstellt Url
		$aQueryData = ['t' => $sType, 's' => $aQueryUrls];
		$sCompressorUrl = $sControllerUrl.'?'.http_build_query($aQueryData);

        // Ergebnis wird zusammengefügt und zurückgegeben.
        $sResultPart = sprintf($aReplacementProtos[$sType], $sCompressorUrl);
        $sResult .= $sResultPart;

        // Falls noch weitere Urls zu verarbeiten sind, führe Rekursion aus
        if($aUrls){
            return $this->_ac__create_tags($sType, $aUrls, $sControllerUrl, $bMerge, $sResult);
        }
        return $sResult;
	}

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
