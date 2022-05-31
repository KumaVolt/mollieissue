<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Laravel\Cashier\Charge\ChargeItemBuilder;
use stdClass;

class CheckoutController extends Controller
{
    public function __invoke(Request $request)
    {
        $user = User::orderBy('id', 'desc')->first();
        
        $product_price = 1.00;
        $tax_rate = 0.21; // 21% tax rate

        // create a new charge item
        $item = new ChargeItemBuilder($user);
        $item->unitPrice(money($this->priceWithoutTax($product_price, $tax_rate) * 100, 'EUR'));
        $item->description('Product description');
        $item->quantity(1);
        $item->taxPercentage($tax_rate * 100);
        $chargeItem = $item->make();

        // dd($chargeItem); // charge item object omitted numbers numbers that are after the comma

        // generate checkout link and navigate to it
        $charge = $user->newFirstPaymentChargeThroughCheckout();
        $charge->addItem($chargeItem);
        $result = $charge->create();

        // expecting to pay 1 euro
        if(is_a($result, \Laravel\Cashier\Http\RedirectToCheckoutResponse::class)) {
            return $result;
        }
    }

    private function priceWithoutTax($price, $tax_rate)
    {
        return $price / (1 + ($tax_rate));
    }
}
