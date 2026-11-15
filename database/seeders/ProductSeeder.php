<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;
use App\Models\Product;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // --- Kategorie (firstOrCreate = utwórz jeśli nie ma, inaczej pobierz) ---
        $hardCheese = Category::firstOrCreate(['slug' => 'sery-twarde'], ['name' => 'Sery twarde']);
        $moldCheese = Category::firstOrCreate(['slug' => 'sery-plesniowe'], ['name' => 'Sery pleśniowe']);
        $goatCheese = Category::firstOrCreate(['slug' => 'sery-kozie'], ['name' => 'Sery kozie']);

        // Lorem ipsum wrzucany jako opis każdego sera
        $lorem = 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. '
            . 'Sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. '
            . 'Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. '
            . 'Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur.';

        // --- Dane serów: [nazwa, slug, cena w groszach (za 100g), stan w gramach] ---
        $cheeses = [
            // Sery twarde (kategoria: $hardCheese)
            $hardCheese->id => [
                ['ser cheddar', 'ser-cheddar', 2599, 3200],
                ['Parmezan', 'parmezan', 4999, 1800],
                ['Gouda', 'gouda', 2299, 5400],
                ['Edam', 'edam', 1999, 4800],
                ['Emmentaler', 'emmentaler', 3199, 2600],
                ['Grana Padano', 'grana-padano', 4599, 1500],
                ['Pecorino Romano', 'pecorino-romano', 4799, 900],
                ['Manchego', 'manchego', 4399, 150],
                ['Comté', 'comte', 5299, 1200],
                ['Gruyère', 'gruyere', 4899, 2100],
                ['Provolone', 'provolone', 2899, 3000],
                ['Cheddar wędzony', 'cheddar-wedzony', 2999, 2400],
                ['Maasdam', 'maasdam', 2499, 4200],
                ['Asiago', 'asiago', 3899, 1700],
                ['Leerdammer', 'leerdammer', 2699, 3600],
            ],
            // Sery pleśniowe (kategoria: $moldCheese)
            $moldCheese->id => [
                ['Gorgonzola', 'gorgonzola', 3899, 2000],
                ['Brie', 'brie', 2999, 2800],
                ['Camembert', 'camembert', 3199, 2500],
                ['Roquefort', 'roquefort', 5499, 1100],
                ['Lazur', 'lazur', 2799, 3400],
                ['Stilton', 'stilton', 5199, 180],
                ["Bleu d'Auvergne", 'bleu-d-auvergne', 4699, 900],
                ['Danablu', 'danablu', 3499, 2200],
                ['Brie z orzechami', 'brie-z-orzechami', 3699, 1600],
                ['Cambozola', 'cambozola', 3999, 1400],
                ["Fourme d'Ambert", 'fourme-d-ambert', 4499, 1300],
                ['Gorgonzola Dolce', 'gorgonzola-dolce', 4199, 1900],
                ['Bresse Bleu', 'bresse-bleu', 3799, 2100],
            ],
            // Sery kozie (kategoria: $goatCheese)
            $goatCheese->id => [
                ['Ser kozi', 'ser-kozi', 5299, 1500],
                ['Chèvre', 'chevre', 4899, 1200],
                ['Crottin de Chavignol', 'crottin-de-chavignol', 5599, 140],
                ['Ser kozi z ziołami', 'ser-kozi-z-ziolami', 5499, 1000],
                ['Bûcheron', 'bucheron', 5799, 800],
                ['Valençay', 'valencay', 5999, 700],
                ['Ser kozi wędzony', 'ser-kozi-wedzony', 5699, 950],
                ['Humboldt Fog', 'humboldt-fog', 6499, 600],
                ['Sainte-Maure', 'sainte-maure', 5899, 850],
                ['Ser kozi dojrzewający', 'ser-kozi-dojrzewajacy', 6199, 650],
                ['Feta kozia', 'feta-kozia', 3999, 2600],
                ['Ricotta kozia', 'ricotta-kozia', 2999, 3100],
            ],
        ];

        // --- Zapis: dla każdej kategorii przechodzimy po jej serach ---
        foreach ($cheeses as $categoryId => $items) {
            foreach ($items as [$name, $slug, $price, $stock]) {
                Product::updateOrCreate(
                    ['slug' => $slug], // po czym szukamy (klucz unikalny)
                    [                  // co ustawiamy / aktualizujemy
                        'category_id'     => $categoryId,
                        'name'            => $name,
                        'description'     => $lorem,
                        'price'           => $price,
                        'unit'            => 'kg',
                        'weight_per_unit' => 1,
                        'stock_quantity'  => $stock,
                        'min_portion'     => 100,
                    ]
                );
            }
        }
    }
}
