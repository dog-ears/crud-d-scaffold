<?php
/**
 * Created by PhpStorm.
 * User: fernandobritofl
 * Date: 4/22/15
 * Time: 10:34 PM

ルートサービスプロバイダに関する処理を担当
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

class MakeRouteServiceProvider {
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
        $stub_path = __DIR__.'/../Stubs/routeServiceProvider/';
        $stub_filename = 'app.stub';

        //create new stub
        $stub = new StubController( $this->commandObj, $this->files, $stub_path.$stub_filename, $schema_repalce_type = null, $custom_replace = null );

        //compile
        $stub_compiled = $stub->getCompiled();

        //get output_path and filename
        $output_path = './app/Providers/';
        $output_filename = 'RouteServiceProvider.php';

        //replace word
        $pattern = '#(.*)(public function boot\(Router \$router\)\n\s*\{\n)(.*?)(\s*}\n)(.*)#s';
        $replacement = '\1\2\3'."\n".$stub_compiled.'\4\5';

        //output(use OutputTrait)
        $this->outputReplace( $output_path, $output_filename, $pattern, $replacement, $message_success='RouteServiceProvider updated successfully', $debug=false );
    }
}
