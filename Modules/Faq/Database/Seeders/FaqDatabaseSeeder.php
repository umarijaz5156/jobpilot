<?php

namespace Modules\Faq\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Faq\Entities\Faq;
use Modules\Language\Entities\Language;

class FaqDatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $faqs = [
            [
                'question' => 'How do I change my password?',
                'answer' => 'You can change your password by logging into your account and going to the "Change Password" section in your profile settings.',
            ],
            [
                'question' => 'What is your product quality guarantee?',
                'answer' => 'We guarantee the quality of our products. If you encounter any issues, please contact our customer support for assistance.',
            ],
            [
                'question' => 'Can I add multiple items to my cart?',
                'answer' => 'Yes, you can add multiple items to your cart by clicking "Add to Cart" for each item you want to purchase.',
            ],
            [
                'question' => 'What is your pricing policy for products?',
                'answer' => 'Our pricing is competitive and based on market rates. We regularly update our prices to reflect current market conditions.',
            ],
            [
                'question' => 'Do you offer a price match guarantee?',
                'answer' => 'Yes, we offer a price match guarantee. If you find the same product at a lower price elsewhere, contact us for a price match.',
            ],
            [
                'question' => 'What is your policy on product recalls?',
                'answer' => 'In the event of a product recall, we will promptly notify affected customers and provide instructions for returns or replacements.',
            ],
            [
                'question' => 'How do I check the availability of a product in a specific size or color?',
                'answer' => 'To check the availability of a specific size or color, select the product and use the dropdown menus on the product page.',
            ],
            [
                'question' => 'What is your return shipping cost policy?',
                'answer' => 'Return shipping costs may be covered by us for eligible returns. Contact our customer support for return instructions and shipping labels.',
            ],
            [
                'question' => 'Do you offer technical support for your products?',
                'answer' => 'Yes, we offer technical support for our products. Contact our technical support team for assistance with product-related issues.',
            ],
            [
                'question' => 'What is your product delivery guarantee?',
                'answer' => 'We guarantee on-time product delivery. If your order is delayed, please contact our customer support for assistance.',
            ],
            [
                'question' => 'Do you offer price adjustments for recently purchased items?',
                'answer' => 'Yes, we offer price adjustments for items purchased within a specified time frame. Contact us for more information on price adjustments.',
            ],
            [
                'question' => 'What is your policy on environmental sustainability?',
                'answer' => 'We are committed to environmental sustainability and follow eco-friendly practices in our operations. Learn more on our sustainability page.',
            ],
        ];

        $lang_codes = Language::all();
        $categoryIds = [1, 2, 3];

        foreach ($lang_codes as $lang_code) {
            foreach ($faqs as $faq) {
                Faq::create([
                    'faq_category_id' => $categoryIds[array_rand($categoryIds)],
                    'question' => $faq['question'],
                    'answer' => $faq['answer'],
                    'code' => $lang_code->code,
                ]);
            }
        }
    }
}
