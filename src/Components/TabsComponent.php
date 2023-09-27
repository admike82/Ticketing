<?php

namespace App\Components;

use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent]
class TabsComponent
{
  public array $routes = [
    [
      'path' => 'app_dashboard',
      'label' => 'Mes tickets'
    ],
    [
      'path' => 'app_dashboard_applications',
      'label' => 'Mes applications'
    ]
  ];
}
