<?php

namespace App\Console\Commands;

use App\Models\AuditLog;
use App\Models\CashFlow;
use App\Models\Category;
use App\Models\Product;
use App\Models\Sale;
use App\Models\Shift;
use App\Models\StockIn;
use App\Models\Supplier;
use App\Models\Unit;
use App\Models\User;
use Illuminate\Console\Command;

class SeedDataCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:seed-data {--count=2000 : Number of records per table}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Seed the database with dummy data and a progress bar';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $count = (int) $this->option('count');

        $this->info("Seeding $count records per table...");

        $this->seedTable('Users', function () {
            User::factory()->create();
        });

        $this->seedTable('Categories', function () {
            Category::factory()->create();
        });

        $this->seedTable('Units', function () {
            Unit::factory()->create();
        });

        $this->seedTable('Suppliers', function () {
            Supplier::factory()->create();
        });

        // For tables with dependencies, we'll pick existing IDs
        $catIds = Category::pluck('id_kat')->toArray();
        $unitIds = Unit::pluck('id_satuan')->toArray();
        $this->seedTable('Products', function () use ($catIds, $unitIds) {
            Product::factory()->create([
                'id_kat' => fake()->randomElement($catIds),
                'id_satuan' => fake()->randomElement($unitIds),
            ]);
        });

        $userIds = User::pluck('id')->toArray();
        $this->seedTable('Shifts', function () use ($userIds) {
            Shift::factory()->create([
                'user_id' => fake()->randomElement($userIds),
            ]);
        });

        $supIds = Supplier::pluck('id_supplier')->toArray();
        $prodIds = Product::pluck('id_brg')->toArray();
        $this->seedTable('Stock In', function () use ($supIds, $prodIds) {
            StockIn::factory()->create([
                'id_supplier' => fake()->randomElement($supIds),
                'id_brg' => fake()->randomElement($prodIds),
            ]);
        });

        $shiftIds = Shift::pluck('id')->toArray();
        $this->seedTable('Sales', function () use ($prodIds, $userIds, $shiftIds) {
            Sale::factory()->create([
                'id_brg' => fake()->randomElement($prodIds),
                'user_id' => fake()->randomElement($userIds),
                'shift_id' => fake()->randomElement($shiftIds),
            ]);
        });

        $this->seedTable('Cash Flow', function () use ($shiftIds) {
            CashFlow::factory()->create([
                'shift_id' => fake()->randomElement($shiftIds),
            ]);
        });

        $this->seedTable('Audit Logs', function () use ($userIds) {
            AuditLog::factory()->create([
                'user_id' => fake()->randomElement($userIds),
            ]);
        });

        $this->info('Seeding completed successfully.');
    }

    private function seedTable(string $name, callable $callback)
    {
        $count = (int) $this->option('count');
        $this->info("Seeding $name...");
        $bar = $this->output->createProgressBar($count);
        $bar->start();

        for ($i = 0; $i < $count; $i++) {
            $callback();
            $bar->advance();
        }

        $bar->finish();
        $this->newLine();
        $this->info("$name seeded.");
    }
}
