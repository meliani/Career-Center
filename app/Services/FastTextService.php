<?php

namespace App\Services;

use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;

class FastTextService
{
    protected $fastTextPath;

    protected $departmentModelPath;

    public function __construct()
    {
        $this->fastTextPath = env('FASTTEXT_BINARY_PATH', '/path/to/fasttext');
        $this->departmentModelPath = env('FASTTEXT_DEPARTMENT_MODEL_PATH', '/path/to/department.bin');
    }

    public function predictDepartment($text, $k = 3)
    {
        $process = new Process([
            $this->fastTextPath,
            'predict-prob',
            $this->departmentModelPath,
            '-',
            $k,
        ]);

        $process->setInput($text);
        $process->run();

        if (! $process->isSuccessful()) {
            throw new ProcessFailedException($process);
        }

        $output = trim($process->getOutput());
        $predictions = [];

        foreach (explode("\n", $output) as $line) {
            [$label, $probability] = explode(' ', $line);
            $department = str_replace('__label__', '', $label);
            $predictions[$department] = (float) $probability;
        }

        return $predictions;
    }
}
