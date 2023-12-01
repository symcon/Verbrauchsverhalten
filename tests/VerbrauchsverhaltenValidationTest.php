<?php

declare(strict_types=1);
include_once __DIR__ . '/stubs/Validator.php';
class VerbrauchsverhaltenValidationTest extends TestCaseSymconValidation
{
    public function testValidateVerbrauchsverhalten(): void
    {
        $this->validateLibrary(__DIR__ . '/..');
    }
    public function testValidateConsumptionBehaviourModule(): void
    {
        $this->validateModule(__DIR__ . '/../ConsumptionBehaviour');
    }
}