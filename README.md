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
	* [image](#image)
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

<b>If you want to validate the data with multiple methods, you can separate methods by adding ```| ``` between the methods or putting those methods in the array.</b>


## Validation Methods

### ```required```

To validate the data is not null or not.

```php

$validator->validate($_REQUEST,[
	'name' => 'required'
]);

```

### ```integer```

To validate the data is integer or not

```php

$validator->validate($_REQUEST,[
	'age' => 'integer'
]);

```

### ```string```

To validate the data is string or not

```php

$validator->validate($_REQUEST,[
	'phone' => 'string'
])

```

### ```bool```

To validate the data is boolean or not


```php

$validator->validate($_REQUEST,[
	'married' => 'bool'
]);

```


### ```double```

To validate the data is double or not

```php

$validator->validate($_REQUEST,[
	'weight' => 'double'
]);

```

### ```array```

To validate the data is array or not

```php

$validator->validate($_REQUEST,[
	'highlights' => 'array'
]);

```

### ```email```

To validate the data is in the email format or not

```php

$validator->validate($_REQUEST,[
	'email' => 'email'
]);

```

### ```file```

To validate the ```$_FILES```'s parameter is null or not

```php

$validator->validate($_REQUEST,[
	'image' => 'file'
]);

```

### ```image```

To validate the uploaded file is image or not

```php

$validator->validate($_REQUEST,[
	'image' => 'image'
]);

```