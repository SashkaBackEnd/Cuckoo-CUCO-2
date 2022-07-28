<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Support\Collection;

interface EventInterface
{
    public function generateEvent(Carbon $from = null, Carbon $to = null): Collection;
}
