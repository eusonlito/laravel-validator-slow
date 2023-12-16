<?php

declare(strict_types=1);

namespace App;

use Illuminate\Contracts\Console\Kernel as KernelContract;
use Illuminate\Contracts\Debug\ExceptionHandler;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Console\Kernel;
use Illuminate\Foundation\Exceptions\Handler;
use Illuminate\Support\Facades\Validator;
use JsonException;
use PhpBench\Attributes\Groups;
use PhpBench\Attributes\ParamProviders;

final class LaravelBench
{
    public function __construct()
    {
        $app = new Application(dirname(__DIR__));
        $app->singleton(
            KernelContract::class,
            Kernel::class,
        );
        $app->singleton(
            ExceptionHandler::class,
            Handler::class,
        );
        $app->make(KernelContract::class)->bootstrap();
    }

    public function provideData(): array
    {
        $items = json_decode(file_get_contents(dirname(__DIR__) . '/large-file.json'), true, flags: JSON_THROW_ON_ERROR);
        $count = count($items);

        return ["$count items" => ['data' => ['items' => $items]]];
    }

    public function provideChunks(): array
    {
        return [
            100 => ['chunkSize' => 100],
            1000 => ['chunkSize' => 1000],
            5000 => ['chunkSize' => 5000],
            10000 => ['chunkSize' => 10000],
        ];
    }

    public function provideRules(): array
    {
        $count = count($this->provideData()['data']['data']['items']);
        $rulesFixed = ['items' => ['array']];

        for ($i = 0; $i < $count; $i++) {
            $rulesFixed['items.' . $i . '.id'] = ['required', 'numeric'];
            $rulesFixed['items.' . $i . '.type'] = ['required', 'string'];
            $rulesFixed['items.' . $i . '.public'] = ['required', 'boolean'];
            $rulesFixed['items.' . $i . '.created_at'] = ['required'];
        }

        return [
            'asterisk' => [
                'rules' => [
                    'items' => ['array'],
                    'items.*.id' => ['required', 'numeric'],
                    'items.*.type' => ['required', 'string'],
                    'items.*.public' => ['required', 'boolean'],
                    'items.*.created_at' => ['required'],
                ],
            ],
            'fixed' => ['rules' => $rulesFixed],
        ];
    }


    #[Groups(['laravel'])]
    #[ParamProviders(['provideData'])]
    public function benchAsterisk(array $data): void
    {
        $rules = [
            'items' => ['array'],
            'items.*.id' => ['required', 'numeric'],
            'items.*.type' => ['required', 'string'],
            'items.*.public' => ['required', 'boolean'],
            'items.*.created_at' => ['required'],
        ];
        $this->validateLaravel($data['data'], $rules);
    }

    #[Groups(['laravel'])]
    #[ParamProviders(['provideData'])]
    public function benchFixed(array $data): void
    {
        $rulesFixed = $this->getFixedRules($data['data']);

        $this->validateLaravel($data['data'], $rulesFixed);
    }

    #[Groups(['laravel'])]
    #[ParamProviders(['provideData', 'provideChunks'])]
    public function benchChunkedAsterisk(array $data): void
    {
        $rules = [
            'items' => ['array'],
            'items.*.id' => ['required', 'numeric'],
            'items.*.type' => ['required', 'string'],
            'items.*.public' => ['required', 'boolean'],
            'items.*.created_at' => ['required'],
        ];
        foreach (array_chunk($data['data']['items'], $data['chunkSize']) as $chunk) {
            $this->validateLaravel(['items' => $chunk], $rules);
        }
    }

    #[Groups(['laravel'])]
    #[ParamProviders(['provideData', 'provideChunks'])]
    public function benchChunkedFixed(array $data): void
    {
        foreach (array_chunk($data['data']['items'], $data['chunkSize']) as $chunk) {
            $rules = $this->getFixedRules($chunk);
            $this->validateLaravel(['items' => $chunk], $rules);
        }
    }

    private function validateLaravel(array $data, array $rules): void
    {
        Validator::make($data, $rules);
    }

    /**
     * @return array[]
     * @throws JsonException
     */
    public function getFixedRules(array $data): array
    {
        $result = ['items' => ['array']];

        foreach ($data as $key => $value) {
            $result["items.$key.id"] = ['required', 'numeric'];
            $result["items.$key.type"] = ['required', 'string'];
            $result["items.$key.public"] = ['required', 'boolean'];
            $result["items.$key.created_at"] = ['required'];
        }
        return $result;
    }
}
