<?php

namespace App\Http\Traits;

use App\Models\CandidateCvView;
use Illuminate\Support\Carbon;

trait ResetCvViewsHistoryTrait
{
    public function reset()
    {
        $cv_views = CandidateCvView::all();
        foreach ($cv_views as $key => $view) {

            $view_date = $view->view_date;
            $destroy_date = Carbon::parse($view_date)->addDays(30);

            if ($destroy_date < Carbon::now()->endOfDay()) {
                $view->delete();
            }
        }
    }
}
