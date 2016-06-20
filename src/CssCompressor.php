<?php 

namespace LuckyNail\Assets;
use MatthiasMullie\Minify;

class CssCompressor extends Compressor{
	public function __construct($sCachePath){
		$this->_sCachePath = Helper\Path::to_abs_path($sCachePath);
		$this->_oCompressor = new Minify\CSS();
	}
}
