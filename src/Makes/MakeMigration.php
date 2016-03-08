<?php
/**
 * Created by PhpStorm.
 * User: fernandobritofl
 * Date: 4/22/15
 * Time: 10:34 PM

マイグレーションに関する処理を担当
・出力先の決定
・スタブコントローラーへの発注、データの受け取り
・実際の出力
・終了メッセージ

 */

namespace dogears\L5scaffold\Makes;

use Illuminate\Filesystem\Filesystem;
use dogears\L5scaffold\Commands\ScaffoldMakeCommand;
use dogears\L5scaffold\Stubs\StubController;
use dogears\L5scaffold\Traits\MakerTrait;
use dogears\L5scaffold\Traits\NameSolverTrait;

class MakeMigration {
    use MakerTrait,NameSolverTrait;

    protected $files;
    protected $scaffoldCommandObj;

    public function __construct(ScaffoldMakeCommand $scaffoldCommand, Filesystem $files)
    {
        $this->files = $files;
        $this->scaffoldCommandObj = $scaffoldCommand;
        $this->start();
    }

    protected function start(){

        //get_stub_path and filename
        $stub_path = __DIR__.'/../Stubs/migrate/';
        $stub_filename = 'app.stub';

        //set custom replace
        $custom_replace = [
            'table' => $this->solveName($this->scaffoldCommandObj->argument('name'), config('l5scaffold.app_name_rules.name_names')),
        ];

        //create new stub
        $stub = new StubController( $this->scaffoldCommandObj, $this->files, $stub_path.$stub_filename, $schema_repalce_type = 'migration', $custom_replace );

        //compile
        $stub_compiled = $stub->getCompiled();

        //get output_path and filename
        $output_path = './database/migrations/';
        $output_filename = date('Y_m_d_His'). '_create_'. $this->solveName($this->scaffoldCommandObj->argument('name'), config('l5scaffold.app_name_rules.app_migrate_filename')). '_table.php';

        //output_exist_check
        if( $this->files->exists($output_path.$output_filename) ){
            return $this->scaffoldCommandObj->error($this->type.' already exists!');
        }

        //output
        $this->makeDirectory($output_path);
        $this->files->put($output_path.$output_filename, $stub_compiled);

        //end message
        $this->scaffoldCommandObj->info('Migration created successfully');
    }
}