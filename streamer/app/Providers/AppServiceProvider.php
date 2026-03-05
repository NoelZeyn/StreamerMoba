<?php
namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Gate;

use App\Models\Player;
use App\Models\Matchs;
use App\Models\VipWallet;

use App\Policies\PlayerPolicy;
use App\Policies\MatchPolicy;
use App\Policies\VipWalletPolicy;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        Gate::policy(Player::class, PlayerPolicy::class);
        Gate::policy(Matchs::class, MatchPolicy::class);
        Gate::policy(VipWallet::class, VipWalletPolicy::class);
    }
}