<?php

namespace App\Services;

use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;

class FastTextService
{
    protected $fastTextPath;

    protected $modelPath;

    public function __construct()
    {
        $this->fastTextPath = env('FASTTEXT_BINARY_PATH', '/path/to/fasttext'); // Adjust path as needed
        $this->modelPath = env('FASTTEXT_MODEL_PATH', '/path/to/lid.176.bin'); // Adjust path as needed
    }

    public function detectLanguage($text)
    {
        $process = new Process([$this->fastTextPath, 'predict-prob', $this->modelPath, '-']);
        $process->setInput($text);
        $process->run();

        if (! $process->isSuccessful()) {
            throw new ProcessFailedException($process);
        }

        $output = $process->getOutput();
        $lines = explode("\n", trim($output));
        $language = explode(' ', $lines[0]);

        return str_replace('__label__', '', $language[0]);
    }
}
