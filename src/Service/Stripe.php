<?php
/**
 * Created by PhpStorm.
 * User: Hp
 * Date: 22/03/2018
 * Time: 09:08
 */

namespace App\Service;

use Stripe\Charge;
use Stripe\Transfer;
use Stripe\Stripe as StripeAPI;


class Stripe
{
    private $key;

    public function __construct(string $key)
    {
        $this->key = $key;
    }

    public function checkout(int $amount, int $popAmount, string $currency = 'eur', string $stripeToken)
    {
        $groupId = uniqid();
        StripeAPI::setApiKey($this->key);
        $charge = Charge::create(
            [
                'amount' => $amount,
                'currency' => $currency,
                "source" => $stripeToken,
                //"transfer_group" => $groupId
            ]
        );
        /*$transferPro = Transfer::create(array(
            "amount" => $amount-$popAmount,
            "currency" => $currency,
            "destination" => "{CONNECTED_STRIPE_ACCOUNT_ID}",
            "transfer_group" => $groupId,
        ));*/
        return $charge;
    }
}