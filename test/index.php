<?php

require_once  __DIR__.'/../src/Validator.php';

use JiJiHoHoCoCo\IchiValidation\Validator;

$testData=[
'test_required' => NULL,
'pass_required' => 'not null',

'custom_rule_required' => NULL ,

'test_integer_one' => NULL ,
'test_integer_two' => 'one_two_three' ,
'pass_integer' => 123,


'test_string_one' => NULL ,
'test_string_two' => 123,
'pass_string' => '123',


'test_bool_one' => NULL ,
'test_bool_two' => 123,
'pass_bool' => TRUE ,


'test_double_one' => NULL ,
'test_double_two' => 123 ,
'pass_double' => 1.23,


'test_array_one' => NULL ,
'test_array_two' => '123',
'pass_array' => [1,2,3] ,


'test_email_one' => NULL ,
'test_email_two' => '123',
'pass_email' => 'test@email.com',


'test_confirm_one' => NULL ,
'test_confirm_two' => 'test',
'pass_confirm' => 'confirm',
'confirm_pass_confirm' => 'confirm',


'test_min_one' => NULL,
'test_min_two' => 1,
'pass_min' => 2 ,


'test_max_one' => NULL,
'test_max_two' => 4,
'pass_max' => 3
];

$testingRules=[
	
	'not_from_test_data' => 'required',

	'test_required' => 'required',
	'pass_required' => 'required',
	
	'custom_rule_required' => 'required',

	'test_integer_one' => 'integer',
	'test_integer_two' => 'integer',
	'pass_integer' => 'integer',

	'test_string_one' => 'string',
	'test_string_two' => 'string',
	'pass_string' => 'string',

	'test_bool_one' => 'bool',
	'test_bool_two' => 'bool',
	'pass_bool' => 'bool' ,


	'test_double_one' => 'double',
	'test_double_two' => 'double',
	'pass_double' => 'double' ,


	'test_array_one' => 'array',
	'test_array_two' => 'array',
	'pass_array' => 'array',

	'test_email_one' => 'email',
	'test_email_two' => 'email',
	'pass_email' => 'email' ,

	'test_confirm_one' => 'confirmed',
	'test_confirm_two' => 'confirmed',
	'pass_confirm' => 'confirmed',


	'test_min_one' => 'min:2',
	'test_min_two' => 'min:2',
	'pass_min' => 'min:2',


	'test_max_one' => 'max:3',
	'test_max_two' => 'max:3',
	'pass_max' => 'max:3'
];

$customRules=[
	'custom_rule_required.required' => 'Custom Required Message'
];

$validator=new Validator;
$result=$validator->validate($testData,$testingRules,$customRules);

print_r($validator->getErrors());
echo PHP_EOL;

