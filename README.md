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
		* [width](#width)
		* [min_width](#min_width)
		* [max_width](#max_width)
		* [height](#height)
		* [min_height](#min_height)
		* [max_height](#max_height)
		* [Using Multiple Sub-Methods](#using-multiple-sub-methods)
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

While validating data, the system will add the error message to this data in the validator object if this data is not passed the validation according to the related method.

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

### ```confirm```

To validate the "field" is same as "confirm_field" or not

```php

$validator->validate($_REQUEST,[
	'password' => 'confirmed'
]);

```

This code is validating the "password" request is same as "confirm_password" or not

### ```min```

If the data is string, it is aimed to validate the number of this data string length is greater than the declared minimum number or not.

```php

$validator->validate($_REQUEST,[
	'name' => 'min:10'
]);

```

If the data is number, it is aimed to validate this number is greater than the declared minimum number or not.

```php

$validator->validate($_REQUEST,[
	'age' => 'min:18'
]);

```

If the data is uploaded file, it is aimed to validate the size of this uploaded file is greater than the declared minimum MB number or not.

```php

$validator->validate($_REQUEST,[
	'image' => 'min:3'
]);

```

### ```max```

If the data is string, it is aimed to validate the number of this data string length is less than the declared maximum number or not.

```php

$validator->validate($_REQUEST,[
	'name' => 'max:10'
]);

```

If the data is number, it is aimed to validate this number is less than the declared maximum number or not.

```php

$validator->validate($_REQUEST,[
	'age' => 'max:18'
]);

```

If the data is uploaded file, it is aimed to validate the size of this uploaded file is less than the declared maximum MB number or not.

```php

$validator->validate($_REQUEST,[
	'image' => 'max:3'
]);

```

### ```unique```

To validate the data is exist in database's table or not

You must set PDO object firstly before using this method

```php

$validator->setPDO($pdoObject);

```

And then you can use this method.

```php

$validator->validate($_REQUEST,[
	'email' => 'unique:user_table,email_field,NULL'
]);

```

Above code is validating request 'email' is same as any values of 'email_field' (column) of 'user_table' (table) or not.

Validating the data with this way is used to check the data duplication while inserting new data into database.

You can also make this way to validate the same process.

```php

$validator->validate($_REQUEST,[
	'email' => 'unique:user_table,email_field,'.NULL
]);

```



```php

$validator->validate($_REQUEST,[	
	'email' => 'unique:user_table,email_field,'. 1
]);

```
Above code is validating request 'email' is same as value of 'email_field' (column) where the id is not 1 of 'user_table' (table) or not.

Validating the data with this way is used to check the data duplication while updating the data into database.

It is used where the primary key of the table is 'id'.

If the primary key of the table is not 'id', you must use the below code

```php

$validator->validate($_REQUEST,[
	'email' => 'unique:user_table,email_field,'. 1.',user_id'
]);

```

### ```mime```

To validate the uploaded file's extension is one of the specific file extensions or not

```php

$validator->validate($_REQUEST,[
	'image' => 'mime:png,jpg,jpeg,gif'
]);

```

### ```between```

To validate the request number is between the specific two numbers or not

```php

$validator->validate($_REQUEST,[
	'age' => 'between:18,25'
]);

```
### ```dimensions```

To validate the uploaded image is specific dimensions or not

There are sub-methods in this method.

1. ```width```
2. ```min_width```
3. ```max_width```
4. ```height```
5. ```min_height```
6. ```max_height```

#### ```width```

To validate the uploaded image's width is delcared width or not

```php

$validator->validate($_REQUEST,[
	'image' => 'dimensions:width=100' 
]);

```

#### ```min_width```

To validate the uploaded image's width is greater than the declared minimum width or not

```php

$validator->validate($_REQUEST,[
	'image' => 'dimensions:min_width=100'
]);

```

#### ```max_width```

To validate the uploaded image's width is less than the delcared maximum width or not

```php

$validator->validate($_REQUEST,[
	'image' => 'dimensions:max_width=100'
]);

```

#### ```height```

To validate the uploaded image's height is delcared width or not

```php

$validator->validate($_REQUEST,[
	'image' => 'dimensions:height=100'
]);

```

#### ```min_height```

To validate the uploaded image's height is greater than the declared minimum height or not

```php

$validator->validate($_REQUEST,[
	'image' => 'dimensions:min_height=100'
]);

```

#### ```max_height```

To validate the uploaded image's height is less than the delcared maximum height or not

```php

$validator->validate($_REQUEST,[
	'image' => 'dimensions:max_height=100'
]);

```

#### Using Multiple Sub-Methods

You can validate your image's dimensions with multiple sub-methods

```php

$validator->validate($_REQUEST,[
	'image' => 'dimensions:width=100,height=100'
]);

```

### ```image_ratio```

To validate the uploaded image width and height is same as the declared ratio

```php

$validator->validate($_REQUEST,[
	'image' => 'image_ratio:1/3'
]);

```

## Customization

### Customizing Error Message

You can customize the error message for validation

```php

$validator->validate($_REQUEST,[
	'name' => 'required',
	'email' => ['required','string','email']
],[
	'required' => 'Data is required'
]);

```

Above code is customizing the error message when the 'required' validation method is not passed.

```php

$validator->validate($_REQUEST,[
	'name' => 'required',
	'email' => 'required|string|email'
],[
	'name.required' => 'Name is required',
]);

```

Above code is customizing the error message when the 'required' validation method for 'name' request is not passed.


### Customizing Validation Method

If you want to create your own validation method, you must create the validation class.

You can create your validation class via commandline.

Firstly you need to created the file named "ichi" under your project folder and use the below code in this file

```php
#!/usr/bin/env php
<?php

require __DIR__.'/vendor/autoload.php';

use JiJiHoHoCoCo\IchiValidation\Command\ValidationCommand;


$validationCommand=new ValidationCommand;
$validationCommand->run(__DIR__,$argv);

```

And then you can create the validation class in your commandline

```php

php ichi make:validation TestValidation

```

The default file folder is "app/Validations". So after making command, the validation class you created will be in the this default file folder. If you want to change the default folder path, you can change it in your "ichi" file.


```php

$validationCommand=new ValidationCommand;
$validationCommand->setPath('new_app/Validations');
$validationCommand->run(__DIR__,$argv);

```


You must set the accepted validation rules in your created validation class.
Let's make to accept only over age 21 in this created class to validate.

```php

namespace App\Validations;
use JiJiHoHoCoCo\IchiValidation\CustomValidator;

class TestValidation extends CustomValidator{


	public function __construct(){


	}


	public function rule(){

		return $this->value>21;
	}


	public function showErrorMessage(){

		return 'Your ' .$this->attribute . ' should be over 21.';

	}


}
```

In calling your validation class

```php

use App\Validations\TestValidation;

$validator=new Validator;
$validator->validate($_REQUEST,[
	'name' => 'required|string',
	'age' => ['required',new TestValidation()]
]);
```

```php

$this->attribute

```

It is the validation data field name. For the example, it is 'age'.

```php

$this->value

```

It is the data value. For the example, it is the value of 'age' request.

You can pass other values in the constructor.
