<?php

namespace App\Console\Commands;


use App\Models\Admin;
use Illuminate\Console\Command;

class InitSuperAdmin extends Command
{

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'create:create-super-administrator';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create One Super Administrator';

    /**
     *
     */

    public function handle()
    {
        if (Admin::whereType(Admin::TYPE_SUPER_ADMINISTRATOR)->exists()) {
            $this->warn('Chief Super Admin Exists');
            return;
        }
        $mobile = $this->ask('Please Input Mobile:');
        if (!(new Admin())->setAttribute('username', $mobile)
            ->setAttribute('nickname', '超级管理员')
            ->setAttribute('password', \Hash::make('123456'))
            ->setAttribute('type', Admin::TYPE_SUPER_ADMINISTRATOR)
            ->save()) {
            $this->warn('Write Chief Super Administrator Failure');
            return;
        }
        $this->info('Write Chief Super Administrator Success');
    }
}
