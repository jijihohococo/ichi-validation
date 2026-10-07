<?php

require_once __DIR__ . '/../src/Validator.php';
require_once __DIR__ . '/../src/CustomValidator.php';

use JiJiHoHoCoCo\IchiValidation\CustomValidator;
use JiJiHoHoCoCo\IchiValidation\Validator;

class TypeBasedNumberValidation extends CustomValidator
{
    private $type;
    private $check;

    public function __construct($type, $check)
    {
        $this->type = $type;
        $this->check = $check;
    }

    public function rule()
    {
        if ($this->type === $this->check) {
			$value = $this->getValue();
			$validatedInt = filter_var($value, FILTER_VALIDATE_INT);
			$test = $validatedInt !== false && (int) $validatedInt > 0;
			return $test;
		}
		return true;
    }

    public function showErrorMessage()
    {
        return $this->getAttribute()
            . ' must be a positive integer when type is '
            . $this->check;
    }
}

function assertTest($condition, $message)
{
    if (!$condition) {
        echo 'FAIL: ' . $message . PHP_EOL;
        exit(1);
    }

    echo 'PASS: ' . $message . PHP_EOL;
}


/*
 * Test 1:
 *
 * BOUGHT does not require product_id.
 *
 * The CustomValidator must still be executed when
 * product_id is missing and must receive null.
 */
$validator = new Validator();

$productIdSoldValidator = new TypeBasedNumberValidation(
    'BOUGHT',
    'SOLD'
);

$productIdRemoveValidator = new TypeBasedNumberValidation(
    'BOUGHT',
    'REMOVE'
);

$productIdAddOnValidator = new TypeBasedNumberValidation(
    'BOUGHT',
    'ADD_ON'
);

$result = $validator->validate(
    [
        'type' => 'BOUGHT',
    ],
    [
        'product_id' => [
            $productIdSoldValidator,
            $productIdRemoveValidator,
            $productIdAddOnValidator,
        ],
    ]
);

assertTest(
    $result === true,
    'BOUGHT passes when product_id is missing'
);

assertTest(
    $productIdSoldValidator->getValue() === null,
    'SOLD validator receives null for missing product_id'
);

assertTest(
    $productIdRemoveValidator->getValue() === null,
    'REMOVE validator receives null for missing product_id'
);

assertTest(
    $productIdAddOnValidator->getValue() === null,
    'ADD_ON validator receives null for missing product_id'
);

assertTest(
    empty($validator->getErrors()),
    'No validation error is returned for BOUGHT product_id'
);


/*
 * Test 2:
 *
 * ADD_ON requires product_id.
 *
 * This proves that the matching validator is still
 * executed when the field is missing.
 */
$validator = new Validator();

$productIdSoldValidator = new TypeBasedNumberValidation(
    'ADD_ON',
    'SOLD'
);

$productIdRemoveValidator = new TypeBasedNumberValidation(
    'ADD_ON',
    'REMOVE'
);

$productIdAddOnValidator = new TypeBasedNumberValidation(
    'ADD_ON',
    'ADD_ON'
);

$result = $validator->validate(
    [
        'type' => 'ADD_ON',
    ],
    [
        'product_id' => [
            $productIdSoldValidator,
            $productIdRemoveValidator,
            $productIdAddOnValidator,
        ],
    ]
);

assertTest(
    $result === false,
    'ADD_ON fails when product_id is missing'
);

assertTest(
    isset($validator->getErrors()['product_id']),
    'product_id error is returned for ADD_ON'
);


/*
 * Test 3:
 *
 * All CustomValidator instances must be executed.
 */
class FirstValidator extends CustomValidator
{
    public static $executed = false;

    public function rule()
    {
        self::$executed = true;

        return true;
    }

    public function showErrorMessage()
    {
        return 'First validator failed';
    }
}

class SecondValidator extends CustomValidator
{
    public static $executed = false;

    public function rule()
    {
        self::$executed = true;

        return true;
    }

    public function showErrorMessage()
    {
        return 'Second validator failed';
    }
}

$validator = new Validator();

$firstValidator = new FirstValidator();
$secondValidator = new SecondValidator();

$result = $validator->validate(
    [
        'product_id' => 100,
    ],
    [
        'product_id' => [
            $firstValidator,
            $secondValidator,
        ],
    ]
);

assertTest(
    $result === true,
    'Multiple CustomValidators pass'
);

assertTest(
    FirstValidator::$executed === true,
    'First CustomValidator was executed'
);

assertTest(
    SecondValidator::$executed === true,
    'Second CustomValidator was executed'
);

echo PHP_EOL;
echo 'All custom validator regression tests passed.' . PHP_EOL;
