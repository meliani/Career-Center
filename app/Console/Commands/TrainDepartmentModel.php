<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;

class TrainDepartmentModel extends Command
{
    protected $signature = 'train:department-model 
                            {trainingFile : Path to labeled training data} 
                            {--output=department : Output prefix for the model}
                            {--model=quantized : Model type (standard/quantized)}';

    protected $description = 'Train a FastText department model from labeled data';

    public function handle()
    {
        $fastTextPath = env('FASTTEXT_BINARY_PATH', '/path/to/fasttext');
        $inputData = $this->argument('trainingFile');
        $outputPrefix = storage_path('app/training_data/' . $this->option('output'));
        $modelType = $this->option('model');

        // First train the standard model
        $process = new Process([
            $fastTextPath,
            'supervised',
            '-input', $inputData,
            '-output', $outputPrefix,
            '-epoch', '50',
            '-lr', '1.0',
            '-wordNgrams', '3',
            '-minCount', '1',
            '-minn', '3',
            '-maxn', '6',
            '-dim', '300',
            '-loss', 'softmax',
            '-thread', '4',
        ]);

        $this->info('Training initial model...');
        $process->setTimeout(500)->run();
        if (! $process->isSuccessful()) {
            throw new ProcessFailedException($process);
        }

        // If quantized model requested, create it
        if ($modelType === 'quantized') {
            $process = new Process([
                $fastTextPath,
                'quantize',
                '-input', $inputData,
                '-output', $outputPrefix,
                '-qnorm',
                '-cutoff', '50000',
                '-retrain',
            ]);

            $this->info('Creating quantized model for better predictions...');
            $process->setTimeout(500)->run();
            if (! $process->isSuccessful()) {
                throw new ProcessFailedException($process);
            }

            $modelPath = $outputPrefix . '.ftz';
        } else {
            $modelPath = $outputPrefix . '.bin';
        }

        $this->info('Model training completed. Model saved at: ' . $modelPath);

        // Update .env to use the new model
        $this->updateEnvFile($modelPath);
    }

    protected function updateEnvFile($modelPath)
    {
        $envContent = file_get_contents(base_path('.env'));
        $envContent = preg_replace(
            '/FASTTEXT_DEPARTMENT_MODEL_PATH=.*/',
            'FASTTEXT_DEPARTMENT_MODEL_PATH=' . $modelPath,
            $envContent
        );
        file_put_contents(base_path('.env'), $envContent);

        $this->info('Updated .env file with new model path');
    }
}
