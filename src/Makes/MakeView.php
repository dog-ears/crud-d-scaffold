<?php
/**
 * Created by PhpStorm.
 * User: fernandobritofl
 * Date: 4/21/15
 * Time: 4:58 PM

ビューに関する処理を担当
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

use dogears\L5scaffold\Migrations\SchemaParser;
use dogears\L5scaffold\Migrations\SyntaxBuilder;

class MakeView
{
    use MakerTrait,NameSolverTrait;

    protected $files;
    protected $scaffoldCommandObj;
    protected $viewName;

    public function __construct(ScaffoldMakeCommand $scaffoldCommand, Filesystem $files, $viewName)
    {
        $this->files = $files;
        $this->scaffoldCommandObj = $scaffoldCommand;
        $this->viewName = $viewName;
        $this->start();
    }

    private function start()
    {
        //get_stub_path and filename
        $stub_path = __DIR__.'/../Stubs/view_app/';
        $stub_filename = $this->viewName.'.stub';

        //create custom parameter
        if ($schema = $this->scaffoldCommandObj->option('schema')) {
            $schemaArray = (new SchemaParser)->parse($schema);
        }

        $custom_replace = [
            'index' => [
                'header_fields' => (new SyntaxBuilder)->create($schemaArray, $this->scaffoldCommandObj->getMeta(), 'view-index-header'),
                'content_fields' => (new SyntaxBuilder)->create($schemaArray, $this->scaffoldCommandObj->getMeta(), 'view-index-content'),
            ],
            'show' => [
                'content_fields' => (new SyntaxBuilder)->create($schemaArray, $this->scaffoldCommandObj->getMeta(), 'view-show-content'),
            ],
            'create' => [
                'content_fields' => (new SyntaxBuilder)->create($schemaArray, $this->scaffoldCommandObj->getMeta(), 'view-create-content', $this->scaffoldCommandObj->option('form')),
            ],
            'edit' => [
                'content_fields' => (new SyntaxBuilder)->create($schemaArray, $this->scaffoldCommandObj->getMeta(), 'view-edit-content', $this->scaffoldCommandObj->option('form')),
            ]
        ];

        //create new stub
        $stub = new StubController( $this->scaffoldCommandObj, $this->files, $stub_path.$stub_filename, $schema_repalce_type = null, $custom_replace[$this->viewName]);

        //compile
        $stub_compiled = $stub->getCompiled();

        //get output_path and filename
        $output_path = './resources/views/'.$this->solveName($this->scaffoldCommandObj->argument('name'), config('l5scaffold.app_name_rules.app_model_vars')).'/';
        $output_filename = $this->viewName.'.blade.php';

        //output_func
        $output_func = function () use($output_path, $output_filename, $stub_compiled){

            //output
            $this->makeDirectory($output_path.$output_filename);
            $this->files->put($output_path.$output_filename, $stub_compiled);            

            //end message
            $this->scaffoldCommandObj->info('View-'.$this->viewName.' created successfully');
        };

        //output_exist_check
        if( $this->files->exists($output_path.$output_filename) ){
            if ($this->scaffoldCommandObj->confirm($output_path.$output_filename. ' already exists! Do you wish to overwrite? [yes|no]')) {

                //call output_func
                $output_func();
            }
        }else{
            //call output_func
            $output_func();
        }
    }
}