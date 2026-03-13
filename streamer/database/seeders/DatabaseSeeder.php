<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Season;
use App\Models\Hero;
use App\Models\Role;
use App\Models\Player;
use App\Models\VipWallet;
use App\Models\Transaction;
use App\Models\StreamSchedule;
use App\Models\Match;
use App\Models\MatchPlayer;
use App\Models\Matchs;
use App\Models\Schedule;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $roles = [];
        $roleNames = ['Tank', 'Fighter', 'Assassin', 'Mage', 'Marksman', 'Support', 'Roamer', 'Jungler', 'Exp Laner', 'Gold Laner', 'Initiator', 'Burst', 'Crowd Control', 'Poke', 'Siege', 'Utility', 'Healer', 'Buffer', 'Debuffer', 'Ganker'];
        foreach ($roleNames as $name) {
            $roles[] = Role::create(['name' => $name]);
        }

        $heroes = [];
        $heroNames = ['Tigreal', 'Alucard', 'Miya', 'Eudora', 'Zilong', 'Layla', 'Balmond', 'Saber', 'Nana', 'Bruno', 'Clint', 'Rafaela', 'Bane', 'Akai', 'Franco', 'Karina', 'Hayabusa', 'Chou', 'Kagura', 'Alice'];
        foreach ($heroNames as $name) {
            $hero = Hero::create(['name' => $name]);
            $hero->roles()->attach([$roles[array_rand($roles)]->id, $roles[array_rand($roles)]->id]);
            $heroes[] = $hero;
        }

        for ($i = 1; $i <= 20; $i++) {
            User::create([
                'name' => "Admin $i",
                'email' => "admin$i@example.com",
                'password' => Hash::make('password'),
                'channel_name' => "Channel_Gaming_$i",
                'webhook_token' => hash('sha256', Str::uuid() . Str::random(10))
            ]);
        }
        $users = User::all();

        for ($i = 1; $i <= 500; $i++) {
            Season::create([
                'name' => "Season $i",
                'start_date' => Carbon::now()->subMonths(20 - $i),
                'end_date' => Carbon::now()->subMonths(19 - $i),
                'is_active' => ($i == 20),
            ]);
        }
        $seasons = Season::all();

        $types = ['VIP', 'PUBLIC', 'STREAMER'];
        for ($i = 1; $i <= 500; $i++) {
            $type = $types[array_rand($types)];
            $player = Player::create([
                'user_id' => ($type == 'STREAMER') ? $users->random()->id : null,
                'name' => "PlayerName_$i",
                'type' => $type,
            ]);

            if ($type == 'VIP') {
                VipWallet::create([
                    'player_id' => $player->id,
                    'play_balance' => rand(100, 5000),
                ]);
            }
        }
        $players = Player::all();

        for ($i = 1; $i <= 500; $i++) {
            Transaction::create([
                'user_id' => $users->random()->id,
                'player_id' => $players->where('type', 'VIP')->random()->id,
                'quantity' => rand(1, 10),
                'price' => rand(10000, 100000),
            ]);
        }
        User::create([
            'name' => "ahmad",
            'email' => "ahmad@test.com",
            'password' => Hash::make('password123'),
            'channel_name' => "Channel_Gaming_21",
            'webhook_token' => hash('sha256', Str::uuid() . Str::random(10))
        ]);
        $statuses = ['scheduled', 'live', 'finished', 'cancelled'];

        for ($i = 1; $i <= 50; $i++) {

            $status = $statuses[array_rand($statuses)];

            Schedule::create([
                'user_id' => 21,
                'title' => "Live Stream Session $i",
                'start_time' => Carbon::now()->addDays($i),
                'status' => $status,
                'notes' => "Note for stream $i",
                'end_time' => $status === 'finished'
                    ? Carbon::now()->addDays($i)->addHours(3)
                    : null,
            ]);
        }
        $schedules = Schedule::all();

        for ($i = 1; $i <= 500; $i++) {
            Matchs::create([
                'user_id' => $users->random()->id,
                'season_id' => $seasons->random()->id,
                'schedule_id' => $schedules->whereIn('status', ['live', 'finished'])->random()->id,
                'played_at' => Carbon::now()->subDays(rand(1, 30)),
            ]);
        };

        $matches = Matchs::all();

        foreach ($matches as $match) {
            for ($j = 1; $j <= 5; $j++) {
                MatchPlayer::create([
                    'match_id' => $match->id,
                    'player_id' => $players->random()->id,
                    'hero_id' => $heroes[array_rand($heroes)]->id,
                    'role_id' => $roles[array_rand($roles)]->id,
                ]);
            }
        }
    }
}
