<?php

namespace App\Billing;

class Charge 
{
    protected $data;

    public function __construct(array $data)
    {
        $this->data = $data;
    }

    public function cardLastFour() 
    {
        return $this->data['card_last_four'];
    }

    public function amount() 
    {
        return $this->data['amount'];
    }
}