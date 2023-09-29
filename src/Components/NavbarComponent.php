<?php
namespace App\Components;

use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent]
class NavbarComponent
{
  public array $routes = [
    [
      'path' => 'app_dashboard_tickets',
      'label' => 'Mes tickets'
    ],
    [
      'path' => 'app_dashboard_applications',
      'label' => 'Mes applications'
    ]
  ];
}