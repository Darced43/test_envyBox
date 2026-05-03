<?php

namespace App\Application\Strategies;

use Illuminate\Support\Str;
use App\Models\Poll;

class RandomCodeStrategy implements CodeGenerationStrategyInterface {
    private const CHARS = '23456789ABCDEFGHJKLMNPQRSTUVWXYZ'; // без 0/O/l/I/1
    
    public function generate(): string {
        do {
            $code = Str::random(6);
            $code = preg_replace_callback('/[01OIl]/', fn() => self::CHARS[random_int(0, strlen(self::CHARS) - 1)], $code);
        } while (Poll::where('short_code', $code)->exists());
        return strtoupper(substr($code, 0, 6));
    }
}