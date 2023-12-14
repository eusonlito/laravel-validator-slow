<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Validator;

class Slow extends Command
{
    protected $signature = 'app:slow';

    public function handle()
    {
        $data = $this->data();

        $this->validatorAsterisk($data);
        $this->validatorFixed($data);
    }

    protected function data(): array
    {
        $start = microtime(true);

        $data = ['items' => json_decode(file_get_contents(base_path('large-file.json')), true)];

        dump('Read Data Time: '.sprintf('%.4f', microtime(true) - $start));
        dump('Items: '.count($data['items']));

        return $data;
    }

    protected function validatorAsterisk(array $data)
    {
        $start = microtime(true);

        Validator::make($data, [
            'items' => ['array'],
            'items.*.id' => ['required', 'numeric'],
            'items.*.type' => ['required', 'string'],
            'items.*.public' => ['required', 'boolean'],
            'items.*.created_at' => ['required'],
        ]);

        dump('validatorAsterisk Time: '.sprintf('%.4f', microtime(true) - $start));
    }

    protected function validatorFixed(array $data)
    {
        $start = microtime(true);

        $count = count($data['items']);
        $rules = ['items' => ['array']];

        for ($i = 0; $i < $count; $i++) {
            $rules['items.'.$i.'.id'] = ['required', 'numeric'];
            $rules['items.'.$i.'.type'] = ['required', 'string'];
            $rules['items.'.$i.'.public'] = ['required', 'boolean'];
            $rules['items.'.$i.'.created_at'] = ['required'];
        }

        dump('validatorFixed Prepare: '.sprintf('%.4f', microtime(true) - $start));

        $start = microtime(true);

        Validator::make($data, $rules);

        dump('validatorFixed Time: '.sprintf('%.4f', microtime(true) - $start));
    }
}
