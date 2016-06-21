<?php 

namespace LuckyNail\Assets;
use MatthiasMullie\Minify;

class CssCompressor extends AssetCompressor{
	public function __construct($sPublicBasePath){
		parent::__construct('css', $sPublicBasePath, $sCachePath);
	}

	protected function _create_compressor(){
		$this->_oCompressor = new Minify\CSS();
	}

}
