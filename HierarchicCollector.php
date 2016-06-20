<?php 

namespace LuckyNail\Assets;
use LuckyNail\Helper\Path;

class HierarchicCollector{
	private $_aTrunk = [];

	public function collect($aFileHierarchy = [], $sBasePath = '/'){
		$sBasePath = Path::to_path_part($sBasePath);
		$this->_parse_hierarchiacal_files_rec($aFileHierarchy, $sBasePath);
		return $this->_aTrunk;
	}

	private function _parse_hierarchiacal_files_rec($aFileHierarchyPart, $sBasePath){
		foreach($aFileHierarchyPart as $sFolderName => $mFileOrFolder){
			if(!is_array($mFileOrFolder)){
				$sFilePath = $sBasePath.DIRECTORY_SEPARATOR.$mFileOrFolder;
				if(file_exists($sFilePath)){
					$this->_aTrunk[] = $sFilePath;
				}
			}else{
				$sBasePath .= $sFolderName;
				$this->_parse_hierarchiacal_files_rec($mFileOrFolder, $sBasePath);
			}
		}
	}
}
