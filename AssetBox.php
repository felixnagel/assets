<?php 

namespace LuckyNail\Assets;
use LuckyNail\Helper;
use LuckyNail\Simple;

class AssetBox extends Simple\BlackBox{
	protected $_aHierarchicalAssetPaths = [];
	protected $_aHierarchicalAssetUrls;
	protected $_aGlobalAssetPaths = [];
	protected $_aGlobalAssetUrls;
	protected $_sPublicBasePath;

	public function __construct($sType, $sPublicBasePath){
		parent::__construct($sType);
		$this->_sPublicBasePath = Path::to_path_part($sPublicBasePath);
	}

	public function add_hierarchical_assets($aFileHierarchy, $sBasePath = '/'){
		$oCollector = new HierarchicCollector();
		$aNewHierarchicalAssetPaths = $oCollector->collect($aFileHierarchy, $sBasePath);

		$this->_aHierarchicalAssetPaths = array_merge(
			$this->_aHierarchicalAssetPaths,
			$aNewHierarchicalAssetPaths
		);
	}

	public function get_assets(){
		$this->_aHierarchicalAssetUrls = [];
		foreach($this->_aHierarchicalAssetPaths as $sPath){
			$sUrl = Path::to_url_part(str_replace($this->_sPublicBasePath, '', $sPath));
			$this->_aHierarchicalAssetUrls[] = $sUrl;
		}

		$this->_aGlobalAssetUrls = [];
		$this->_aGlobalAssetPaths = $this->look();
		foreach($this->_aGlobalAssetPaths as $sPath){
			$sUrl = Path::to_url_part(str_replace($this->_sPublicBasePath, '', $sPath));
			$this->_aGlobalAssetUrls[] = $sUrl;
		}

		return array_combine(
			array_merge($this->_aGlobalAssetUrls, $this->_aHierarchicalAssetUrls),
			array_merge($this->_aGlobalAssetPaths, $this->_aHierarchicalAssetPaths)
		);
	}
}
