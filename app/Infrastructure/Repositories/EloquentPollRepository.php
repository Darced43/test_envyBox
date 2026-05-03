<?php

namespace App\Infrastructure\Repositories;

use App\Domain\Repositories\PollRepositoryInterface;
use App\Models\Poll;
use App\Models\Vote;
use Illuminate\Support\Facades\DB;

class EloquentPollRepository implements PollRepositoryInterface {
    public function createPoll(Poll $poll, array $optionsData): Poll {
        return DB::transaction(function () use ($poll, $optionsData) {
            $poll->save();
            foreach ($optionsData as $text) {
                $poll->options()->create(['text' => $text]);
            }
            return $poll->load('options');
        });
    }

    public function findByCode(string $code): ?Poll {
        return Poll::with('options')->where('short_code', $code)->first();
    }

    public function hasVoted(int $pollId, string $ip): bool {
        return Vote::where('poll_id', $pollId)->where('ip_address', $ip)->exists();
    }

    public function recordVote(int $pollId, int $optionId, string $ip): void {
        Vote::create(['poll_id' => $pollId, 'option_id' => $optionId, 'ip_address' => $ip]);
    }

    public function getResults(string $code): array {
        $poll = Poll::with(['options.votes'])->where('short_code', $code)->firstOrFail();
        return $poll->options->map(function ($option) {
            return [
                'id' => $option->id,
                'text' => $option->text,
                'votes' => $option->votes->count()
            ];
        })->toArray();
    }
}
