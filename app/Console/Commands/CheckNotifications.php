<?php

namespace App\Console\Commands;

use App\Enums\CarType;
use App\Utils\{Sms, Rzd};
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Redis;

class CheckNotifications extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'check:notifications';

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
            $rzdResponse = Rzd::get($redisData->date, $redisData->from, $redisData->to);
            foreach ($rzdResponse as $item) {
                if ($item->number === $redisData->number) {
                    // проверяем тип вагона
                    if (isset($redisData->type)) {
                        $type = CarType::getValue($redisData->type);
                        if (array_search($type, array_column($item->cars, 'itype')) === false) {
                            continue;
                        }
                    }
                    $this->info('Есть места!');
                    dump($item);
                    Sms::send(env('PHONE'), 'TEST ' . $item->number . ' ' . $item->time0);
                    Redis::del($redisKey);
                }
            }
        }
    }
}
