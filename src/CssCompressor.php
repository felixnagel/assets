<?php 

namespace LuckyNail\Assets;
use MatthiasMullie\Minify;

class CssCompressor extends AssetCompressor{
	public function __construct($sPublicBasePath, $sCachePath){
		parent::__construct('css', $sPublicBasePath, $sCachePath);
		$this->_oCompressor = new Minify\CSS();
	}
}
