<?php

namespace dogears\L5scaffold;

class GeneratorException extends \Exception {

    /**
     * The exception description.
     *
     * @var string
     */
    protected $message = 'Could not determine what you are trying to do. Sorry! Check your migration name.';

}