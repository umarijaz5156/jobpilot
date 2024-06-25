<?php

namespace Modules\Currency\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Currency\Entities\Currency;
use Modules\Currency\Http\Requests\CurrencyCreateFormRequest;
use Modules\Currency\Http\Requests\CurrencyUpdateFormRequest;

class CurrencyController extends Controller
{
    public function __construct()
    {
        $this->middleware('access_limitation')->only([
            'destroy', 'update', 'defaultCurrency',
        ]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return Renderable
     */
    public function index()
    {
        try {
            abort_if(! userCan('setting.view'), 403);

            $currencies = Currency::paginate(15);

            return view('currency::index', compact('currencies'));
        } catch (\Exception $e) {
            flashError('An error occurred: '.$e->getMessage());

            return back();
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Renderable
     */
    public function create()
    {
        return view('currency::create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return Renderable
     */
    public function store(CurrencyCreateFormRequest $request)
    {
        try {
            Currency::create([
                'name' => $request->name,
                'code' => $request->code,
                'rate' => $request->rate,
                'symbol' => $request->symbol,
                'symbol_position' => $request->symbol_position ? 'left' : 'right',
            ]);

            flashSuccess(__('currency_created_successfully'));

            return redirect()->route('module.currency.index');
        } catch (\Exception $e) {
            flashError('An error occurred: '.$e->getMessage());

            return back();
        }
    }

    /**
     * Show the specified resource.
     *
     * @param  int  $id
     * @return Renderable
     */
    public function show($id)
    {
        return view('currency::show');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @return Renderable
     */
    public function edit(Currency $currency)
    {
        try {
            return view('currency::edit', compact('currency'));
        } catch (\Exception $e) {
            flashError('An error occurred: '.$e->getMessage());

            return back();
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @return Renderable
     */
    public function update(CurrencyUpdateFormRequest $request, Currency $currency)
    {
        try {
            $currency->update([
                'name' => $request->name,
                'code' => $request->code,
                'rate' => $request->rate,
                'symbol' => $request->symbol,
                'symbol_position' => $request->symbol_position == 'left' ? 'left' : 'right',
            ]);

            // Clear system currency cache
            forgetCache('systemCurrency');

            flashSuccess(__('currency_updated_successfully'));

            return redirect()->route('module.currency.index');
        } catch (\Exception $e) {
            flashError('An error occurred: '.$e->getMessage());

            return back();
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return Renderable
     */
    public function destroy(Currency $currency)
    {
        try {
            if ($currency->code == config('templatecookie.currency')) {
                $currencyDefault = Currency::where('code', 'USD')->first();
                if ($currencyDefault) {
                    envReplace('APP_CURRENCY', $currencyDefault->code);
                    envReplace('APP_CURRENCY_SYMBOL', $currencyDefault->symbol);
                    envReplace('APP_CURRENCY_SYMBOL_POSITION', $currencyDefault->symbol_position);

                    // Clear system currency cache
                    forgetCache('systemCurrency');
                }
            }

            $currency->delete();

            flashSuccess(__('currency_deleted_successfully'));

            return back();
        } catch (\Exception $e) {
            flashError('An error occurred: '.$e->getMessage());

            return back();
        }
    }

    public function defaultCurrency(Request $request)
    {
        try {
            $currency = Currency::findOrFail($request->currency);

            envReplace('APP_CURRENCY', $currency->code);
            envReplace('APP_CURRENCY_SYMBOL', $currency->symbol);
            envReplace('APP_CURRENCY_SYMBOL_POSITION', $currency->symbol_position);

            // Clear system currency cache
            forgetCache('systemCurrency');

            flashSuccess(__('currency_changed_successfully'));

            return redirect()->back();
        } catch (\Exception $e) {
            flashError('An error occurred: '.$e->getMessage());

            return back();
        }
    }

    public function example()
    {
        try {
            return view('currency::example');
        } catch (\Exception $e) {
            flashError('An error occurred: '.$e->getMessage());

            return back();
        }
    }

    public function changeCurrency($code)
    {
        try {
            $currency = Currency::where('code', $code)->first();
            session(['current_currency' => $currency]);
            currencyRateStore();

            return back();
        } catch (\Exception $e) {
            flashError('An error occurred: '.$e->getMessage());

            return back();
        }
    }
}
