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
use dogears\L5scaffold\Traits\OutputTrait;

class MakeView
{
    use MakerTrait,NameSolverTrait,OutputTrait;

    protected $files;
    protected $commandObj;
    protected $viewName;

    public function __construct($command, Filesystem $files, $viewName)
    {
        $this->files = $files;
        $this->commandObj = $command;
        $this->viewName = $viewName;
        $this->start();
    }

    private function start()
    {

        //get_stub_path and filename
        $stub_path = __DIR__.'/../Stubs/view_app/';
        $stub_filename = $this->viewName.'.stub';

        //create custom parameter
        if ($schema = $this->commandObj->option('schema')) {
            $schemaArray = (new SchemaParser)->parse($schema);
        }

        $custom_replace = [
            'index' => [
                'search_fields' => (new SyntaxBuilder)->create($schemaArray, $this->commandObj->getMeta(), 'view-index-search'),
                'header_fields' => (new SyntaxBuilder)->create($schemaArray, $this->commandObj->getMeta(), 'view-index-header'),
                'content_fields' => (new SyntaxBuilder)->create($schemaArray, $this->commandObj->getMeta(), 'view-index-content'),
            ],
            'show' => [
                'content_fields' => (new SyntaxBuilder)->create($schemaArray, $this->commandObj->getMeta(), 'view-show-content'),
            ],
            'create' => [ 'content_fields' => '', ],
            'edit' => [ 'content_fields' => '', ],
            'duplicate' => [ 'content_fields' => '', ],
            '_form' => [
                'content_fields' => (new SyntaxBuilder)->create($schemaArray, $this->commandObj->getMeta(), 'view-edit-content', true),
            ]
        ];

        //create new stub
        $stub = new StubController( $this->commandObj, $this->files, $stub_path.$stub_filename, $schema_repalce_type = null, $custom_replace[$this->viewName]);

        //compile
        $stub_compiled = $stub->getCompiled();

        //get output_path and filename
        $output_path = './resources/views/'.$this->solveName($this->commandObj->argument('name'), config('l5scaffold.app_name_rules.app_model_vars')).'/';
        $output_filename = $this->viewName.'.blade.php';

        //output(use OutputTrait)
        $this->outputPut( $output_path, $output_filename, $stub_compiled, $message_success='View-'.$this->viewName.' created successfully', $debug=false );
    }
}