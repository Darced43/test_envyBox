<?php

namespace App\Domain\Repositories;

use App\Models\Poll;

interface PollRepositoryInterface {
    public function createPoll(Poll $poll, array $optionsData): Poll;
    public function findByCode(string $code): ?Poll;
    public function hasVoted(int $pollId, string $ip): bool;
    public function recordVote(int $pollId, int $optionId, string $ip): void;
    public function getResults(string $code): array;
}