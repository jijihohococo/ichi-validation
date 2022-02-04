# Ichi Validation

<p>Ichi Validation is the fast and secure PHP validation library.</p>

## License

This package is Open Source According to [MIT license](LICENSE.md)

## Table of Content

* [Installation](#installation)
* [Testing](#testing)
* [Validation Methods](#validation-methods)
	* [required](#required)
	* [integer](#integer)
	* [string](#string)
	* [bool](#bool)
	* [double](#double)
	* [array](#array)
	* [email](#email)
	* [file](#file)
	* [confirm](#confirm)
	* [min](#min)
	* [max](#max)
	* [unique](#unique)
	* [mime](#mime)
	* [between](#between)
	* [dimensions](#dimensions)
	* [image_ratio](#image_ratio)
* [Customization](#customization)
	* [Customizing Error Message](#customizing-error-message)
	* [Customizing Validation Method](#customizing-validation-method)


## Installation


```php

composer require jijihohococo/ichi-validation

```

## Testing

```txt

your_project/vendor/jijihohococo/ichi-validation > php test/index.php 

```

## Using

You can validate the input data with <b>JiJiHoHoCoCo\IchiValidation\Validator</b>.

For example, let's make the request which have 'name' , 'age' and 'email'.

We want to validate that 'name' is not null, 'age' must be integer and 'email' must be not null and avialable email format string

```php

use JiJiHoHoCoCo\IchiValidation\Validator;

$validator=new Validator();
$boolResult=$validator->validate($_REQUEST,[
	'name' => 'required' ,
	'age' => 'required|integer' ,
	'email' => ['required','email']
]);

// To get error messages in array if the boolResult is FALSE //

$errorMessages=$boolResult==FALSE ? $validator->getErrors() : []; 

```

<b>If you want to validate the data with multiple methods, you can separate methods by adding ```| ``` by adding those methods in the array.</b>



## Validation Methods

### ```required```