<?php 

namespace LuckyNail\Assets;

use LuckyNail\Simple;
use LuckyNail\Helper;

class AssetBox extends Simple\BlackBox{
	protected $_aHierarchicalAssets = [];
	protected $_sPublicBasePath;
	protected $_assetUrls = [];
	protected $_assets = [];

	public function __construct($sType, $sPublicBasePath){
		parent::__construct($sType);
		$this->_sPublicBasePath = Helper\Path::to_abs_path($sPublicBasePath);
	}

	public function add_hierarchical_assets($aFileHierarchy){
		$oCollector = new HierarchicCollector();
		$aNewHierarchicalAssetPaths = $oCollector->collect(
			$aFileHierarchy,
			$this->_sPublicBasePath
		);

		foreach($aNewHierarchicalAssetPaths as $sPath){
			$this->_aHierarchicalAssets[] = $this->_normalize_asset_path($sPath);
		}
	}

	protected function _normalize_asset_path($sInput){
		$sUrl = Helper\Path::to_url_part(str_replace($this->_sPublicBasePath, '', $sInput));
		$sPath = $this->_sPublicBasePath.DIRECTORY_SEPARATOR.$sUrl;
		$sPathAbs = Helper\Path::to_abs_path($sPath);
		
		if($sPathAbs === false){
			throw new \Exception(
				__CLASS__.' - File cannot be read or does not exist: "'.$sPath.'"'
			);
		}

		return $sUrl;
	}

	public static function add_asset($sType, $aInput){
		if(!is_array($aInput)){
			$aInput = [$aInput];
		}
		foreach($aInput as $sInput){
			self::put($sType, $sInput);
		}
	}

	public function fetch_assets(){
		$this->_assets = [];
		foreach($this->get_asset_urls() as $sUrl){
			$this->_assets[$sUrl] = file_get_contents(
				$this->_sPublicBasePath.DIRECTORY_SEPARATOR.$sUrl
			);
		}
		return $this->_assets;
	}

	public function get_asset_urls(){
		if(!$this->_assetUrls){
			$this->fetch_asset_urls();
		}
		return $this->_assetUrls;
	}
	public function get_assets(){
		if(!$this->_assets){
			$this->fetch_assets();
		}
		return $this->_assets;
	}

	public function fetch_asset_urls(){
		$aGlobalAssets = [];
		foreach($this->look() as $sUrl){
			$aGlobalAssets[] = $this->_normalize_asset_path($sUrl);
		}
		$this->_assetUrls = array_merge($aGlobalAssets, $this->_aHierarchicalAssets);
		return $this->_assetUrls;
	}
}
