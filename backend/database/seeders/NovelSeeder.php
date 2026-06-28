<?php

namespace Database\Seeders;

use App\Models\Novel;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class NovelSeeder extends Seeder
{
    private array $novels = [
        ['name' => 'Nano Machine',              'type' => 'manhwa'],
        ['name' => 'Solo Leveling',             'type' => 'manhwa'],
        ['name' => 'Omniscient Reader',         'type' => 'manhwa'],
        ['name' => 'Return of the Blossoming Blade', 'type' => 'manhwa'],
        ['name' => 'The Beginning After The End', 'type' => 'manhwa'],
        ['name' => 'Overgeared',                'type' => 'manhwa'],
        ['name' => 'Tower of God',              'type' => 'manhwa'],
        ['name' => 'Legend of the Northern Blade', 'type' => 'manhwa'],
        ['name' => 'Volcanic Age',              'type' => 'manhwa'],
        ['name' => 'Martial Peak',              'type' => 'manhua'],
        ['name' => 'Apotheosis',                'type' => 'manhua'],
        ['name' => 'Against the Gods',          'type' => 'manhua'],
        ['name' => 'Battle Through the Heavens', 'type' => 'manhua'],
        ['name' => 'Cultivation Chat Group',    'type' => 'manhua'],
        ['name' => 'Tales of Demons and Gods',  'type' => 'manhua'],
        ['name' => 'Mushoku Tensei',            'type' => 'manga'],
        ['name' => 'That Time I Got Reincarnated as a Slime', 'type' => 'manga'],
        ['name' => 'Overlord',                  'type' => 'manga'],
        ['name' => 'Re:Zero',                   'type' => 'manga'],
        ['name' => 'Sword Art Online',          'type' => 'manga'],
    ];

    public function run(): void
    {
        foreach ($this->novels as $novel) {
            Novel::create([
                'name'      => $novel['name'],
                'type'      => $novel['type'],
                'hash'      => hash('sha256', $novel['name']),
                'is_active' => true,
            ]);
        }

        $this->command->info('✓ ' . count($this->novels) . ' novels seeded.');
    }
}