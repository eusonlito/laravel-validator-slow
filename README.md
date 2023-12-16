# PHP Validator benchmarks

This benchmark idea was born when a real production data validation took me **about a minute**.

The `yiisoft/validator` is used as an alternative because it can be used in any Laravel application after just 
`composer require yiisoft/validator` without any configuration except validation rules.

The current values (`laravel/framework:10.37.3`):

| benchmark    | subject              | set               | revs | mem_peak | time_avg      | comp_z_value | comp_deviation |
|--------------|----------------------|-------------------|------|----------|---------------|--------------|----------------|
| Yii3Bench    | benchYiiFullData     | 11351 items       | 10   | 15.934MB | 328.396ms     | +0.00σ       | +0.00%         |
| Yii3Bench    | benchYiiChunked      | 11351 items,100   | 10   | 16.236MB | 330.047ms     | +0.00σ       | +0.00%         |
| Yii3Bench    | benchYiiChunked      | 11351 items,1000  | 10   | 16.182MB | 335.803ms     | +0.00σ       | +0.00%         |
| Yii3Bench    | benchYiiChunked      | 11351 items,5000  | 10   | 16.341MB | 359.142ms     | +0.00σ       | +0.00%         |
| Yii3Bench    | benchYiiChunked      | 11351 items,10000 | 10   | 16.468MB | 410.632ms     | +0.00σ       | +0.00%         |
| LaravelBench | benchAsterisk        | 11351 items       | 10   | 60.726MB | _**62.143s**_ | +0.00σ       | +0.00%         |
| LaravelBench | benchFixed           | 11351 items       | 10   | 32.492MB | 12.223ms      | +0.00σ       | +0.00%         |
| LaravelBench | benchChunkedAsterisk | 11351 items,100   | 10   | 27.305MB | 442.052ms     | +0.00σ       | +0.00%         |
| LaravelBench | benchChunkedAsterisk | 11351 items,1000  | 10   | 29.794MB | 2.652s        | +0.00σ       | +0.00%         |
| LaravelBench | benchChunkedAsterisk | 11351 items,5000  | 10   | 41.014MB | 11.641s       | +0.00σ       | +0.00%         |
| LaravelBench | benchChunkedAsterisk | 11351 items,10000 | 10   | 54.957MB | 43.389s       | +0.00σ       | +0.00%         |
| LaravelBench | benchChunkedFixed    | 11351 items,100   | 10   | 27.307MB | 150.431ms     | +0.00σ       | +0.00%         |
| LaravelBench | benchChunkedFixed    | 11351 items,1000  | 10   | 30.000MB | 148.192ms     | +0.00σ       | +0.00%         |
| LaravelBench | benchChunkedFixed    | 11351 items,5000  | 10   | 44.418MB | 154.372ms     | +0.00σ       | +0.00%         |
| LaravelBench | benchChunkedFixed    | 11351 items,10000 | 10   | 61.827MB | 157.231ms     | +0.00σ       | +0.00%         |

Initial `phpbench` data:

| iter | benchmark    | subject              | set               | revs | mem_peak    | time_avg         | comp_z_value | comp_deviation |
|------|--------------|----------------------|-------------------|------|-------------|------------------|--------------|----------------|
| 0    | Yii3Bench    | benchYiiFullData     | 11351 items       | 10   | 15,934,328b | 328,395.500μs    | +0.00σ       | +0.00%         |
| 0    | Yii3Bench    | benchYiiChunked      | 11351 items,100   | 10   | 16,235,608b | 330,046.800μs    | +0.00σ       | +0.00%         |
| 0    | Yii3Bench    | benchYiiChunked      | 11351 items,1000  | 10   | 16,181,576b | 335,802.500μs    | +0.00σ       | +0.00%         |
| 0    | Yii3Bench    | benchYiiChunked      | 11351 items,5000  | 10   | 16,340,656b | 359,141.800μs    | +0.00σ       | +0.00%         |
| 0    | Yii3Bench    | benchYiiChunked      | 11351 items,10000 | 10   | 16,467,584b | 410,632.100μs    | +0.00σ       | +0.00%         |
| 0    | LaravelBench | benchAsterisk        | 11351 items       | 10   | 60,725,576b | 62,142,847.400μs | +0.00σ       | +0.00%         |
| 0    | LaravelBench | benchFixed           | 11351 items       | 10   | 32,491,728b | 12,222.900μs     | +0.00σ       | +0.00%         |
| 0    | LaravelBench | benchChunkedAsterisk | 11351 items,100   | 10   | 27,305,264b | 442,051.700μs    | +0.00σ       | +0.00%         |
| 0    | LaravelBench | benchChunkedAsterisk | 11351 items,1000  | 10   | 29,794,376b | 2,652,065.300μs  | +0.00σ       | +0.00%         |
| 0    | LaravelBench | benchChunkedAsterisk | 11351 items,5000  | 10   | 41,013,648b | 11,640,652.800μs | +0.00σ       | +0.00%         |
| 0    | LaravelBench | benchChunkedAsterisk | 11351 items,10000 | 10   | 54,956,824b | 43,388,806.300μs | +0.00σ       | +0.00%         |
| 0    | LaravelBench | benchChunkedFixed    | 11351 items,100   | 10   | 27,306,768b | 150,431.300μs    | +0.00σ       | +0.00%         |
| 0    | LaravelBench | benchChunkedFixed    | 11351 items,1000  | 10   | 29,999,872b | 148,192.300μs    | +0.00σ       | +0.00%         |
| 0    | LaravelBench | benchChunkedFixed    | 11351 items,5000  | 10   | 44,418,448b | 154,372.000μs    | +0.00σ       | +0.00%         |
| 0    | LaravelBench | benchChunkedFixed    | 11351 items,10000 | 10   | 61,827,024b | 157,231.300μs    | +0.00σ       | +0.00%         |


Run benchmarks on your onw machine after repository cloning:
`composer install`
`php vendor/bin/phpbench run`

Command modifications:
- If you want to run Laravel or Yii benchmarks only, you can add either `--group laravel` or `--group yii3` to the command
- If you want to run benchmark more times, you can add `--revs N` with the count instead of N. I.e., `--revs 1000`. It may be very time-consuming operation.  
For other options see `phpbench` docs.

The full example:
```bash
php vendor/bin/phpbench run --group yii3
```
