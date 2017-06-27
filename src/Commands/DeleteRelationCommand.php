<?php

/**
Copyright (c) 2016 dog-ears

This software is released under the MIT License.
http://dog-ears.net/
*/

namespace dogears\CrudDscaffold\Commands;

use Illuminate\Console\DetectsApplicationNamespace;
use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Composer;

use dogears\CrudDscaffold\Relation\DeleteRelation;
use dogears\CrudDscaffold\Traits\MakerTrait;
use dogears\CrudDscaffold\Traits\NameSolverTrait;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

class DeleteRelationCommand extends Command
{
    use DetectsApplicationNamespace, MakerTrait, NameSolverTrait;

    /**
     * The console command name.
     *
     * @var string
     */
    protected $signature = 'delete:relation
                            {model_A : The name of the model that has many model_B. (Ex: AppleType)}
                            {model_B : The name of the model thet belongto model_A. (Ex: Apple)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Delete OntToMany Relationship between model_A and model_B';

    /**
     * @var Composer
     */
    private $composer;

    /**
     * Create a new command instance.
     *
     * @param Filesystem $files
     * @param Composer $composer
     */
    public function __construct(Filesystem $files, Composer $composer)
    {
        parent::__construct();

        $this->files = $files;
        $this->composer = $composer;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function fire()
    {
        // Message of Start
        $this->info('Configuring...');

        // Delete Relation
        new DeleteRelation($this, $this->files);

        // End Message
        $this->info('Deleting is done');

    }
}
