<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Candidate;
use App\Models\Company;
use App\Models\Earning;
use App\Models\Job;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use PDF;

class AdminController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth:admin');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        return view('backend.home');
    }

    /*
    * Show the application dashboard.
    *
    * @return \Illuminate\Contracts\Support\Renderable
    */
    public function dashboard()
    {
        session(['layout_mode' => 'left_nav']);
        $jobs = Job::withoutEdited()->get();

        $data['all_jobs'] = $jobs->count();
        $data['active_jobs'] = $jobs->where('status', 'active')->count();
        $data['expire_jobs'] = $jobs->where('status', 'expired')->count();
        $data['pending_jobs'] = $jobs->where('status', 'pending')->count();
        $data['verified_users'] = User::whereNotNull('email_verified_at')->count();
        $data['candidates'] = Candidate::all()->count();
        $data['companies'] = Company::all()->count();
        $data['earnings'] = currencyConversion(Earning::sum('usd_amount'));
        $data['email_verification'] = setting('email_verification');

        $months = Earning::select(
            \DB::raw('MIN(created_at) AS created_at'),
            \DB::raw('sum(usd_amount) as `amount`'),
            \DB::raw("DATE_FORMAT(created_at,'%M') as month")
        )
            ->where('created_at', '>', \Carbon\Carbon::now()->startOfYear())
            ->orderBy('created_at')
            ->groupBy('month')
            ->get();

        $earnings = $this->formatEarnings($months);
        $latest_jobs = Job::withoutEdited()->with(['company', 'job_type', 'experience'])->latest()->get()->take(10);
        $latest_earnings = Earning::with('plan', 'manualPayment:id,name')->latest()->take(10)->get();
        $users = User::select(['id', 'name', 'email', 'role', 'status', 'email_verified_at', 'created_at', 'image', 'username'])->latest()->take(10)->get();
        $popular_countries = DB::table('jobs')
            ->select('country', DB::raw('count(*) as total'))
            ->orderBy('total', 'desc')
            ->groupBy('country')
            ->limit(10)
            ->get();

        $current_currency = currentCurrency();

        return view('backend.index', compact('data', 'earnings', 'popular_countries', 'latest_jobs', 'latest_earnings', 'users', 'current_currency'));
    }

    /*
    * Mark all notifications as read
    *
    * @return Response
    */
    public function notificationRead()
    {
        foreach (auth()->user()->unreadNotifications as $notification) {
            $notification->markAsRead();
        }

        return response()->json(true);
    }

    /*
    * Get all notifications
    *
    * @return Response
    */
    public function allNotifications()
    {

        $notifications = auth()->user()->notifications()->paginate(20);

        return view('backend.notifications', compact('notifications'));
    }

    /*
    * Format earnings data
    *
    * @param object $data
    * @return array
    */
    private function formatEarnings(object $data)
    {
        $amountArray = [];
        $monthArray = [];

        foreach ($data as $value) {
            array_push($amountArray, $value->amount);
            array_push($monthArray, $value->month);
        }

        return ['amount' => $amountArray, 'months' => $monthArray];
    }

    /*
    * Download transaction invoice
    *
    * @param Earning $transaction
    * @return Response
    */
    public function downloadTransactionInvoice(Earning $transaction)
    {
        $transaction = $transaction->load('plan', 'company.user.contactInfo');
        $pdf = PDF::loadView('frontend.pages.invoice.download-invoice', compact('transaction'))->setOptions(['defaultFont' => 'sans-serif']);

        return $pdf->stream();

        return $pdf->download('invoice_'.$transaction->order_id.'.pdf');
    }

    /*
    * View transaction invoice
    *
    * @param Earning $transaction
    * @return Response
    */
    public function viewTransactionInvoice(Earning $transaction)
    {
        $transaction = $transaction->load('plan', 'company.user.contactInfo');

        return view('frontend.pages.invoice.preview-invoice', compact('transaction'));
    }
}
