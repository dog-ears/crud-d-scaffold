# Laravel 5.3 Scaffold Generator

  Hi, this is a scaffold generator for Laravel 5.3.
  You can Create Basic CRUD application by using this package.
  (For laravel 5.2, use package version 1.2.0)

  Basic CRUD application generated by this package has some distinctive features.
  
  (i) Duplicate function.  
  (ii) Show sorted list with filter by conditions (include word or range value).



## How to Install

  At first, this package needs [Collective\Html] package.
  See document and Install [Collective\Html].
  https://laravelcollective.com/docs/5.2/html

### Step 1: Install Through Composer

```
"require": {
...
    "dog-ears/crud-d-scaffold": "1.*"
}
```
  and update composer
```
composer update
```

### Step 2: Add the Service Provider

Open `config/app.php` and, to your **providers** array at the bottom, add:

```
'providers' => [
...
  dogears\CrudDscaffold\GeneratorsServiceProvider::class,
    ],
```

### Step 3: Run Artisan!

You're all set. Run `php artisan` from the console, and you'll see the new commands below.
```
- 'make:scaffold' : Create a scaffold with bootstrap 3
- 'delete:scaffold' : Delete a scaffold
- 'make:relation' : Create OntToMany Relationship between model_A and model_B
- 'delete:relation' : Delete OntToMany Relationship between model_A and model_B
```



## Examples 1 - Create Application and make relationship.

(i) publish public resource.
```
php artisan vendor:publish --tag=public --force
```
(ii) Scaffold 2 Model [AppleType] and [Apple].
  Apple has apple_type_id column for relationship.
```
php artisan make:scaffold AppleType --schema="name:string" --seeding
```
```
php artisan make:scaffold Apple --schema="name:string,apple_type_id:integer:unsigned" --seeding
```
(iii) migrate and seeding
```
php artisan migrate
```
```
php artisan db:seed
```
(iv) Make Relationship [AppleType] has many [Apple]s.
```
php artisan make:relation AppleType Apple
```

Check your application.



## Examples 2 Delete Application created scaffold command.

(i) Delete relationship. 
```
php artisan delete:relation AppleType Apple
```
(ii) Delete application.
```
php artisan delete:scaffold AppleType
```
```
php artisan delete:scaffold Apple
```
Some files remains.
  It is recommended that you do migrate:reset and delete files manually.



## Options
-s, --schema[=SCHEMA]  Schema to generate scaffold files.  
(Ex: --schema="title:string, body:nullable:, apple_type_id:integer:unsigned")  
-S, --seeding          Create seeding files.  
-d, --softdelete       add soft delete function to model



## Screen Capture
![image](https://github.com/dog-ears/crud-d-scaffold/wiki/img/cap01.jpg)
![image](https://github.com/dog-ears/crud-d-scaffold/wiki/img/cap02.jpg)
![image](https://github.com/dog-ears/crud-d-scaffold/wiki/img/cap03.jpg)



## Update History

visit my blog
<http://dog-ears.net/en/category/laravel/package/scaffold/history/>
