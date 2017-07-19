<?php

namespace Tests\Unit;

use App\Order;
use App\Ticket;
use App\Concert;
use Carbon\Carbon;
use Tests\TestCase;
use App\Exceptions\NotEnoughTicketsException;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class ConcertTest extends TestCase
{
    use DatabaseMigrations;

    /** @test */
    function can_get_formatted_date()
    {
        $concert = factory(Concert::class)->make([
            'date' => Carbon::parse('2016-12-13 8:00pm')
        ]);

        $this->assertEquals('December 13, 2016', $concert->formatted_date);
    }

    /** @test */
    function can_get_formatted_start_time()
    {
        $concert = factory(Concert::class)->make([
            'date' => Carbon::parse('2016-12-13 17:00:00')
        ]);

        $this->assertEquals('5:00pm', $concert->formatted_start_time);
    }

    /** @test */
    function can_get_ticket_price_in_dollars()
    {
        $concert = factory(Concert::class)->make([
            'ticket_price' => 6750
        ]);

        $this->assertEquals('67.50', $concert->ticket_price_in_dollars);
    }

    /** @test */
    function concerts_with_a_published_at_are_published()
    {
        $publishedConcertA = factory(Concert::class)->create([ 'published_at' => Carbon::parse('-1 week')]);
        $publishedConcertB = factory(Concert::class)->create([ 'published_at' => Carbon::parse('-1 week')]);
        $unpublishedConcert = factory(Concert::class)->create([ 'published_at' => null]);

        $publishedConcerts = Concert::published()->get();

        $this->assertTrue($publishedConcerts->contains($publishedConcertA));
        $this->assertTrue($publishedConcerts->contains($publishedConcertB));
        $this->assertFalse($publishedConcerts->contains($unpublishedConcert));
    }

    /** @test */
    function can_add_tickets()
    {
        $concert = factory(Concert::class)->create();

        $concert->addTickets(50);

        $this->assertEquals(50, $concert->ticketsRemaining());
    }

    /** @test */
    function tickets_remaining_do_not_include_tickets_associated_with_an_order()
    {
        $concert = factory(Concert::class)->create();
        $concert->tickets()->saveMany(factory(Ticket::class, 30)->create(['order_id' => 1]));
        $concert->tickets()->saveMany(factory(Ticket::class, 20)->create(['order_id' => null]));


        $this->assertEquals(20, $concert->ticketsRemaining());
    }

    /** @test */
    function trying_to_reserve_more_tickets_than_remain_throws_an_exception()
    {
        $concert = factory(Concert::class)->create()->addTickets(10);
        try {
            $reservation = $concert->reserveTickets(11, 'john@example.com');
        } catch (NotEnoughTicketsException $e) {
            $this->assertEquals(10, $concert->ticketsRemaining());
            return;
        }
        $this->fail("Order succeeded even though there were not enough tickets remaining.");
    }

    /** @test */
    function can_reserve_available_tickets()
    {
        $concert = factory(Concert::class)->create()->addTickets(3);
        $this->assertEquals(3, $concert->ticketsRemaining());

        $reservation = $concert->reserveTickets(2, 'john@doe.com');

        $this->assertCount(2, $reservation->tickets());
        $this->assertEquals('john@doe.com', $reservation->email());
        $this->assertEquals(1, $concert->ticketsRemaining());
    }

    /** @test */
    function cannot_reserve_tickets_that_have_already_been_purchased()
    {
        $concert = factory(Concert::class)->create()->addTickets(3);
        $order = factory(Order::class)->create();
        $order->tickets()->saveMany($concert->tickets->take(2));

        try {
            $concert->reserveTickets(2, 'john@doe.com');
        } catch (NotEnoughTicketsException $e) {
            $this->assertEquals(1, $concert->ticketsRemaining());
            return;
        }

        $this->fail('Reserving tickets succeeded even though they were already sold!');
    }

    /** @test */
    function cannot_reserve_tickets_that_have_already_been_reserved()
    {
        $concert = factory(Concert::class)->create()->addTickets(3);
        $concert->reserveTickets(2, 'john@doe.com');

        try {
            $concert->reserveTickets(2, 'john@doe.com');
        } catch (NotEnoughTicketsException $e) {
            $this->assertEquals(1, $concert->ticketsRemaining());
            return;
        }

        $this->fail('Reserving tickets succeeded even though they were already reserved!');
    }
}
