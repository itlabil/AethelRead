<?php

namespace Database\Seeders;

use App\Models\Alias;
use App\Models\Description;
use App\Models\Entity;
use App\Models\Keyword;
use App\Models\Novel;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class EntitySeeder extends Seeder
{
    private array $characterTitles = [
        'The Heavenly Demon', 'Sword Saint', 'Spear Master', 'Divine Dragon',
        'Shadow Monarch', 'Ice Queen', 'Lightning King', 'Blood Demon',
        'Flame Emperor', 'Void Walker', 'Star Destroyer', 'Moon Empress',
        'Iron Fist', 'Silver Wolf', 'Golden Eagle', 'Dark Phoenix',
        'Thunder God', 'Earth Shaker', 'Wind Dancer', 'Water Sage',
    ];

    private array $placeTypes = [
        'Kingdom', 'Empire', 'Dungeon', 'Academy', 'Mountain', 'Forest',
        'City', 'Village', 'Temple', 'Castle', 'Tower', 'Abyss',
        'Realm', 'Domain', 'Sect', 'Gate', 'Labyrinth', 'Sanctuary',
        'Fortress', 'Ruins',
    ];

    private array $itemTypes = [
        'Sword', 'Spear', 'Shield', 'Armor', 'Ring', 'Necklace',
        'Scroll', 'Potion', 'Artifact', 'Tome', 'Staff', 'Bow',
        'Dagger', 'Orb', 'Crystal', 'Rune', 'Talisman', 'Elixir',
        'Gem', 'Pill',
    ];

    private array $adjectives = [
        'Ancient', 'Divine', 'Sacred', 'Cursed', 'Legendary', 'Mythical',
        'Eternal', 'Celestial', 'Abyssal', 'Primordial', 'Supreme', 'Ultimate',
        'Forbidden', 'Transcendent', 'Immortal', 'Demonic', 'Holy', 'Shadow',
        'Blazing', 'Frozen',
    ];

    private array $descriptionTemplates = [
        'en' => [
            'character' => [
                'A powerful martial artist who rose from humble beginnings to become one of the strongest in the realm.',
                'A mysterious figure with a dark past, wielding unparalleled strength and wisdom.',
                'Once a weakling, this warrior overcame countless trials to reach the peak of power.',
                'A prodigy blessed with extraordinary talent, feared and respected by all who know them.',
                'An enigmatic warrior whose true strength remains unknown even to their closest allies.',
            ],
            'place' => [
                'A vast and dangerous territory filled with powerful monsters and hidden treasures.',
                'A sacred location where ancient power still lingers, attracting warriors from across the land.',
                'A legendary place that has witnessed countless battles throughout history.',
                'A mysterious realm that exists between dimensions, accessible only to the strongest warriors.',
                'An ancient stronghold that has stood for thousands of years, protecting its inhabitants.',
            ],
            'item' => [
                'A legendary weapon forged by ancient craftsmen using materials from another world.',
                'An artifact imbued with immense power, capable of turning the tide of any battle.',
                'A rare treasure that enhances the wielder abilities beyond normal human limits.',
                'An ancient relic whose true power has yet to be fully unlocked by any warrior.',
                'A sacred item passed down through generations, carrying the will of its original owner.',
            ],
        ],
        'id' => [
            'character' => [
                'Seorang praktisi bela diri yang bangkit dari latar belakang sederhana menjadi salah satu yang terkuat.',
                'Sosok misterius dengan masa lalu kelam yang menggunakan kekuatan dan kebijaksanaan tak tertandingi.',
                'Dulunya lemah, pejuang ini mengatasi berbagai ujian untuk mencapai puncak kekuatan.',
                'Seorang jenius berbakat luar biasa yang ditakuti dan dihormati oleh semua orang.',
                'Pejuang enigmatis yang kekuatan sejatinya tetap tidak diketahui bahkan oleh sekutu terdekatnya.',
            ],
            'place' => [
                'Wilayah luas dan berbahaya yang dipenuhi monster kuat dan harta tersembunyi.',
                'Lokasi suci di mana kekuatan kuno masih ada, menarik pejuang dari seluruh penjuru.',
                'Tempat legendaris yang telah menyaksikan pertempuran tak terhitung sepanjang sejarah.',
                'Alam misterius yang ada di antara dimensi, hanya dapat diakses oleh pejuang terkuat.',
                'Benteng kuno yang telah berdiri selama ribuan tahun, melindungi penghuninya.',
            ],
            'item' => [
                'Senjata legendaris yang ditempa oleh pengrajin kuno menggunakan bahan dari dunia lain.',
                'Artefak yang dipenuhi kekuatan luar biasa, mampu membalikkan keadaan pertempuran.',
                'Harta langka yang meningkatkan kemampuan pemakainya melampaui batas manusia normal.',
                'Relik kuno yang kekuatan sejatinya belum sepenuhnya dibuka oleh pejuang manapun.',
                'Benda suci yang diturunkan dari generasi ke generasi, membawa kehendak pemilik aslinya.',
            ],
        ],
    ];

    public function run(): void
    {
        $novels  = Novel::all();
        $total   = 0;

        foreach ($novels as $novel) {
            // Setiap novel dapat 50-60 entities
            $count = rand(50, 60);

            for ($i = 0; $i < $count; $i++) {
                $type = $this->randomType();
                $name = $this->generateName($type, $novel->name, $i);

                // Cek duplikasi slug
                $slug = Str::slug($name);
                if (Entity::where('slug', $slug)->exists()) {
                    $slug = $slug . '-' . Str::random(4);
                    $name = $name . ' ' . Str::random(4);
                }

                $entityData = [
                    'novel_id'  => $novel->id,
                    'type'      => $type,
                    'name'      => $name,
                    'slug'      => $slug,
                    'is_active' => rand(0, 9) > 1, // 80% active
                ];

                $entityData['hash'] = hash('sha256', json_encode($entityData));

                $entity = Entity::create($entityData);

                // Tambah aliases (2-4 per entity)
                $aliasCount = rand(2, 4);
                $aliases    = [];
                for ($j = 0; $j < $aliasCount; $j++) {
                    $aliasName = $this->generateAlias($type, $j);
                    if (! in_array($aliasName, $aliases)) {
                        $aliases[] = $aliasName;
                        Alias::create([
                            'id'        => (string) Str::orderedUuid(),
                            'entity_id' => $entity->id,
                            'name'      => $aliasName,
                        ]);
                    }
                }

                // Tambah keywords (3-5 per entity)
                $keywordCount = rand(3, 5);
                $keywords     = [];
                for ($k = 0; $k < $keywordCount; $k++) {
                    $keyword = strtolower(explode(' ', $name)[array_rand(explode(' ', $name))]);
                    if (! in_array($keyword, $keywords) && strlen($keyword) > 2) {
                        $keywords[] = $keyword;
                        Keyword::create([
                            'id'        => (string) Str::orderedUuid(),
                            'entity_id' => $entity->id,
                            'keyword'   => $keyword,
                        ]);
                    }
                }

                // Tambah descriptions (EN & ID)
                $descEn = $this->descriptionTemplates['en'][$type][array_rand($this->descriptionTemplates['en'][$type])];
                $descId = $this->descriptionTemplates['id'][$type][array_rand($this->descriptionTemplates['id'][$type])];

                Description::create([
                    'id'        => (string) Str::orderedUuid(),
                    'entity_id' => $entity->id,
                    'locale'    => 'en',
                    'content'   => $descEn . ' ' . $name . ' is one of the most notable figures in ' . $novel->name . '.',
                ]);

                Description::create([
                    'id'        => (string) Str::orderedUuid(),
                    'entity_id' => $entity->id,
                    'locale'    => 'id',
                    'content'   => $descId . ' ' . $name . ' adalah salah satu tokoh paling terkenal di ' . $novel->name . '.',
                ]);

                $total++;
            }

            $this->command->info("✓ {$novel->name} — {$count} entities seeded.");
        }

        $this->command->info("✓ Total {$total} entities seeded.");
    }

    private function randomType(): string
    {
        $types = ['character', 'character', 'character', 'place', 'place', 'item'];
        return $types[array_rand($types)];
    }

    private function generateName(string $type, string $novelName, int $index): string
    {
        return match ($type) {
            'character' => $this->adjectives[array_rand($this->adjectives)] . ' '
                         . $this->characterTitles[array_rand($this->characterTitles)]
                         . ' ' . ($index + 1),
            'place'     => $this->adjectives[array_rand($this->adjectives)] . ' '
                         . $this->placeTypes[array_rand($this->placeTypes)]
                         . ' of ' . explode(' ', $novelName)[0],
            'item'      => $this->adjectives[array_rand($this->adjectives)] . ' '
                         . $this->itemTypes[array_rand($this->itemTypes)]
                         . ' of ' . explode(' ', $novelName)[0],
            default     => 'Unknown Entity ' . $index,
        };
    }

    private function generateAlias(string $type, int $index): string
    {
        $prefixes = ['The', 'Great', 'Mighty', 'Legendary', 'Ancient'];
        $suffixes = match ($type) {
            'character' => ['Warrior', 'Master', 'Lord', 'King', 'Emperor', 'Sage'],
            'place'     => ['Land', 'Territory', 'Zone', 'Area', 'Region'],
            'item'      => ['Blade', 'Relic', 'Treasure', 'Artifact', 'Heirloom'],
            default     => ['Entity'],
        };

        return $prefixes[array_rand($prefixes)] . ' ' . $suffixes[array_rand($suffixes)] . ' ' . ($index + 1);
    }
}