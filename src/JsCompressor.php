<?php 

namespace LuckyNail\Assets;
use MatthiasMullie\Minify;

class JsCompressor extends AssetCompressor{
	public function __construct($sPublicBasePath, $sCachePath){
		parent::__construct('js', $sPublicBasePath, $sCachePath);
		$this->_oCompressor = new Minify\JS();
	}
}
