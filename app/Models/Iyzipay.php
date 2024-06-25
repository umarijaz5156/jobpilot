<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Iyzipay extends Model
{
    use HasFactory;
    public static function options()
    {
        $options = new \Iyzipay\Options();
        $options->setApiKey('sandbox-sQFqo1xO9AZub8xmt2CZjVqTTWVVgnbu');
        $options->setSecretKey('sandbox-r9zHFYjfcGPYCV5tQGC0c0glfq05a9wC');
        $options->setBaseUrl('https://sandbox-api.iyzipay.com');

        return $options;
    }
}
