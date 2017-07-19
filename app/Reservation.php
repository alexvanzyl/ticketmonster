<?php

namespace App;

class Reservation 
{
    protected $tickets;
    protected $email;
    
    public function __construct($tickets, $email)
    {
        $this->tickets = $tickets;
        $this->email = $email;
    }

    public function totalCost()
    {
        return $this->tickets->sum('price');
    }

    public function complete($paymentGateway, $paymentToken)
    {
        $charge = $paymentGateway->charge($this->totalCost(), $paymentToken);

        return Order::forTickets($this->tickets(), $this->email(), $charge);
    }

    public function cancel()
    {
        $this->tickets->each->release();
    }

    public function tickets()
    {
        return $this->tickets;
    }

    public function email()
    {
        return $this->email;
    }
}