<?php
return [

    //Naming rules
    //
    //$keyword => $rule
    //の形で指定
    //
    //$ruleは次のなかから選択['nameName','NameName','nameNames','NameNames','name_name','name_names']
    //
    //スタブ内の {{$keyword}} は、スキャフォールドコマンドで入力されたアプリ名を
    //$rule ルールで変換したものに置換されます。
    //
    //
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

        //ここから下は共通
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