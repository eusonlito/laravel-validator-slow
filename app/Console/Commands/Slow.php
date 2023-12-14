<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Validator;

class Slow extends Command
{
    const ASTERISK_FORMAT = 'rulesAsterisk';
    const FIXED_FORMAT = 'validatorFixed';
    
    protected $signature = 'app:slow';

    public function handle(): void
    {
        $data = $this->data();

        $this->validatorAsterisk($data);
        $this->validatorFixed($data);
        $this->validatorChunks($data, 100);
        $this->validatorChunks($data, 1000);
        $this->validatorChunks($data, 1000, self::FIXED_FORMAT);
        $this->validatorChunks($data, 5000);
        $this->validatorChunks($data, 10000);
    }

    protected function data(): array
    {
        $start = $this->start();

        $data = ['items' => json_decode(file_get_contents(base_path('large-file.json')), true)];

        $this->finish('Read Data', $start);

        dump('Items: '.count($data['items']));

        return $data;
    }

    protected function validatorAsterisk(array $data): void
    {
        $start = $this->start();

        Validator::make($data, $this->rulesAsterisk());

        $this->finish('validatorAsterisk', $start);
    }

    protected function validatorFixed(array $data): void
    {
        $rules = $this->rulesFixed($data);

        $start = $this->start();

        Validator::make($data, $rules);

        $this->finish('validatorFixed', $start);
    }

    protected function validatorChunks(array $data, int $size, $mode = self::ASTERISK_FORMAT): void
    {
        $start = $this->start();

        foreach (array_chunk($data['items'], $size) as $chunk) {
            Validator::make(
                ['items' => $chunk],
                $mode == self::ASTERISK_FORMAT
                    ? $this->rulesAsterisk()
                    : $this->rulesFixed($chunk)
            );
        }

        $this->finish('validatorChunks'.$size, $start);
    }

    protected function rulesAsterisk(): array
    {
        return [
            'items' => ['array'],
            'items.*.id' => ['required', 'numeric'],
            'items.*.type' => ['required', 'string'],
            'items.*.public' => ['required', 'boolean'],
            'items.*.created_at' => ['required'],
        ];
    }

    protected function rulesFixed(array $data): array
    {
        $start = $this->start();

        $count = count($data['items'] ?? $data);
        $rules = ['items' => ['array']];

        for ($i = 0; $i < $count; $i++) {
            $rules['items.'.$i.'.id'] = ['required', 'numeric'];
            $rules['items.'.$i.'.type'] = ['required', 'string'];
            $rules['items.'.$i.'.public'] = ['required', 'boolean'];
            $rules['items.'.$i.'.created_at'] = ['required'];
        }

        $this->finish('rulesFixed', $start);

        return $rules;
    }

    protected function start(): float
    {
        memory_reset_peak_usage();

        return microtime(true);
    }

    protected function finish(string $title, float $start): void
    {
        dump(sprintf($title.': %.4f seconds / %.2f memory', microtime(true) - $start, memory_get_peak_usage(true) / 1024 / 1024));
    }
}
