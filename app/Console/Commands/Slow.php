<?php

namespace App\Console\Commands;

use Closure;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\RequiredIf;

class Slow extends Command
{
    protected $signature = 'app:slow';

    public function handle(): void
    {
        $data = $this->data();

        $this->validatorAsterisk($data);
        $this->validatorFixed($data);
        //$this->validatorChunks($data, 10);
        //$this->validatorChunks($data, 100);
        //$this->validatorChunks($data, 1000);
        //$this->validatorChunks($data, 5000);
        //$this->validatorChunks($data, 10000);
    }

    protected function data(): array
    {

        $data = ['items' => json_decode(file_get_contents(base_path('large-file.json')), true)];


        dump('Items: '.count($data['items']));

        return $data;
    }

    protected function validatorAsterisk(array $data): void
    {
        $start = $this->start();

        Validator::make($data, $this->rulesAsterisk())->stopOnFirstFailure()->fails() ? die(__LINE__.':FAIL') : '';

        $this->finish('validatorAsterisk', $start);
    }

    protected function validatorFixed(array $data): void
    {
        $rules = $this->rulesFixed($data);

        $start = $this->start();

        Validator::make($data, $rules)->stopOnFirstFailure()->fails() ? die(__LINE__.':FAIL') : '';

        $this->finish('validatorFixed', $start);
    }

    protected function validatorChunks(array $data, int $size): void
    {
        $start = $this->start();

        foreach (array_chunk($data['items'], $size) as $chunk) {
            Validator::make(['items' => $chunk], $this->rulesAsterisk())->stopOnFirstFailure()->fails() ? die(__LINE__.':FAIL') : '';
        }

        $this->finish('validatorChunks'.$size, $start);
    }

    protected function rulesAsterisk(): array
    {
        return [
            'items' => ['array'],
            'items.*.id' => ['required', 'numeric'],
            'items.*.type' => ['required', 'string', $this->ruleClosure()],
            'items.*.public' => ['required', 'boolean'],
            'items.*.created_at' => [$this->ruleRequiredIf()],
        ];
    }

    protected function rulesFixed(array $data): array
    {
        $start = $this->start();

        $count = count($data['items'] ?? $data);
        $rules = ['items' => ['array']];

        $ruleClosure = $this->ruleClosure();
        $ruleRequiredIf = $this->ruleRequiredIf();

        for ($i = 0; $i < $count; $i++) {
            $rules['items.'.$i.'.id'] = ['required', 'numeric'];
            $rules['items.'.$i.'.type'] = ['required', 'string', $ruleClosure];
            $rules['items.'.$i.'.public'] = ['required', 'boolean'];
            $rules['items.'.$i.'.created_at'] = [$ruleRequiredIf];
        }

        $this->finish('rulesFixed', $start);

        return $rules;
    }

    protected function ruleClosure(): Closure
    {
        return static function (string $attribute, mixed $value, Closure $fail) {
            if ($value === 'foo') {
                $fail("The {$attribute} is invalid.");
            }
        };
    }

    protected function ruleRequiredIf(): RequiredIf
    {
        return Rule::requiredIf(static fn () => boolval(rand(0, 1)));
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
