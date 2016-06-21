<?php 

namespace LuckyNail\Assets;
use MatthiasMullie\Minify;

class JsCompressor extends AssetCompressor{
	public function __construct($sPublicBasePath){
		parent::__construct('js', $sPublicBasePath, $sCachePath);
	}

	protected function _create_compressor(){
		$this->_oCompressor = new Minify\JS();
	}
}
