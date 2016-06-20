<?php 

namespace LuckyNail\Assets;
use LuckyNail\Helper;
use LuckyNail\SimpleCache;

abstract class Compressor{
	protected $_sCachePath;
	protected $_oCompressor;

	public function get_compressed_assets($aAssets){
		$sCacheRequestId = md5(implode('', $aAssets));
		$oCache = new SimpleCache\Text($this->_sCachePath);
		
		$sCachedPath = $oCache->get_cached_path($sCacheRequestId);
		if($sCachedPath === false){
			foreach($aAssets as $sUrl => $sPath){
				$this->_oCompressor->add($sPath);
			}
			$sResult = $this->_oCompressor->minify();
			$oCache->write($sCacheRequestId, $sResult);
		}else{
			$sResult = file_get_contents($sCachedPath);
		}
		
		return $sResult;
	}
}
