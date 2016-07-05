<?php 

namespace LuckyNail\Assets;
use LuckyNail\Helper;
use LuckyNail\SimpleCache;
use MatthiasMullie\Minify;

class AssetCompressor extends AssetBox{
	protected $_oCompressor;
	protected $_oCache;
	public function __construct($sAssetType, $sBasePath = '/'){
		parent::__construct($sAssetType, $sBasePath);
	}
	protected function _reset_compressor(){
		if(strtolower($this->get_asset_type()) === 'js'){
			$this->_oCompressor = new Minify\JS();
		}
		elseif(strtolower($this->get_asset_type()) === 'css'){
			$this->_oCompressor = new Minify\CSS();
		}
	}	
	public function get_package_id($bMinified = true){
		$sIds = implode('', $this->get_asset_urls());
		if($bMinified){
			$sIds .= '.min';
		}
		return md5($sIds);
	}
	protected function _fetch_package($bMinified){
		$sAssets = implode("\r\n", $this->get_assets());
		if($bMinified){
			$this->_reset_compressor();
			$this->_oCompressor->add($sAssets);
			$sAssets = $this->_oCompressor->minify();
		}
		return $sAssets;
	}
	public function get_package($bMinified = true){
		if(!$this->_oCache){
			$sContent = $this->_fetch_package($bMinified);
		}else{
			$sPackageId = $this->get_package_id($bMinified);
            $sContent = $this->_oCache->read($sPackageId);
            if($sContent === false){
                $sContent = $this->_fetch_package($bMinified);
                $this->_oCache->write($sPackageId, $sContent);
            }
		}
		return $sContent;
	}

	public function add_cache($iCacheHours = 24, $sCachePath = false){
		if(!$sCachePath){
			$sCachePath = $this->_sBasePath.DIRECTORY_SEPARATOR.'__ac_cache_'.$this->_sKey;
		}
		$this->_oCache = new SimpleCache\Text($sCachePath, $iCacheHours);
	}
	public function create_tags($sControllerUrl, $bMerge = true){
		$aUrls = $this->get_asset_urls();

		// Erstellt Tag-Prototypen für sprintf()
        $aReplacementProtos = [
            'js' => '<script src="%s" type="text/javascript"></script>'."\r\n",
            'css' => '<link href="%s" type="text/css" rel="stylesheet" />'."\r\n",
        ];

		$sResult = '';
        // Falls die Assets zusammengefügt werden sollen, werden als Urls-Parameter gleich alle
        // Urls als Array gesetzt und der Url-Pool wird sofort geleert.
        while($aUrls){
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
			$aQueryData = ['t' => $this->_sKey, 's' => $aQueryUrls];
			$sCompressorUrl = $sControllerUrl.'?'.http_build_query($aQueryData);

	        // Ergebnis wird zusammengefügt und zurückgegeben.
	        $sResult .= sprintf($aReplacementProtos[$this->_sKey], $sCompressorUrl);
        }

        return $sResult;
	}

}
