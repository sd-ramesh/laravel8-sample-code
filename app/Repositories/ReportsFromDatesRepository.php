<?php
declare(strict_types=1);


namespace App\Repositories;

use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use App\Models\Queue;
use App\Models\User;

/**
 * Class StatsRepository
 * @package App\Repositories
 *
 * @property Queue $model
 */
class ReportsFromDatesRepository extends BaseRepository implements ReportsFromDatesInterface
{
    public function getByDates(Carbon $fromDate, Carbon $toDate = null): Collection
    {
        // $user = auth()->user();
        $user = User::with('vendor')->first();

        $model = $this->model->where([
            'created_at' => $fromDate->toDate(),
            'vendor_id' => $user->vendor->id
        ]);

        $data = $model->get();

        // Do some other related query and stats enhancement

        return $data;
    }
}
