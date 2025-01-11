<?php

namespace Domain\NewsApis\Console\Commands;

use Domain\NewsApis\Services\NewsAggregatorService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class ImportArticlesCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'articles:import';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import initial articles from APIs and populate the database';

    /**
     * Execute the console command.
     */
    public function handle(NewsAggregatorService $newsAggregatorService)
    {

        $dataSources = $newsAggregatorService->dataSourceManager->getDataSources();

        $this->line('Fetching articles from APIs...');
        foreach ($dataSources as $sourceClass) {
            try {
                $source = app($sourceClass);
                $newsAggregatorService->importArticlesFromSource($source);
                $errorMessage = "Articles imported successfully from source: {$source->getName()}";
                $this->info($errorMessage);
            } catch (\Throwable $e) {
                $errorMessage = "Failed to import articles from source: {$source->getName()}";
                $this->warn($errorMessage);
                Log::error($errorMessage, [
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString(),
                ]);
            }
        }
        $this->alert('Check the log for any possible issues !');

    }
}
