<?php

namespace App\Console\Commands;

use App\Services\FastTextService;
use Illuminate\Console\Command;

class DetectLanguage extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'detect:language {text}';

    protected $description = 'Detect the language of the provided text';

    protected $fastTextService;

    public function __construct(FastTextService $fastTextService)
    {
        parent::__construct();
        $this->fastTextService = $fastTextService;
    }

    public function handle()
    {
        $text = $this->argument('text');
        $language = $this->fastTextService->detectLanguage($text);

        $this->info('Detected Language: ' . $language);
    }
}
