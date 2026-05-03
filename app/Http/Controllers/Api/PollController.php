<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\{CreatePollRequest, VoteRequest};
use App\Application\Services\PollService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PollController extends Controller {
    public function __construct(private PollService $service) {}

    public function store(CreatePollRequest $request): JsonResponse {
        $poll = $this->service->createPoll(
            $request->validated('title'),
            $request->validated('options')
        );
        return response()->json(['short_code' => $poll->short_code], 201);
    }

    public function show(string $shortCode, Request $request): JsonResponse
    {
        $data = $this->service->getPoll($shortCode);
        
        // Проверяем, голосовал ли уже с этого IP
        $ip = $request->ip() ?? '127.0.0.1';
        $data['has_voted'] = $this->service->checkIfVoted($shortCode, $ip);
        
        return response()->json($data);
    }

    public function vote(VoteRequest $request, string $shortCode): JsonResponse{
        $ip = $request->ip() ?? '127.0.0.1';
        $results = $this->service->vote($shortCode, $request->validated('option_id'), $ip);
        return response()->json(['results' => $results]);
    }
}