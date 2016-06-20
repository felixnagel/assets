<?php 

namespace LuckyNail\Assets;
use LuckyNail\Helper;

class AssetBox{
	protected $_aHierarchicalAssets = [];
	protected $_sType;
	protected static $_aGlobalAssets = [];
	protected static $_sPublicBasePath;

	public function __construct($sType, $sPublicBasePath){
		$this->_sType = $sType;
		self::$_aGlobalAssets[$sType] = [];
		self::$_sPublicBasePath = Helper\Path::to_abs_path($sPublicBasePath);
	}

	public function add_hierarchical_assets($aFileHierarchy){
		$oCollector = new HierarchicCollector();
		$aNewHierarchicalAssetPaths = $oCollector->collect(
			$aFileHierarchy,
			self::$_sPublicBasePath
		);

		foreach($aNewHierarchicalAssetPaths as $sPath){
			$this->_aHierarchicalAssets[] = self::_normalize_asset_path($sPath);
		}
	}

	private static function _normalize_asset_path($sInput){
		$sUrl = Helper\Path::to_url_part(str_replace(self::$_sPublicBasePath, '', $sInput));
		$sPath = self::$_sPublicBasePath.DIRECTORY_SEPARATOR.$sUrl;
		$sPathAbs = Helper\Path::to_abs_path($sPath);
		
		if($sPathAbs === false){
			throw new \Exception(
				__CLASS__.' - File cannot be read or does not exist: "'.$sPath.'"'
			);
		}

		return $sUrl;
	}

	public static function add_asset($sType, $sInput){
		self::$_aGlobalAssets[$sType][] = self::_normalize_asset_path($sInput);
	}

	public function get_assets(){
		return array_merge(self::$_aGlobalAssets[$this->_sType], $this->_aHierarchicalAssets);
	}
}
