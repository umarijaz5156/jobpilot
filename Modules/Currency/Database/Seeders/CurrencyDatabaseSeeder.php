<?php

namespace Modules\Currency\Database\Seeders;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Seeder;
use Modules\Currency\Entities\Currency;

class CurrencyDatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Model::unguard();

        $currencies = [
            [
                'name' => 'United States Dollar',
                'code' => 'USD',
                'symbol' => '$',
                'symbol_position' => 'left',
                'rate' => '1.00',
            ],
            [
                'name' => 'Indian Rupee',
                'code' => 'INR',
                'symbol' => '₹',
                'symbol_position' => 'left',
                'rate' => '83.75',
            ],
            [
                'name' => 'Australian Dollar',
                'code' => 'AUD',
                'symbol' => '$',
                'symbol_position' => 'left',
                'rate' => '1.56',
            ],
            [
                'name' => 'Euro',
                'code' => 'EUR',
                'symbol' => '€',
                'symbol_position' => 'left',
                'rate' => '0.95',
            ],
            [
                'name' => 'Bangladeshi Taka',
                'code' => 'BDT',
                'symbol' => '৳',
                'symbol_position' => 'left',
                'rate' => '110.75',
            ],
            [
                'name' => 'Indonesian Rupiah',
                'code' => 'IDR',
                'symbol' => 'Rp',
                'symbol_position' => 'left',
                'rate' => '15600.65',
            ],
            [
                'name' => 'Pakistani Rupee',
                'code' => 'PKR',
                'symbol' => '₨',
                'symbol_position' => 'left',
                'rate' => '290.00',
            ],
            [
                'name' => 'Nigerian Naira',
                'code' => 'NGN',
                'symbol' => '₦',
                'symbol_position' => 'left',
                'rate' => '769.55',
            ],
            [
                'name' => 'Egyptian Pound',
                'code' => 'EGP',
                'symbol' => '£',
                'symbol_position' => 'left',
                'rate' => '30.90',
            ],
            [
                'name' => 'Turkish Lira',
                'code' => 'TRY',
                'symbol' => '₺',
                'symbol_position' => 'left',
                'rate' => '27.47',
            ],
            [
                'name' => 'Philippine Peso',
                'code' => 'PHP',
                'symbol' => '₱',
                'symbol_position' => 'left',
                'rate' => '56.79',
            ],
            [
                'name' => 'Kenyan Shilling',
                'code' => 'KES',
                'symbol' => 'KSh',
                'symbol_position' => 'left',
                'rate' => '148.30',
            ],
            [
                'name' => 'Nepalese Rupee',
                'code' => 'NPR',
                'symbol' => '₨',
                'symbol_position' => 'left',
                'rate' => '133.44',
            ],
            [
                'name' => 'British Pound Sterling',
                'code' => 'GBP',
                'symbol' => '£',
                'symbol_position' => 'left',
                'rate' => '0.82',
            ],
            [
                'name' => 'Vietnamese Dong',
                'code' => 'VND',
                'symbol' => '₫',
                'symbol_position' => 'left',
                'rate' => '24.335',
            ],
            [
                'name' => 'Brazilian Real',
                'code' => 'BRL',
                'symbol' => 'R$',
                'symbol_position' => 'left',
                'rate' => '5.03',
            ],
        ];

        foreach ($currencies as $currency) {
            Currency::create($currency);
        }
    }
}
