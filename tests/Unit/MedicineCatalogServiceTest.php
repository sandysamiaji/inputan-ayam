<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;
use App\Services\MedicineCatalogService;

class MedicineCatalogServiceTest extends TestCase
{
    public function test_categories_returns_8_categories(): void
    {
        $categories = MedicineCatalogService::getCategories();
        $this->assertCount(8, $categories);
        $this->assertArrayHasKey('obat_cacing', $categories);
        $this->assertArrayHasKey('antibiotik', $categories);
        $this->assertArrayHasKey('antikoksidia', $categories);
        $this->assertArrayHasKey('vitamin', $categories);
        $this->assertArrayHasKey('vaksin', $categories);
        $this->assertArrayHasKey('mineral', $categories);
        $this->assertArrayHasKey('disinfektan', $categories);
        $this->assertArrayHasKey('obat', $categories);
    }

    public function test_base_medicines_has_26_products(): void
    {
        $base = MedicineCatalogService::getBaseMedicines();
        $this->assertCount(26, $base);
    }

    public function test_all_medicines_without_livestock_has_attributes(): void
    {
        $meds = MedicineCatalogService::getAllMedicines(false);
        $this->assertCount(26, $meds);

        $vermixon = $meds[0];
        $this->assertEquals('Vermixon', $vermixon['name']);
        $this->assertEquals(12, $vermixon['initial_stock']);
        $this->assertEquals(12, $vermixon['total_masuk']);
        $this->assertEquals(0, $vermixon['total_keluar']);
        $this->assertEquals(12, $vermixon['stock']);
        $this->assertEquals('aman', $vermixon['status']);
    }

    public function test_match_medicine_id(): void
    {
        $base = MedicineCatalogService::getBaseMedicines();

        $this->assertEquals(1, MedicineCatalogService::matchMedicineId('Vermixon', $base));
        $this->assertEquals(1, MedicineCatalogService::matchMedicineId('Pembelian Obat Vermixon', $base));
        $this->assertEquals(2, MedicineCatalogService::matchMedicineId('Wormzol-K', $base));
        $this->assertEquals(11, MedicineCatalogService::matchMedicineId('Vita Stress', $base));
        $this->assertEquals(13, MedicineCatalogService::matchMedicineId('Vitamin B Complex', $base));
        $this->assertEquals(16, MedicineCatalogService::matchMedicineId('ND Lasota', $base));
        $this->assertEquals(17, MedicineCatalogService::matchMedicineId('ND IB Vaccine', $base));
        $this->assertEquals(20, MedicineCatalogService::matchMedicineId('Medivac AI (Flu Burung)', $base));
        $this->assertEquals(21, MedicineCatalogService::matchMedicineId('Kalsium Premix Layer', $base));
        $this->assertEquals(24, MedicineCatalogService::matchMedicineId('Medisep Disinfektan', $base));
    }

    public function test_medicine_summary(): void
    {
        $meds = MedicineCatalogService::getAllMedicines(false);
        $summary = MedicineCatalogService::getMedicineSummary($meds);

        $this->assertEquals(26, $summary['total_products']);
        $this->assertGreaterThan(0, $summary['total_initial']);
        $this->assertGreaterThan(0, $summary['total_stock']);
        $this->assertEquals(0, $summary['total_keluar']);
        $this->assertGreaterThan(0, $summary['safe_count']);
        $this->assertArrayHasKey('total_stock_consumed', $summary);
        $this->assertArrayHasKey('total_deficit', $summary);
        $this->assertArrayHasKey('deficit_products', $summary);
        $this->assertArrayHasKey('deficit_products_count', $summary);
    }

    public function test_find_medicine_by_name(): void
    {
        $med = MedicineCatalogService::findMedicineByName('Vermixon');
        $this->assertNotNull($med);
        $this->assertEquals('Vermixon', $med['name']);
        $this->assertEquals('Botol', $med['unit']);

        $med2 = MedicineCatalogService::findMedicineByName('ND IB Vaccine');
        $this->assertNotNull($med2);
        $this->assertEquals('ND IB Vaccine', $med2['name']);
    }

    public function test_parse_dosage_quantity(): void
    {
        $this->assertEquals(2.0, MedicineCatalogService::parseDosageQuantity('2 Botol (2000 dosis)'));
        $this->assertEquals(1.0, MedicineCatalogService::parseDosageQuantity('1000 dosis'));
        $this->assertEquals(2.0, MedicineCatalogService::parseDosageQuantity('2000 dosis'));
        $this->assertEquals(5.0, MedicineCatalogService::parseDosageQuantity('5 Botol'));
        $this->assertEquals(1.5, MedicineCatalogService::parseDosageQuantity('1.5 Liter'));
        $this->assertEquals(3.0, MedicineCatalogService::parseDosageQuantity('3 Box'));
        $this->assertEquals(1.0, MedicineCatalogService::parseDosageQuantity(null));
        $this->assertEquals(1.0, MedicineCatalogService::parseDosageQuantity(''));
    }

    public function test_medicine_category_keys(): void
    {
        $keys = MedicineCatalogService::getMedicineCategoryKeys();
        $this->assertContains('obat', $keys);
        $this->assertContains('vaksin', $keys);
        $this->assertContains('vitamin', $keys);
    }
}

