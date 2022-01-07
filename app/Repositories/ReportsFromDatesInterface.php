<?php
declare(strict_types=1);


namespace App\Repositories;

use Illuminate\Support\Collection;
use Illuminate\Support\Carbon;

interface ReportsFromDatesInterface
{
    public function getByDates(Carbon $fromDate, Carbon $toDate = null): Collection;
}
