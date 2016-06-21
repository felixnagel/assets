<?php 

namespace LuckyNail\Assets;
use LuckyNail\SimpleCache;

abstract class AssetCompressor extends AssetBox{
	protected $_sCachePath;
	protected $_oCompressor;

	public function __construct($sType, $sPublicBasePath, $sCachePath){
		parent::__construct($sType, $sPublicBasePath);
		$this->_sCachePath = $sCachePath;
	}

	public function get_compressed_assets(){
		$aAssetPaths = $this->get_assets();
		$sCacheRequestId = md5(implode('', $aAssetPaths));
		$oCache = new SimpleCache\Text($this->_sCachePath, 1);
		$sContent = $oCache->read($sCacheRequestId);

		if($sContent !== false){
			$sResult = $sContent;
		}else{
			foreach($aAssetPaths as $sPath){
				$this->_oCompressor->add($sPath);
			}
			$sResult = $this->_oCompressor->minify();
			$oCache->write($sCacheRequestId, $sResult);
		}
	
		return $sResult;
	}
}
