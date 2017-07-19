<?php

namespace Tests\Unit\Mail;

use App\Order;
use Tests\TestCase;
use App\Mail\OrderConfirmationEmail;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class OrderConfirmationEmailTest extends TestCase
{
    /** @test */
    function email_contains_a_link_to_the_order_confirmation_page()
    {
        $order = factory(Order::class)->make(['confirmation_name' => 'ORDERCONFIRMATION1234']);
        $email = new OrderConfirmationEmail($order);

        // L5.5 use $email->render() instead
        $rendered = $this->render($email);

        $this->assertContains(url('/orders/ORDERCONFIRMATION1234'), $rendered);
    }

    /** @test */
    function email_has_a_subject()
    {
        $order = factory(Order::class)->make();
        $email = new OrderConfirmationEmail($order);

        $this->assertEquals('Your TicketMonster Order', $email->build()->subject);
    }

    private function render($mailable)
    {
        $mailable->build();
        return view($mailable->view, $mailable->buildViewData())->render();
    }
}
