<?php 

namespace LuckyNail\Assets;
use LuckyNail\Helper;
use MatthiasMullie\Minify;

class JsCompressor extends Compressor{
	public function __construct($sCachePath){
		$this->_sCachePath = $sCachePath;
		$this->_oCompressor = new Minify\JS();
	}
}
