<?php

namespace App\Traits;

use App\Models\ShippingRule;

trait ShippingCalculator
{
    public function calculateShipping($orderTotal, $region = null)
    {
        $rules = ShippingRule::where('status', '1')->get();

        foreach ($rules as $rule) {
            if (
                (!$rule->min_subtotal || $orderTotal >= $rule->min_subtotal) &&
                (!$rule->max_subtotal || $orderTotal <= $rule->max_subtotal)
            ) {
                return $rule->shipping_cost;
            }
        }

        return 150;
    }
}
