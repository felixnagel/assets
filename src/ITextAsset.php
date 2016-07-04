<?php 

namespace LuckyNail\Assets;

interface ITextAsset{
	public function add_assets($aAssets);
	public function get_assets();
	public function get_asset_type();
	public function get_asset_urls();
	public function get_asset_paths();
}
