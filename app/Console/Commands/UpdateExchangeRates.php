<?php

namespace App\Console\Commands;

use GuzzleHttp\Client;
use Illuminate\Console\Command;
use Modules\Currency\Entities\Currency;

class UpdateExchangeRates extends Command
{
    protected $signature = 'update:exchange-rates';

    protected $description = 'Update exchange rates from external API';

    public function handle()
    {
        $currencies = Currency::pluck('code')->toArray();
        $symbol = implode(',', $currencies);

        $client = new Client();

        try {
            $response = $client->request('GET', 'https://api.apilayer.com/exchangerates_data/latest', [
                'query' => [
                    'symbols' => $symbol,
                    'base' => 'USD',
                ],
                'headers' => [
                    'Content-Type' => 'text/plain',
                    'apikey' => env('CURRENCY_API_KEY'),
                ],
            ]);

            $body = $response->getBody();
            $responseData = json_decode($body, true);

            foreach ($responseData['rates'] as $code => $value) {
                Currency::where('code', $code)->update(['rate' => $value]);
            }
        } catch (\Exception $e) {
            // Handle exception, e.g., log or return an error response
            $this->error($e->getMessage());
        }

        $this->info('Exchange rates updated successfully.');
    }
}
