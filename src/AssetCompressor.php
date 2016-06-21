<?php 

namespace LuckyNail\Assets;

abstract class AssetCompressor extends AssetBox{
	protected $_oCompressor;

	public function __construct($sType, $sPublicBasePath){
		parent::__construct($sType, $sPublicBasePath);
	}

	public function get_compressed_assets_id($bMerged = true, $bMinified = true){
		$aUrls = $this->get_asset_urls();
		
		if($bMerged){
			$aUrls = [implode('', $aUrls)];
		}
		if($bMinified){
			foreach($aUrls as $i => $sUrl){
				$aUrl[$i] .= '.min';
			}
		}
		foreach($aUrls as $i => $sUrl){
			$aUrl[$i] = md5($sUrl);
		}

		return $bMerged ? $aUrls[0] : $aUrls;
	}


	public function get_compressed_assets($bMerged = true, $bMinified = true){
		$aAssets = $this->get_assets();

		if($bMerged){
			$aAssets = [implode("\r\n", $aAssets)];
		}
		if($bMinified){
			foreach($aAssets as $i => $sAsset){
				$this->_create_compressor();
				$this->_oCompressor->add($sAsset);
				$aAssets[$i] = $this->_oCompressor->minify();
			}
		}

		return $bMerged ? $aAssets[0] : $aAssets;
	}
}
