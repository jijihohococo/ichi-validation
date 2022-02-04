<?php

namespace JiJiHoHoCoCo\IchiValidation\Command;

use Exception;
class ValidationCommand{

	private $path='app/Validators';
	private $validationCommandLine='make:validation';


	public function setPath(string $path){
		$this->path=$path;
	}

	public function getPath(){
		return $this->path;
	}

	private function getNamespace(string $defaulFolder){
		return str_replace('/', '\\', ucfirst($defaulFolder));
	}

	private function alreadyHave(string $createdFile,string $createdOption){
		echo $createdFile . " ".$createdOption." is already created".PHP_EOL;
		exit();
	}

	private function success(string $createdFile,string $createdOption){
		echo $createdFile . " ".$createdOption." is created successfully".PHP_EOL;
		exit();
	}

	private function wrongCommand(){
		echo "You type wrong command".PHP_EOL;
		exit();
	}

	private function createError(string $createdFile,string $createdOption){
		echo "You can't create ". $createdFile . " " . $createdOption.PHP_EOL;
		exit();
	}

	private function makeValidationContent(string $defaulFolder,string $createdFile){
		return "<?php

namespace ". $this->getNamespace( $defaulFolder ).";
use JiJiHoHoCoCo\IchiValidation\CustomValidator;

class ".$createdFile." extends CustomValidator{



		public function __construct(){


		}


		public function rule(){


		}


		public function showErrorMessage(){


		}

}
";
	}

	private function checkOption(string $command){
		switch ($command) {
			case $this->validationCommandLine:
			return 'Validation';
			break;
			
		}
	}


	private function checkPath(string $command){
		switch ($command) {
			case $this->validationCommandLine:
			return $this->getPath();
			break;
		}
	}

	private function checkContent(string $command,string $defaulFolder,string $createdFile){
		switch ($command) {
			case $this->validationCommandLine:
			return $this->makeModelContent($defaulFolder,$createdFile);
			break;

		}
	}



	public function run(string $dir,array $argv){

		if(count($argv)==3 && $argv[1]==$this->validationCommandLine ){
			$command=$argv[1];
			$createdOption=$this->checkOption($command);
			$defaulFolder=$this->checkPath($command);
			$baseDir=$dir.'/'.$defaulFolder;
			if(substr($argv[2], -1)=='/'){
				return $this->wrongCommand();
			}
			try {
				if(!is_dir($baseDir)){
					$createdFolder=NULL;
					$basefolder=explode('/', $defaulFolder);
					foreach($basefolder as $key => $folder){
						$createdFolder .= $key == 0 ? $dir . '/' . $folder : '/' . $folder;
						if(!is_dir($createdFolder)){
							mkdir($createdFolder);
						}
					}
				}
				$inputFile=explode('/',$argv[2]);
				$count=count($inputFile);

				if($count==1 && $inputFile[0]!==NULL && !file_exists($baseDir.'/'.$inputFile[0].'.php') ){
					$createdFile=$inputFile[0];
					fopen($baseDir.'/'.$createdFile.'.php', 'w') or die('Unable to create '.$createdOption);
						$createdFileContent=$this->checkContent($command,$defaulFolder,$createdFile);
						file_put_contents($baseDir.'/'.$createdFile.'.php', $createdFileContent,LOCK_EX);
						return $this->success($createdFile,$createdOption);
				
				}elseif($count==1 && $inputFile[0]!==NULL && file_exists($baseDir . '/'.$inputFile[0].'.php') ){
					$createdFile=$inputFile[0];
				
					return $this->alreadyHave($createdFile,$createdOption);
				
				}elseif($count>1 && file_exists($baseDir.'/'. implode('/', $inputFile) . '.php' ) ){
					$createdFile=implode('/',$inputFile);
					return $this->alreadyHave($createdFile,$createdOption);
				
				}elseif($count>1 && !file_exists($baseDir .'/'. implode('/', $inputFile) . '.php' ) ){
					$createdFile=$inputFile[$count-1];
					unset($inputFile[$count-1]);
					$currentFolder=NULL;
					$newCreatedFolder=NULL;
					foreach($inputFile as $key => $folder){
						$currentFolder .= $key == 0 ? $baseDir . '/' . $folder : '/' . $folder;
						$newCreatedFolder .= $key ==0 ? $defaulFolder . '/' . $folder : '/' . $folder;
						if(!is_dir($currentFolder)){
							mkdir($currentFolder);
						}
					}

					fopen($currentFolder.'/'.$createdFile.'.php', 'w') or die('Unable to create '.$createdOption);
						$createdFileContent=$this->checkContent($command,$newCreatedFolder,$createdFile);
						file_put_contents($currentFolder.'/'.$createdFile.'.php', $createdFileContent,LOCK_EX);
						return $this->success($createdFile,$createdOption);
				}
			} catch (Exception $e) {

				return $this->createError($createdFile,$createdOption);
				
			}

		}
	}

}