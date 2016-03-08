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

class MakeRoute{
    use MakerTrait,NameSolverTrait;

    protected $files;
    protected $scaffoldCommandObj;

    public function __construct(ScaffoldMakeCommand $scaffoldCommand, Filesystem $files)
    {
        $this->files = $files;
        $this->scaffoldCommandObj = $scaffoldCommand;
        $this->start();
    }

    protected function start()
    {
        //get_stub_path and filename
        $stub_path = __DIR__.'/../Stubs/route/';
        $stub_filename = 'route_insert.stub';

        //create new stub
        $stub = new StubController( $this->scaffoldCommandObj, $this->files, $stub_path.$stub_filename, $schema_repalce_type = null, $custom_replace = null );

        //compile
        $stub_compiled = $stub->getCompiled();

        //get output_path and filename
        $output_path = './app/Http/';
        $output_filename = 'routes.php';

        //output(insert)
        $src = $this->files->get($output_path.$output_filename);
        $pattern = '/(Route::group\(\[\'middleware\' => \[\'web\'\]\], function \(\) {\n)(.*\n)(}\);)/s';
        $src = preg_replace($pattern, '\1\2    '.$stub_compiled. "\n". '\3', $src);
        $this->files->put($output_path.$output_filename, $src);

        //end message
        $this->scaffoldCommandObj->info('Route updated successfully');
    }
}