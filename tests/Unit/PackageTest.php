<?php

namespace Tests\Unit;

use App\Models\Package;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class PackageTest extends TestCase
{
    #[Test]
    public function it_calculates_the_total_monthly_fee(): void
    {
        $package = new Package([
            'nom' => 'Standard',
            'frais_scolarite' => 120,
            'frais_dejeuner' => 80,
            'frais_activite' => 35.5,
            'is_active' => true,
        ]);

        $this->assertSame(235.5, $package->total_mensuel);
    }
}