<?php

namespace dogears\L5scaffold\Makes;


use Illuminate\Filesystem\Filesystem;
use dogears\L5scaffold\Commands\ScaffoldMakeCommand;

class MakeSeed
{
    use MakerTrait;

    public function __construct(ScaffoldMakeCommand $scaffoldCommand, Filesystem $files)
    {
        $this->files = $files;
        $this->scaffoldCommandObj = $scaffoldCommand;

        $this->start();
    }


    protected function start()
    {


        // Get path
        $path = $this->getPath($this->scaffoldCommandObj->getNameConfig('seeder_name'). 'TableSeeder', 'seed');


        // Create directory
        $this->makeDirectory($path);


        if ($this->files->exists($path)) {
            if ($this->scaffoldCommandObj->confirm($path . ' already exists! Do you wish to overwrite? [yes|no]')) {
                // Put file
                $this->files->put($path, $this->compileSeedStub());
                $this->getSuccessMsg();
            }
        } else {

            // Put file
            $this->files->put($path, $this->compileSeedStub());
            $this->getSuccessMsg();

        }

    }


    protected function getSuccessMsg()
    {
        $this->scaffoldCommandObj->info('Seed created successfully.');
    }


    /**
     * Compile the migration stub.
     *
     * @return string
     */
    protected function compileSeedStub()
    {
        $stub = $this->files->get(__DIR__ . '/../stubs/seed.stub');

        $this->replaceClassName($stub);


        return $stub;
    }


    private function replaceClassName(&$stub)
    {
        $name = $this->scaffoldCommandObj->getNameConfig('seeder_name');

        $stub = str_replace('{{class}}', $name, $stub);

        return $this;
    }


}