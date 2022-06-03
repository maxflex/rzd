<?php

namespace App\Console\Commands;

use App\Enums\CarType;
use App\Utils\{Sms, Rzd};
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Redis;

class CheckNotifications extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'check:notifications {--debug}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $keys = Redis::keys('*notification*');
        foreach ($keys as $key) {
            $redisKey = str_replace(config('database.redis.options.prefix'), '', $key);
            $redisData = json_decode(Redis::get($redisKey));
            $this->dump($redisData);
            $rzdResponse = Rzd::get($redisData->date, $redisData->from, $redisData->to);
            foreach ($rzdResponse as $item) {
                if ($item->number === $redisData->number) {
                    $this->info('Есть места!');
                    $this->dump($item);
                    // проверяем тип вагона
                    if (isset($redisData->type)) {
                        $type = CarType::getValue($redisData->type);
                        if (array_search($type, array_column($item->cars, 'itype')) === false) {
                            continue;
                        }
                    }
                    if (isset($redisData->lower)) {
                        $seats = Rzd::getSeats($item);
                        $this->dump($seats);
                        if (!$seats->contains(
                            fn ($e) => strpos($e['CarPlaceType'], 'Lower') !== false
                                && (isset($redisData->type)
                                    // Compartment - купе, ReservedSeat - плацкарт
                                    ? $e['CarType'] === ($redisData->type === 'coupe' ? 'Compartment' : 'ReservedSeat')
                                    : true)
                        )) {
                            continue;
                        }
                    }

                    $this->info('Есть нужные места!');
                    Sms::send(env('PHONE'), 'TEST ' . $item->number . ' ' . $item->time0);
                    Redis::del($redisKey);
                }
            }
        }
    }

    private function dump($data)
    {
        if ($this->option('debug')) {
            dump($data);
        }
    }
}
