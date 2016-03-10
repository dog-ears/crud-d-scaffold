<?php
/**
 * Created by PhpStorm.
 * User: fernandobritofl
 * Date: 4/22/15
 * Time: 10:34 PM

ルートに関する処理を担当
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
use dogears\L5scaffold\Traits\OutputTrait;

class MakeRoute{
    use MakerTrait,NameSolverTrait,OutputTrait;

    protected $files;
    protected $commandObj;

    public function __construct($command, Filesystem $files)
    {
        $this->files = $files;
        $this->commandObj = $command;
        $this->start();
    }

    protected function start()
    {
        //get_stub_path and filename
        $stub_path = __DIR__.'/../Stubs/route/';
        $stub_filename = 'route_insert.stub';

        //create new stub
        $stub = new StubController( $this->commandObj, $this->files, $stub_path.$stub_filename, $schema_repalce_type = null, $custom_replace = null );

        //compile
        $stub_compiled = $stub->getCompiled();

        //get output_path and filename
        $output_path = './app/Http/';
        $output_filename = 'routes.php';

        //replace word
        $pattern = '/(Route::group\(\[\'middleware\' => \[\'web\'\]\], function \(\) {\n)(.*\n)(}\);)/s';
        $replacement = '\1\2    '.$stub_compiled. "\n". '\3';

        //output(use OutputTrait)
        $this->outputReplace( $output_path, $output_filename, $pattern, $replacement, $message_success='Route updated successfully', $debug=false );
    }
}