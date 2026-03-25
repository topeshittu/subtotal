<?php

namespace App\Console\Commands;

use App\Utils\ModuleUtil;
use App\Utils\DemoResetHelper;
use DB;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Cache;

class CreateDummyBusiness extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'pos:dummyBusiness';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Creates a dummy business in the application';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        try {
            $this->info('Starting demo data reset...');
            
            ini_set('max_execution_time', 0);
            ini_set('memory_limit', '512M');
            $flagFile = storage_path('framework/demo_resetting.flag');
            file_put_contents($flagFile, time());
            DemoResetHelper::updateProgress('resetting'); 
            Artisan::call('cache:clear');
            DB::statement('SET FOREIGN_KEY_CHECKS = 0');
            DB::statement('SET default_storage_engine=INNODB;');
            Artisan::call('migrate:fresh', ['--path'  => 'database/migrations', '--force' => true]);
            Artisan::call('module:migrate', ['--force' => true]);
            Artisan::call('db:seed', ['--force' => true]);
            Artisan::call('db:seed', ['--class' => 'DummyBusinessSeeder','--force' => true]);
            DB::statement('SET FOREIGN_KEY_CHECKS = 1');

            (new ModuleUtil())->getModuleData('dummy_data');
            DemoResetHelper::updateProgress('finalizing');
            sleep(3);
            
        } catch (\Exception $e) {
            //
            try {
                DB::statement('SET FOREIGN_KEY_CHECKS = 1');
            } catch (\Exception $ex) {
                // Ignore if this fails
            }
            
            return 1; 
        } finally {
            DemoResetHelper::clearProgress();
            $flagFile = storage_path('framework/demo_resetting.flag');
            if (file_exists($flagFile)) {
                @unlink($flagFile);
            }
        }
        
        return 0;
    }
    
}
