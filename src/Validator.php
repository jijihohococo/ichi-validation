<?php

namespace JiJiHoHoCoCo\IchiValidation;

use PDO,Exception;

class Validator{

	private $data = [];
	private $customErrorMessages = [];
	private $number;
	private $errors = [];
	private $validationMethods = [
		'required',
		'integer',
		'string',
		'bool',
		'double',
		'array',
		'email',
		'file',
		'image',
		'confirmed'
	];

	private $parameterValidationMethods = [
		'min',
		'max',
		'unique',
		'mime',
		'between',
		'dimensions',
		'image_ratio'
	];

	private $pdo;
	private $imageErrorMessage;

	public function setPDO(PDO $pdo){
		$this->pdo = $pdo;
	}

	public function getPDO(){
		return  $this->pdo;
	}

	public function setData($data){
		$this->data = $data;
	}

	public function getData(){
		return $this->data;
	}
	
	public function setErrors($errors){
		$this->errors = $errors;
	}

	public function getErrors(){
		return $this->errors;
	}

	public function checkFile(string $key){
		return !isset($_FILES[$key]);
	}

	public function checkMime(array $extensions,string $key){
		return $this->checkFile($key) || (isset($_FILES[$key]) && isset($_FILES[$key]['name']) && !in_array(pathinfo($_FILES[$key]['name'],PATHINFO_EXTENSION) , $extensions ) );
	}

	public function checkEmail($email){
		$find1 = strpos($email, '@');
		$find2 = strpos($email, '.');
		return $find1 !== false && $find2 !== false && $find2 > $find1;
	}

	public function checkImage($key){
		return $this->checkFile($key) || (isset($_FILES[$key]) && getimagesize($_FILES[$key]["tmp_name"]) == FALSE);
	}

	public function  checkRequired($key){
		return !isset($this->data[$key]) || (isset($this->data[$key]) && $this->data[$key] == null);
	}

	public function checkEmailVerify($key){
		return !isset($this->data[$key]) ||  (isset($this->data[$key]) && !$this->checkEmail($this->data[$key]));
	}

	public function checkData(string $key){
		return isset($this->data[$key]) && (is_array($this->data[$key]) || is_null($this->data[$key]) );
	}

	public function checkMin(int $number,string $key){
		$this->number = $number;
		return !isset($this->data[$key]) || 
		$this->checkData($key) ||
		(isset($this->data[$key]) && is_string($this->data[$key]) && strlen($this->data[$key])<$this->number ) ||
		(isset($this->data[$key]) && is_numeric($this->data[$key]) && $this->data[$key]<$this->number) ||
		(isset($_FILES[$key]) && convertToMb($_FILES[$key]['size']) < $this->number) ;
	}

	public function checkMax(int $number,string $key){
		$this->number = $number;
		return !isset($this->data[$key]) || 
		$this->checkData($key) ||
		(isset($this->data[$key]) && is_string($this->data[$key]) && strlen($this->data[$key])>$this->number ) ||
		(isset($this->data[$key]) && is_numeric($this->data[$key]) && $this->data[$key]>$this->number) ||
		(isset($_FILES[$key]) && convertToMb($_FILES[$key]['size']) > $this->number) ;
	}

	public function checkInteger(string $key){
		return !isset($this->data[$key]) || (isset($this->data[$key]) && !is_int($this->data[$key]));
	}

	public function checkString(string $key){
		return !isset($this->data[$key]) || (isset($this->data[$key]) && !is_string($this->data[$key]));
	}

	public function checkBoolean(string $key){
		$availableBooleans = [
			0,1,'0','1',TRUE,FALSE
		];
		return !isset($this->data[$key]) || (isset($this->data[$key]) && !in_array($this->data[$key], $availableBooleans)  );
	}

	public function checkDouble(string $key){
		return !isset($this->data[$key]) || (isset($this->data[$key]) && !is_double($this->data[$key])  );
	}

	public function checkArray(string $key){
		return !isset($this->data[$key]) || (isset($this->data[$key]) && !is_array($this->data[$key]) );
	}

	public function checkUnique(string $table,string $key,PDO $pdo){
		if(!isset($this->data[$key])){
			return TRUE;
		}
		if(isset($this->data[$key]) ){
			
			$checkTable = explode(',', $table);
			$table = $checkTable[0];
			$column = $checkTable[1];
			
			if(isset($checkTable[2]) && $checkTable[2] !== '' && $checkTable[2] !== 'NULL' ){
				$id = isset($checkTable[3]) ? $checkTable[3] : 'id';
				$statement = $pdo->prepare('SELECT  COUNT(*) FROM '.$table.'  WHERE '.$column.'  = ? AND '.$id.' <> ?');
				$statement->execute([$this->data[$key],$checkTable[2]]);
			}elseif(isset($checkTable[2]) && ($checkTable[2] == NULL || $checkTable[2] == 'NULL' ) ){
				$statement = $pdo->prepare('SELECT COUNT(*) FROM '.$table.' WHERE '.$column.' = ?');
				$statement->execute([$this->data[$key]]);
			}
			return $statement->fetchColumn()>0;
		}
		return FALSE;
	}

	public function checkBetween(int $numberOne,int $numberTwo,string $key){
		return !isset($this->data[$key]) || 
		(isset($this->data[$key]) && 
			(!is_numeric($this->data[$key])) ||
			(!($this->data[$key]>=$numberOne) || !($this->data[$key]<=$numberTwo) ) );
	}

	public function checkConfirmed(string $key){
		$confirmedField = 'confirm_'.$key;
		return !isset($this->data[$confirmedField]) ||
		!isset($this->data[$key]) ||
		(isset($this->data[$confirmedField]) && isset($this->data[$key]) &&
			$this->data[$confirmedField] !== $this->data[$key]  );
	}

	public function checkImageDimension(array $params,string $key){
		if($this->checkImage($key)){
			return TRUE;
		}
		if(isset($_FILES[$key])){
			$image = getimagesize($_FILES[$key]['tmp_name']);
			if(!isset($image[0]) || !isset($image[1]) ){
				return TRUE;
			}
			$imageWidth = $image[0];
			$imageHeight = $image[1];
			$rules = FALSE;
			$message = NULL;
			$availableMethods = ['width','min_width','max_width','height','min_height','max_height'];

			foreach ($params as $key => $subMethod) {

				$subMethodArray = explode('=', $subMethod);
				if(count($subMethodArray)!==2 || 
					(count($subMethodArray)==2 &&   
						( !in_array($subMethodArray[0],$availableMethods) || !is_numeric($subMethodArray[1]) )
					) ){
					$this->throwSystemErrorMessage();
			}
			switch ($subMethodArray[0]) {
				case 'width':
				$rules = $imageWidth !== $subMethodArray[1];
				$message = $key . "'s width is not ".$subMethodArray[1];
				break;

				case 'min_width':
				$rules = $imageWidth<$subMethodArray[1];
				$message = $key ."'s width is less than ".$subMethodArray[1];
				break;

				case 'max_width':
				$rules = $imageWidth>$subMethodArray[1];
				$message = $key ."'s width is greater than ".$subMethodArray[1];
				break;

				case 'height':
				$rules = $imageHeight !== $subMethodArray[1];
				$message = $key ."'s height is not ".$subMethodArray[1];
				break;

				case 'min_height':
				$rules = $imageHeight<$subMethodArray[1];
				$message = $key ."'s height is less than ".$subMethodArray[1];
				break;

				case 'max_height':
				$rules = $imageHeight>$subMethodArray[1];
				$message = $key ."'s height is greater than ".$subMethodArray[1];
				break;
			}
			if($rules == TRUE){
				$this->imageErrorMessage = $message;
				return TRUE;
			}
		}
	}
}



public function checkImageRatio(string $requiredRatio , string $key){
	if($this->checkImage($key)){
		return TRUE;
	}
	if(isset($_FILES[$key])){
		$image=getimagesize($_FILES[$key]['tmp_name']);
		if(!isset($image[0]) || !isset($image[1]) ){
			return TRUE;
		}
		$imageWidth = $image[0];
		$imageHeight = $image[1];

		$divisor = gmp_intval( gmp_gcd( $imageWidth, $imageHeight ) );
		$aspectRatio = $imageWidth / $divisor . '/' . $imageHeight / $divisor;
		return $aspectRatio !== $requiredRatio;
	}
}

public function getErrorMessage(string $rule,string $key,string $defaultErrorMessage){
	if(isset($this->customErrorMessages[$rule])){
		return $this->customErrorMessages[$rule];
	}
	if(isset($this->customErrorMessages[$key.'.'.$rule])){
		return $this->customErrorMessages[$key.'.'.$rule];
	}
	return $defaultErrorMessage;
}

private function throwSystemErrorMessage(){
	throw new Exception("You are making unavailable validation", 1);
}

private function checkCustomValidatior($rule){
	return (is_object($rule) && (!$rule instanceof CustomValidator));
}

private function checkCountString($countRuleString){
	return $countRuleString>2;
}

private function checkValidationMethods($countRuleString,$rule){
	return $countRuleString == 1 && !in_array($rule, $this->validationMethods);
}

private function checkParameterValidationMethods($countRuleString,$ruleString){
	return $countRuleString == 2 && !in_array($ruleString[0],$this->parameterValidationMethods);
}

private function checkMinAndMax($countRuleString,$ruleString){
	return  $countRuleString == 2 && ($ruleString[0] == 'min' || $ruleString[0]=='max' ) && !is_numeric($ruleString[1]);
}

private function checkMimeError($countRuleString,$ruleString,$countParams){
	return $countRuleString == 2 && $ruleString[0] == 'mime' && $countParams==0;
}

private function checkUniqueError($countRuleString,$ruleString,$countParams){
	return $countRuleString == 2 && $ruleString[0] == 'unique' && 
	($countParams < 2 || $countParams > 4);
}

private function checkImageDimensionError($countRuleString,$ruleString,$countParams){
	return $countRuleString == 2 && $ruleString[0] == 'dimensions' && $countParams < 1;
}

private function checkImageRatioError($countRuleString,$ruleString){
	if($countRuleString == 2 && $ruleString[0] == 'image_ratio'){
		$ratio = explode('/', $ruleString[1]);
		$count = count($ratio);
		if($count !== 2 || ($count == 2 && 
			(!is_numeric($ratio[0]) || !is_numeric($ratio[1]) ) ) ){
			return TRUE;
		}
	}
}

private function checkBetweenErrorOne($countRuleString,$ruleString,$countParams){
	return $countRuleString == 2 && $ruleString[0] == 'between' && $countParams !== 2;
}

private function checkBetweenErrorTwo($countRuleString,$ruleString,$countParams,$params){
	return $countRuleString == 2 && $ruleString[0] == 'between' && $countParams == 2 && 
	(!is_numeric($params[0]) || !is_numeric($params[1]));
}

public function validate(array $data,array $fields,array $customErrorMessages=[]){
	$pdo = $this->getPDO();
	if(empty($data)){
		throw new Exception("Please don't add empty array data", 1);
	}
	$this->setData($data);
	$this->customErrorMessages = $customErrorMessages;
	$errors = [];
	foreach($fields as $key => $field){
		if(!is_string($key)){
			throw new Exception("Please add string for the data field to validate", 1);	
		}
		if(!is_array($field) && !is_string($field) ){
			throw new Exception("You set the validation rules in string or array", 1);

		}
		$validatedRules = is_array($field) ? $field : explode('|', $field);

		foreach($validatedRules as $rule){

			$ruleString = explode(':', $rule);
			$countRuleString = count($ruleString);
			$params = $countRuleString == 2 ? explode(',',$ruleString[1]) : NULL;
			$countParams = $params !== NULL ? count($params) : 0;

			if(
				$this->checkCustomValidatior($rule) ||
				$this->checkCountString($countRuleString) ||
				$this->checkValidationMethods($countRuleString,$rule) ||
				$this->checkParameterValidationMethods($countRuleString,$ruleString)||
				$this->checkMinAndMax($countRuleString,$ruleString) ||
				$this->checkMimeError($countRuleString,$ruleString,$countParams) ||
				$this->checkUniqueError($countRuleString,$ruleString,$countParams) ||
				$this->checkBetweenErrorOne($countRuleString,$ruleString,$countParams) ||
				$this->checkBetweenErrorTwo($countRuleString,$ruleString,$countParams,$params) || 
				$this->checkImageDimensionError($countRuleString,$ruleString,$countParams) ||
				$this->checkImageRatioError($countRuleString,$ruleString)
			){
				$this->throwSystemErrorMessage();
			}

			if(is_object($rule) && ($rule instanceof CustomValidator) ){
				$rule->setAttribute($key);
				if(isset($this->data[$key])){
					$rule->setValue($this->data[$key]);
					if(!$rule->rule()){
						$errors[$key] = $rule->showErrorMessage();
					}
				}else{
					$errors[$key] = $rule->showErrorMessage();
				}
				break;
			}

			switch ($countRuleString) {
				case 1:
				switch ($rule) {
					case 'required':
					if($this->checkRequired($key)){
						$errors[$key] = $this->getErrorMessage($rule , $key , $key . ' is required');
					}
					break;

					case 'email':
					if($this->checkEmailVerify($key)){
						$errors[$key] = $this->getErrorMessage( $rule , $key , $key . ' is not valid email address');
					}
					break;

					case 'integer':
					if($this->checkInteger($key)){
						$errors[$key] = $this->getErrorMessage( $rule , $key , $key . ' is not integer');
					}
					break;

					case 'string':
					if($this->checkString($key)){
						$errors[$key] = $this->getErrorMessage( $rule , $key , $key . ' is not string');
					}
					break;


					case 'file':
					if($this->checkFile($key)){
						$errors[$key] = $this->getErrorMessage( $rule , $key , $key . ' is not a file' );
					}
					break;

					case 'image':
					if($this->checkImage($key)){
						$errors[$key] = $this->getErrorMessage( $rule , $key , $key . ' is not image');
					}
					break;

					case 'bool':
					if($this->checkBoolean($key)){
						$errors[$key] = $this->getErrorMessage( $rule , $key , $key . ' is not boolean');
					}
					break;

					case 'double':
					if($this->checkDouble($key)){
						$errors[$key] = $this->getErrorMessage( $rule , $key , $key . ' is not double');
					}
					break;

					case 'array':
					if($this->checkArray($key)){
						$errors[$key] = $this->getErrorMessage( $rule , $key , $key . ' is not array');
					}
					break;


					case 'confirmed':
					if($this->checkConfirmed($key)){
						$errors[$key] = $this->getErrorMessage( $rule , $key , 'confirm_'.  $key . ' is not same as '.$key );
					}
					break;

				}
				break;

				case 2:
				switch ($ruleString[0]) {
					case 'min':
					if($this->checkMin($ruleString[1],$key)){
						$errors[$key] = $this->getErrorMessage( $rule , $key , $key . ' has less than ' . $this->number . ' characters');
					}
					break;

					case 'max':
					if($this->checkMax($ruleString[1],$key)){
						$errors[$key] = $this->getErrorMessage( $rule , $key , $key . ' has more than ' . $this->number . ' characters');
					}
					break;

					case 'unique':
					if($this->checkUnique($ruleString[1],$key,$pdo)){
						$errors[$key] = $this->getErrorMessage( $rule , $key , $key  . '  has been already saved');	
					}
					break;

					case 'mime':
					if($this->checkMime($params,$key)){
						$errors[$key] = $this->getErrorMessage( $rule , $key , $key . ' is not '.$ruleString[1].' files' );
					}
					break;

					case 'between':
					$numberOne = $params[0];
					$numberTwo = $params[1];
					if($this->checkBetween($numberOne,$numberTwo,$key)){
						$errors[$key] = $this->getErrorMessage($rule,$key,$key . ' is not between '.$numberOne.' and '.$numberTwo);
					}
					break;

					case 'dimensions':
					if($this->checkImageDimension($params,$key)){
						$errors[$key] = $this->getErrorMessage($rule,$key,$this->imageErrorMessage);
					}
					break;

					case 'image_ratio':
					$ratios = explode('/', $ruleString[1]);
					$widthRatio = $ratios[0];
					$heightRatio = $ratios[1];
					if($this->checkImageRatio($ruleString[1],$key)){
						$errors[$key] = $this->getErrorMessage($rule,$key,$key . ' is not the '.$widthRatio.'/'.$heightRatio );
					}
					break;
				}
				break;
			}

			if(isset($errors[$key])){
				break;
			}
		}
	}

	if(empty($errors)){
		return TRUE;
	}else{
		http_response_code(401);
		$this->setErrors($errors);
		return FALSE;
	}
}
}