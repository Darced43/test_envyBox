<?php 

namespace App\Application\Services;

use App\Application\Factories\PollFactory;
use App\Domain\Repositories\PollRepositoryInterface;
use App\Models\Poll;

class PollService {
    public function __construct(
        private PollRepositoryInterface $repo,
        private PollFactory $factory
    ) {}

    public function createPoll(string $title, array $options): Poll {
        $poll = $this->factory->create($title, $options);
        return $this->repo->createPoll($poll, $options);
    }

    public function getPoll(string $code): array {
        $poll = $this->repo->findByCode($code);
        abort_if(!$poll, 404, 'Опрос не найден');
        return [
            'title' => $poll->title,
            'short_code' => $poll->short_code,
            'options' => $poll->options->map(fn($o) => ['id' => $o->id, 'text' => $o->text])->toArray(),
        ];
    }

    public function vote(string $code, int $optionId, string $ip): array {
        $poll = $this->repo->findByCode($code);
        abort_if(!$poll, 404, 'Опрос не найден');
        abort_if(!$poll->options->contains('id', $optionId), 400, 'Неверный вариант');

        if ($this->repo->hasVoted($poll->id, $ip)) {
            abort(409, 'Вы уже голосовали');
        }

        $this->repo->recordVote($poll->id, $optionId, $ip);
        return $this->repo->getResults($code);
    }

    public function checkIfVoted(string $code, string $ip): bool
    {
        $poll = $this->repo->findByCode($code);
        if (!$poll) return false;
        return $this->repo->hasVoted($poll->id, $ip);
    }
}
