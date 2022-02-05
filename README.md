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

If the data is string, it is aimed to validate the number of this data string length is greater than the declared number or not.

```php

$validator->validate($_REQUEST,[
	'name' => 'min:10'
]);

```

If the data is number, it is aimed to validate this number is greater than the declared number or not.

```php

$validator->validate($_REQUEST,[
	'age' => 'min:18'
]);

```

If the data is uploaded file, it is aimed to validate the size of this uploaded file is greater than the declared MB number or not.

```php

$validator->validate($_REQUEST,[
	'image' => 'min:3'
]);

```

### ```max```

If the data is string, it is aimed to validate the number of this data string length is less than the declared number or not.

```php

$validator->validate($_REQUEST,[
	'name' => 'max:10'
]);

```

If the data is number, it is aimed to validate this number is less than the declared number or not.

```php

$validator->validate($_REQUEST,[
	'age' => 'max:18'
]);

```

If the data is uploaded file, it is aimed to validate the size of this uploaded file is less than the declared MB number or not.

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





