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
use App\Models\Matchs;
use App\Models\MatchPlayer;
use App\Models\Schedule;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $roleNames = ['Exp Lane', 'Mid Lane', 'Gold Lane', 'Jungling', 'Roaming'];
        $roles = [];
        foreach ($roleNames as $name) {
            $roles[] = Role::create(['name' => $name]);
        }

        $heroNames = ['Miya', 'Balmond', 'Saber', 'Alice', 'Nana', 'Tigreal', 'Alucard', 'Karina', 'Akai', 'Franco', 'Bane', 'Bruno', 'Clint', 'Rafaela', 'Eudora', 'Zilong', 'Fanny', 'Layla', 'Minotaur', 'Lolita', 'Hayabusa', 'Freya', 'Gord', 'Natalia', 'Kagura', 'Chou', 'Sun', 'Alpha', 'Ruby', 'Yi Sun-shin', 'Moskov', 'Johnson', 'Cyclops', 'Estes', 'Hilda', 'Aurora', 'Lapu-Lapu', 'Vexana', 'Roger', 'Karrie', 'Gatotkaca', 'Harley', 'Irithel', 'Grock', 'Argus', 'Odette', 'Lancelot', 'Diggie', 'Hylos', 'Zhask', 'Helcurt', 'Pharsa', 'Lesley', 'Jawhead', 'Angela', 'Gusion', 'Valir', 'Martis', 'Uranus', 'Hanabi', 'Chang’e', 'Kaja', 'Selena', 'Aldous', 'Claude', 'Vale', 'Leomord', 'Lunox', 'Hanzo', 'Belerick', 'Kimmy', 'Thamuz', 'Harith', 'Minsitthar', 'Kadita', 'Faramis', 'Badang', 'Khufra', 'Granger', 'Guinevere', 'Esmeralda', 'Terizla', 'X.Borg', 'Ling', 'Dyrroth', 'Lylia', 'Baxia', 'Masha', 'Wanwan', 'Silvanna', 'Cecilion', 'Carmilla', 'Atlas', 'Popol and Kupa', 'Yu Zhong', 'Luo Yi', 'Khaleed', 'Benedetta', 'Barats', 'Brody', 'Yve', 'Mathilda', 'Paquito', 'Gloo', 'Beatrix', 'Phoveus', 'Natan', 'Aulus', 'Aamon', 'Valentina', 'Edith', 'Floryn', 'Yin', 'Melissa', 'Xavier', 'Julian', 'Fredrinn', 'Joy', 'Novaria', 'Arlott', 'Ixia', 'Nolan', 'Cici', 'Chip', 'Zhuxin', 'Suyou', 'Lukas', 'Kalea', 'Zetian', 'Obsidia', 'Marcel'];
        $heroes = [];
        foreach ($heroNames as $name) {
            $hero = Hero::create(['name' => $name]);
            $hero->roles()->attach([$roles[array_rand($roles)]->id, $roles[array_rand($roles)]->id]);
            $heroes[] = $hero;
        }

        $streamer1 = User::create([
            'name' => "Ahmad (Streamer 1)",
            'email' => "ahmad@test.com",
            'password' => Hash::make('password123'),
            'channel_name' => "Ahmad_Gaming",
            'webhook_token' => hash('sha256', Str::uuid() . Str::random(10)),
            'sociabuzz_token' => 'sbwhook-mkn59ntl0ytrsjzlme6ivukh'
        ]);

        $streamer2 = User::create([
            'name' => "Vasta (Streamer 2)",
            'email' => "vasta@test.com",
            'password' => Hash::make('password123'),
            'channel_name' => "Vasta_Official",
            'webhook_token' => hash('sha256', Str::uuid() . Str::random(10))
        ]);

        $streamers = [$streamer1, $streamer2];

        for ($i = 1; $i <= 40; $i++) {
            Season::create([
                'name' => "Season $i",
                'start_date' => Carbon::now()->subMonths(20 - $i),
                'end_date' => Carbon::now()->subMonths(19 - $i),
                'is_active' => ($i == 40),
            ]);
        }
        $seasons = Season::all();

        $types = ['VIP', 'PUBLIC'];
        foreach ($streamers as $st) {
            for ($i = 1; $i <= 50; $i++) {
                $type = $types[array_rand($types)];
                $player = Player::create([
                    'user_id' => $st->id,
                    'name' => "Player_" . $st->id . "_$i",
                    'type' => $type,
                ]);

                if ($type == 'VIP') {
                    VipWallet::create([
                        'player_id' => $player->id,
                        'play_balance' => rand(100, 5000),
                    ]);
                }
            }
        }
        $players = Player::all();

        foreach ($streamers as $st) {
            Schedule::create([
                'user_id' => $st->id,
                'title' => "Mabar Bar-Bar with " . $st->name,
                'start_time' => Carbon::now(),
                'status' => 'live',
                'notes' => "Live sekarang!",
            ]);

            $otherStatuses = ['scheduled', 'finished', 'cancelled'];
            for ($j = 1; $j <= 5; $j++) {
                $status = $otherStatuses[array_rand($otherStatuses)];
                Schedule::create([
                    'user_id' => $st->id,
                    'title' => "Session $status $j",
                    'start_time' => $status == 'scheduled' ? Carbon::now()->addDays($j) : Carbon::now()->subDays($j),
                    'end_time' => $status == 'finished' ? Carbon::now()->subDays($j)->addHours(2) : null,
                    'status' => $status,
                    'notes' => "Note for $status session",
                ]);
            }
        }
        $schedules = Schedule::all();

        $validSchedules = $schedules->whereIn('status', ['live', 'finished']);
        
        for ($i = 1; $i <= 100; $i++) {
            $randomSchedule = $validSchedules->random();
            $match = Matchs::create([
                'user_id' => $randomSchedule->user_id,
                'season_id' => $seasons->random()->id,
                'schedule_id' => $randomSchedule->id,
                'played_at' => Carbon::now()->subMinutes(rand(1, 1000)),
            ]);

            for ($j = 1; $j <= 5; $j++) {
                MatchPlayer::create([
                    'match_id' => $match->id,
                    'player_id' => $players->where('user_id', $match->user_id)->random()->id,
                    'hero_id' => $heroes[array_rand($heroes)]->id,
                    'role_id' => $roles[array_rand($roles)]->id,
                ]);
            }
        }
    }
}