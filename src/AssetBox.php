<?php 

namespace LuckyNail\Assets;
use LuckyNail\Helper\Path;
use LuckyNail\Simple\Blackbox;
use LuckyNail\SimpleCache\Text;
use MatthiasMullie\Minify;

class AssetBox extends BlackBox{
	protected $_aHierarchicalAssetPaths = [];
	protected $_aHierarchicalAssetUrls;
	protected $_aGlobalAssetPaths = [];
	protected $_aGlobalAssetUrls;
	protected $_sPublicBasePath;
	protected $_sCachePath;

	public function __construct($sType, $sPublicBasePath, $sCachePath){
		parent::__construct($sType);
		$this->_sPublicBasePath = Path::to_path_part($sPublicBasePath);
		$this->_sCachePath = Path::to_path_part($sCachePath);
	}

	public function add_hierarchical_assets($aFileHierarchy, $sBasePath = '/'){
		$oCollector = new HierarchicCollector();
		$aNewHierarchicalAssetPaths = $oCollector->collect($aFileHierarchy, $sBasePath);

		$this->_aHierarchicalAssetPaths = array_merge(
			$this->_aHierarchicalAssetPaths,
			$aNewHierarchicalAssetPaths
		);
	}

	public static function add_global_asset($sType){
		self::put(__CLASS__.$sType);
	}

	private function _prepare(){
		$this->_aHierarchicalAssetUrls = [];
		foreach($this->_aHierarchicalAssetPaths as $sPath){
			$sUrl = Path::to_url_part(str_replace($this->_sPublicBasePath, '', $sPath));
			$this->_aHierarchicalAssetUrls[] = $sUrl;
		}

		$this->_aGlobalAssetUrls = [];
		foreach($this->_aGlobalAssetPaths as $sPath){
			$sUrl = Path::to_url_part(str_replace($this->_sPublicBasePath, '', $sPath));
			$this->_aGlobalAssetUrls[] = $sUrl;
		}

		return md5(
			implode('', $this->_aHierarchicalAssetUrls).implode('', $this->_aGlobalAssetUrls)
		);
	}

	public function compress(){
		$sFileId = $this->_prepare();
		$oCache = new Text($this->_sCachePath);

		$mCachedFile = $oCache->get($sFileId);
		if($mCachedFile !== false){
			return $mCachedFile;
		}

		if($sType === 'js'){
			$oCompressor = new Minify\JS();
		}
		elseif($sType === 'css'){
			$oCompressor = new Minify\CSS();
		}

		foreach($this->_aGlobalAssetPaths as $sPath){
			$oCompressor->add($sPath);
		}
		foreach($this->_aHierarchicalAssetPaths as $sPath){
			$oCompressor->add($sPath);
		}

		$sCompressedContent = $oCompressor->minify();
		
		$oCache->write($sFileId, $sCompressedContent);

		return $sCompressedContent;
	}


}
