<?php 

namespace LuckyNail\Assets;
use LuckyNail\Helper;
use MatthiasMullie\Minify;

class AssetCompressor extends AssetBox{
	protected $_oCompressor;
	protected $_bMerge = true;
	protected $_bMinify = true;
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
	public function get_package_id($bMerged = true, $bMinified = true){
		$aIds = $this->get_asset_urls();
		if($bMerged){
			$aIds = [implode('', $aIds)];
		}
		if($bMinified){
			foreach($aIds as $iKey => $sId){
				$aIds[$iKey] .= '.min';
			}
		}
		foreach($aIds as $iKey => $sId){
			$aIds[$iKey] = md5($sId);
		}
		return $bMerged ? $aIds[0] : $aIds;
	}
	public function get_package($bMerged = true, $bMinified = true){
		$aAssets = $this->get_assets();
		if($bMerged){
			$aAssets = [implode("\r\n", $aAssets)];
		}

		if($bMinified){
			foreach($aAssets as $iKey => $sAsset){
				$this->_reset_compressor();
				$this->_oCompressor->add($sAsset);
				$aAssets[$iKey] = $this->_oCompressor->minify();
			}
		}
		return $bMerged ? $aAssets[0] : $aAssets;
	}
}
