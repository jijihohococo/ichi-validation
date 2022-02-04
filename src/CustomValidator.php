<?php

namespace JiJiHoHoCoCo\IchiValidation;

abstract class CustomValidator{

	private $attribute;

	abstract public function rule();

	abstract public function showErrorMessage();

	public function setAttribute($attribute){
		$this->attribute=$attribute;
	}

	public function getAttribute(){
		return $this->attribute;
	}

}