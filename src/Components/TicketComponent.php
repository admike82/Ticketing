<?php
namespace App\Components;

use App\Entity\Ticket;
use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent]
class TicketComponent
{
  public Ticket $ticket;
  public bool $hidden = false;
}