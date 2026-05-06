<?php 

namespace App\Application\Factories;

use App\Application\Strategies\CodeGenerationStrategyInterface;
use App\Models\Poll;

class PollFactory {
    public function __construct(
        private CodeGenerationStrategyInterface $strategy
    ) {}

    public function create(string $title, array $options): Poll {
        $poll = new Poll();
        $poll->title = $title;
        $poll->short_code = $this->strategy->generate();
        return $poll;
    }
}
