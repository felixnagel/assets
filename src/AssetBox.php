<?php 

namespace LuckyNail\Assets;

use LuckyNail\Simple;
use LuckyNail\Helper;

class AssetBox extends Simple\BlackBox implements ITextAsset{
	protected $_sBasePath = '';
	protected $_aAssetUrls = [];
	protected $_aAssetPaths = [];
	protected $_aAssets = [];
	public function __construct($sAssetType, $sBasePath = '/'){
		parent::__construct($sAssetType);
		$this->set_base_path($sBasePath);
	}
	public function set_base_path($sBasePath){
		$this->_sBasePath = Helper\Path::to_abs_path($sBasePath);
	}
	public function add_assets($aAssets){
		if(!is_array($aAssets)){
			$aAssets = [$aAssets];
		}
		foreach($aAssets as $sAssetRelPath){
			$this->insert($sAssetRelPath);
		}
	}
	public function get_asset_type(){
		return $this->_sKey;
	}
	protected function _to_asset_path($sInput){
		if(strpos($sInput, $this->_sBasePath) === false){
			$sInput = $this->_sBasePath.DIRECTORY_SEPARATOR.Helper\Path::to_path_part($sInput);
		}
		$sFullPath = realpath($sInput);
		if($sFullPath === false){
			throw new \Exception(
				__CLASS__.' - File cannot be read or does not exist: "'.$sInput.'"'
			);
		}
		if(strpos($sFullPath, $this->_sBasePath) !== 0){
			throw new \Exception(
				__CLASS__.' - File path seems not to be in public path: "'.$sInput.'"'
			);
		}
		return $sFullPath;
	}
	protected function _to_asset_url($sInput){
		$sPathPart = Helper\Path::to_path_part($sInput);
		if(strpos($sPathPart, $this->_sBasePath) === 0){
			$sPathPart = str_replace($this->_sBasePath, '', $sPathPart);
		}
		return Helper\Path::to_url_part($sPathPart);
	}
	protected function fetch_asset_urls(){
		$this->_aAssetUrls = [];
		foreach($this->get() as $sInput){
			$this->_aAssetUrls[] = $this->_to_asset_url($sInput);
		}
		$this->_aAssetUrls = array_unique($this->_aAssetUrls);
	}
	public function get_asset_urls(){
		if(count($this->get()) !== count($this->_aAssetUrls)){
			$this->fetch_asset_urls();
		}
		return $this->_aAssetUrls;
	}
	protected function fetch_asset_paths(){
		$this->_aAssetPaths = [];
		foreach($this->get() as $sInput){
			$this->_aAssetPaths[] = $this->_to_asset_path($sInput);
		}
		$this->_aAssetPaths = array_unique($this->_aAssetPaths);
	}
	public function get_asset_paths(){
		if(count($this->get()) !== count($this->_aAssetPaths)){
			$this->fetch_asset_paths();
		}
		return $this->_aAssetPaths;
	}
	protected function fetch_assets(){
		$this->_aAssets = [];
		foreach($this->get_asset_paths() as $sPath){
			$sAsset = file_get_contents($sPath);

			if($this->_sKey === 'css'){
				$sPrepend = str_replace($this->_sBasePath, '', $sPath);
				$sPrepend = dirname($sPrepend).DIRECTORY_SEPARATOR;
				$sAsset = preg_replace("=(url\(['\"])\/*(.+?)(['\"]\))=i", '$1'.$sPrepend.'$2$3', $sAsset);
			}
			$this->_aAssets[$sPath] = $sAsset;
		}
	}	
	public function get_assets(){
		if(count($this->get()) !== count($this->_aAssets)){
			$this->fetch_assets();
		}
		return $this->_aAssets;
	}
}
