<?php

namespace App\Application\Strategies;

interface CodeGenerationStrategyInterface {
    public function generate(): string;
}