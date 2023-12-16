<?php

declare(strict_types=1);

namespace App;

use PhpBench\Attributes\Groups;
use PhpBench\Attributes\ParamProviders;
use Yiisoft\Validator\Rule\BooleanValue;
use Yiisoft\Validator\Rule\Each;
use Yiisoft\Validator\Rule\Nested;
use Yiisoft\Validator\Rule\Number;
use Yiisoft\Validator\Rule\Required;
use Yiisoft\Validator\Rule\StringValue;
use Yiisoft\Validator\Validator;

final class Yii3Bench
{
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
        return [
            'items' => new Each([
                new Nested([
                    'id' => [new Required(), new Number()],
                    'type' => [new Required(), new StringValue()],
                    'public' => [new Required(), new BooleanValue()],
                    'created_at' => [new Required()],
                ]),
            ]),
        ];
    }

    #[Groups(['yii3'])]
    #[ParamProviders(['provideData'])]
    public function benchYiiFullData(array $data): void
    {
        $this->validateYii3($this->provideRules(), $data['data']);
    }

    #[Groups(['yii3'])]
    #[ParamProviders(['provideData', 'provideChunks'])]
    public function benchYiiChunked(array $data): void
    {
        foreach (array_chunk($data['data']['items'], $data['chunkSize']) as $chunk) {
            $this->validateYii3($this->provideRules(), ['items' => $chunk]);
        }
    }

    private function validateYii3(array $rules, array $data): void
    {
        (new Validator())->validate($data, $rules);
    }
}
