<?php 

namespace LuckyNail\Assets;
use LuckyNail\Helper;
use MatthiasMullie\Minify;

class AssetCompressor{
	protected $_oCompressor;
	protected $_aAssets = [];
	protected $_aIds = [];
	protected $_bMerge = true;
	protected $_bMinify = true;
	protected $_sAssetType;

	public function __construct($sAssetType){
		$this->_sAssetType = $sAssetType;
	}

	protected function _reset_compressor(){
		if(strtolower($this->_sAssetType) === 'js'){
			$this->_oCompressor = new Minify\JS();
		}
		elseif(strtolower($this->_sAssetType) === 'css'){
			$this->_oCompressor = new Minify\CSS();
		}
	}	

	public function add($sAssetContent, $sAssetId = false){
		$this->_aAssets[] = $sAssetContent;
		if(!$sAssetId){
			$sAssetId = md5(uniqid(rand(), true));
		}
		$this->_aIds[] = $sAssetId;
	}

	public function get_package_id($bMerged = true, $bMinified = true){
		$aIds = $this->_aIds;
		
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
		$aAssets = $this->_aAssets;

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
