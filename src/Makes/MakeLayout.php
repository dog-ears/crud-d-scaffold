<?php
/**
 * Created by PhpStorm.
 * User: fernandobritofl
 * Date: 4/22/15
 * Time: 11:49 PM

ビュー（ベースレイアウト）に関する処理を担当
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

class MakeLayout {
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
        //(i)layout --------------------------------------------------

        //get_stub_path and filename
        $stub_path = __DIR__.'/../Stubs/view_layout/';
        $stub_filename = 'layout.stub';

        //create new stub
        $stub = new StubController( $this->commandObj, $this->files, $stub_path.$stub_filename, $schema_repalce_type = null, $custom_replace = null );

        //compile
        $stub_compiled = $stub->getCompiled();

        //get output_path and filename
        $output_path = './resources/views/';
        $output_filename = 'layout.blade.php';

        //output(use OutputTrait)
        $this->outputPutWithoutAlert( $output_path, $output_filename, $stub_compiled, $message_success='view_layout_layout created successfully', $debug=false );



        //(ii)error --------------------------------------------------

        //get_stub_path and filename
        $stub_path = __DIR__.'/../Stubs/view_layout/';
        $stub_filename = 'error.stub';

        //create new stub
        $stub = new StubController( $this->commandObj, $this->files, $stub_path.$stub_filename, $schema_repalce_type = null, $custom_replace = null );

        //compile
        $stub_compiled = $stub->getCompiled();

        //get output_path and filename
        $output_path = './resources/views/';
        $output_filename = 'error.blade.php';

        //output(use OutputTrait)
        $this->outputPutWithoutAlert( $output_path, $output_filename, $stub_compiled, $message_success='view_layout_error created successfully', $debug=false );



        //(iii)navi - put --------------------------------------------------

        //get_stub_path and filename
        $stub_path = __DIR__.'/../Stubs/view_layout/';
        $stub_filename = 'navi.stub';

        //create new stub
        $stub = new StubController( $this->commandObj, $this->files, $stub_path.$stub_filename, $schema_repalce_type = null, $custom_replace = null );

        //compile
        $stub_compiled = $stub->getCompiled();

        //get output_path and filename
        $output_path = './resources/views/';
        $output_filename = 'navi.blade.php';

        //output(use OutputTrait)
        $this->outputPutWithoutAlert( $output_path, $output_filename, $stub_compiled, $message_success='view_layout_navi created successfully', $debug=false );

        //(iv)navi - add --------------------------------------------------

        //get_stub_path and filename
        $stub_path = __DIR__.'/../Stubs/view_layout/';
        $stub_filename = 'navi_add.stub';

        //create new stub
        $stub = new StubController( $this->commandObj, $this->files, $stub_path.$stub_filename, $schema_repalce_type = null, $custom_replace = null );

        //compile
        $stub_compiled = $stub->getCompiled();

        //get output_path and filename
        $output_path = './resources/views/';
        $output_filename = 'navi.blade.php';

        //replace word
        $pattern = '#(<ul id="app_navi" class="dropdown-menu" role="menu">)(.*?)(\s*?</ul>)#s';
        $replacement = '\1\2'.$stub_compiled.'\3';

        //output(use OutputTrait)
        $this->outputReplace( $output_path, $output_filename, $pattern, $replacement, $message_success='view_layout_navi updated successfully', $debug=false );

        //(iv)_getOrderlink.blade.php --------------------------------------------------

        //(iii) GetAllDataTrait

        //get_stub_path and filename
        $stub_path = __DIR__.'/../Stubs/view_layout/';
        $stub_filename = '_getOrderlink.blade.stub';
        $src = $this->files->get($stub_path.$stub_filename);

        //get output_path and filename
        $output_path = './resources/views/';
        $output_filename = '_getOrderlink.blade.php';

        //output(use OutputTrait)
        $this->outputPutWithoutAlert( $output_path, $output_filename, $src, $message_success='view_layout_GetAllDataTrait updated successfully', $debug=false );

    }
}