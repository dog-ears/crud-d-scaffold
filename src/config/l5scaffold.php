<?php
return [

    /*
    [app_name_rules]
    write Naming rules.
    Basically defined by laravel Naming rules.
    use it nochange.

    ex).
    '$key' => '$value',
    Replace {{$keyword}} to [APP name] formed by $value rule  in StubController.

    */

    'app_name_rules' => [
        'app_migrate_filename' => 'name_names', // use as table name and migration file name.
        'app_migrate_class' => 'NameNames',
        'app_migrate_tablename' => 'name_names',
        'app_seeder_class' => 'NameNames',
        'app_model_class' => 'NameName',
        'app_model_var' => 'nameName',
        'app_model_vars' => 'nameNames',
        'app_controller_class' => 'NameNames',
        'app_route' => 'nameNames',

        //Common
        'nameName' => 'nameName',
        'NameName' => 'NameName',
        'nameNames' => 'nameNames',
        'NameNames' => 'NameNames',
        'name_name' => 'name_name',
        'name_names' => 'name_names',
        'NAME_NAME' => 'NAME_NAME',
        'NAME_NAMES' => 'NAME_NAMES'
    ],

];