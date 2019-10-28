<?php
/**
 * Author: Marek Dočekal
 * Licence: WTFPL v2
 */

namespace Kazlik\SchemaBuilder\Command;

use Illuminate\Console\Command;
use Kazlik\Schemabuilder\IApplyTableChangesService;

class SchemaUpdateCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'schema:update';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update schema changes';


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
    public function handle( IApplyTableChangesService $ApplyTableChangesService )
    {
        $ApplyTableChangesService->applyAllChanges();
    }
}
