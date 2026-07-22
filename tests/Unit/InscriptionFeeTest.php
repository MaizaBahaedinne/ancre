<?php

namespace Tests\Unit;

use App\Models\Inscription;
use App\Models\Package;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class InscriptionFeeTest extends TestCase
{
    #[Test]
    public function it_resolves_total_from_snapshots_when_present(): void
    {
        $inscription = new Inscription([
            'annual_registration_fee' => 50,
            'package_monthly_total' => 220,
            'total_amount' => 270,
        ]);

        $this->assertSame(50.0, $inscription->resolved_annual_registration_fee);
        $this->assertSame(220.0, $inscription->resolved_package_monthly_total);
        $this->assertSame(270.0, $inscription->resolved_total_amount);
    }

    #[Test]
    public function it_falls_back_to_package_total_when_snapshots_are_missing(): void
    {
        $inscription = new Inscription([
            'annual_registration_fee' => 0,
            'package_monthly_total' => 0,
            'total_amount' => 0,
        ]);
        $inscription->setRelation('package', new Package([
            'nom' => 'Premium',
            'frais_scolarite' => 100,
            'frais_dejeuner' => 80,
            'frais_activite' => 20,
            'is_active' => true,
        ]));

        $this->assertSame(200.0, $inscription->resolved_package_monthly_total);
        $this->assertSame(200.0, $inscription->resolved_total_amount);
    }
}