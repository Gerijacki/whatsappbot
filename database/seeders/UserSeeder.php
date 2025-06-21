<?php

namespace Database\Seeders;

use App\Models\Child;
use App\Models\Error;
use App\Models\Notification;
use App\Models\Policy;
use App\Models\User;
use App\Models\UserSetting;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        User::factory(10)->create()->each(function (User $user) {
        });
    }
}
