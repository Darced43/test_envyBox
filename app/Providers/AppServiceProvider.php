<?php

namespace App\Providers;

use App\Application\Factories\PollFactory;
use App\Application\Services\PollService;
use App\Application\Strategies\{CodeGenerationStrategyInterface, RandomCodeStrategy};
use App\Domain\Repositories\PollRepositoryInterface;
use App\Infrastructure\Repositories\EloquentPollRepository;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void {
        $this->app->bind(CodeGenerationStrategyInterface::class, RandomCodeStrategy::class);
        $this->app->bind(PollRepositoryInterface::class, EloquentPollRepository::class);
        $this->app->bind(PollService::class, function($app){
            return new PollService(
                $app->make(PollRepositoryInterface::class),
                new PollFactory($app->make(CodeGenerationStrategyInterface::class))
            );
        });
    }
}
