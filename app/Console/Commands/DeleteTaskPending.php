<?php

namespace App\Console\Commands;

use App\Models\Task;
use Illuminate\Console\Command;

class DeleteTaskPending extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:deletetask';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Borra todas las tareas que están en sofdeleted';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // Borrar tareas que deleted_at sea mayor a 5 dias
        Task::where('deleted_at','!=',null)
            ->where('deleted_at','<',now()->subDays(5))
            ->forceDelete();
    }
}
