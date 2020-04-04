<?php

namespace Tests\Browser;

use Tests\DuskTestCase;
use Laravel\Dusk\Browser;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class Case0011Test extends DuskTestCase
{
    /**
     * @group case0011
     */
    public function testCode()
    {

		// copy crud-d-scaffold.json
		// shell_exec('copy .\vendor\dog-ears\crud-d-scaffold\crud-d-scaffold_case0011.json .\crud-d-scaffold.json');

        // // clear db
        // if( file_exists('.\database\database.sqlite') ){
        //     shell_exec('del .\database\database.sqlite');
        // }
        // shell_exec('type nul > .\database\database.sqlite');

        // shell_exec('php artisan crud-d-scaffold:setup -f');

        // shell_exec('php artisan migrate');
        // shell_exec('php artisan db:seed');


        $this->browse(function (Browser $browser) {

            $browser->visit('/')
                    ->assertSee('Laravel')
                    ->assertSee('LOGIN')
                    ->assertSee('REGISTER');

                    // visit home and redirect to login
            $browser->visit('/home')
                    ->assertPathIs('/login');

                    // register user TESTNAME01 as #31 and open home
            $browser->clickLink('Register')
                    ->assertPathIs('/register')
                    ->type('name', 'TESTNAME01')
                    ->type('email', 'testname01@example.com')
                    ->type('password', 'password')
                    ->type('password_confirmation', 'password')
                    ->press('Register')
                    ->assertPathIs('/home')
                    ->assertSee('You are logged in!');

                    // Logout
            $browser->clickLink('TESTNAME01')
                    ->clickLink('Logout')
                    ->assertPathIs('/')
                    ->assertSee('LOGIN')
                    ->assertSee('REGISTER');

                    // Login and Logout
            $browser->clickLink('Login')
          					->assertPathIs('/login')
                    ->type('email', 'testname01@example.com')
                    ->type('password', 'password')
                    ->press('Login')
                    ->assertPathIs('/home')
                    ->assertSee('You are logged in!')
                    ->clickLink('TESTNAME01')
                    ->clickLink('Logout')
                    ->assertPathIs('/')
                    ->assertSee('LOGIN')
                    ->assertSee('REGISTER');



                    // visit company index page
            $browser->visit('/companies')
                    ->assertSee('ID')
                    ->assertSee('NAME')
                    ->assertSee('USER')
                    ->assertSee('OPTIONS')
                    ->clickLink('Create');

                    // create company COMPANY01 as #31
            $browser->assertPathIs('/companies/create')
                    ->assertSee('COMPANY / Create')
                    ->press('Create')
                    ->assertPathIs('/companies/create')
                    ->assertSee('Invalid!')
                    ->type('model[name]', 'COMPANY01');
					// html5 validation hack
					$current_url = $browser->driver->getCurrentURL();
					$browser->press('Create');
					if( $current_url === $browser->driver->getCurrentURL() ){
					    $browser->press('Create');
					}
            $browser->assertPathIs('/companies')
                    ->assertSee('COMPANY01');



            // visit dog index page
            $browser->visit('/dogs')
                    ->assertSee('ID')
                    ->assertSee('NAME')
                    ->assertDontSee('WEIGHT')
                    ->assertSee('USER')
                    ->assertSee('OPTIONS')
                    ->clickLink('Create');

                    // create dog DOG01 as #31
            $browser->assertPathIs('/dogs/create')
                    ->assertSee('DOG / Create')
                    ->type('model[name]', 'DOG01')
                    ->type('model[weight]', '5');
					// html5 validation hack
					$current_url = $browser->driver->getCurrentURL();
					$browser->press('Create');
					if( $current_url === $browser->driver->getCurrentURL() ){
					    $browser->press('Create');
					}
            $browser->assertPathIs('/dogs')
                    ->assertSee('DOG01');



            // visit hobby index page
            $browser->visit('/hobbies')
                    ->assertSee('ID')
                    ->assertSee('NAME')
                    ->assertSee('USER')
                    ->assertSee('OPTIONS')
                    ->clickLink('Create');

                    // create hobby HOBBY01,HOBBY02,HOBBY03 as #31,#32,#33
            $browser->assertPathIs('/hobbies/create')
                    ->assertSee('HOBBY / Create')
                    ->type('model[name]', 'HOBBY01');
					// html5 validation hack
					$current_url = $browser->driver->getCurrentURL();
					$browser->press('Create');
					if( $current_url === $browser->driver->getCurrentURL() ){
					    $browser->press('Create');
					}
            $browser->assertPathIs('/hobbies')
                    ->assertSee('HOBBY01')
                    ->clickLink('Create');

            $browser->assertPathIs('/hobbies/create')
                    ->assertSee('HOBBY / Create')
                    ->type('model[name]', 'HOBBY02');
					// html5 validation hack
					$current_url = $browser->driver->getCurrentURL();
					$browser->press('Create');
					if( $current_url === $browser->driver->getCurrentURL() ){
					    $browser->press('Create');
					}
            $browser->assertPathIs('/hobbies')
                    ->assertSee('HOBBY02')
                    ->clickLink('Create');

            $browser->assertPathIs('/hobbies/create')
                    ->assertSee('HOBBY / Create')
                    ->type('model[name]', 'HOBBY03');
					// html5 validation hack
					$current_url = $browser->driver->getCurrentURL();
					$browser->press('Create');
					if( $current_url === $browser->driver->getCurrentURL() ){
					    $browser->press('Create');
					}
            $browser->assertPathIs('/hobbies')
                    ->assertSee('HOBBY03')
                    ->clickLink('Create');



            // visit users index page
            $browser->clickLink('Menu level 1')
                	  ->clickLink('USER')
					          ->assertPathIs('/users')
                    ->assertSee('USER')
                    ->assertSee('ID')
                    ->assertSee('NAME')
                    ->assertDontSee('EMAIL')
                    ->assertDontSee('PASSWORD')
                    ->assertSee('BIRTH DAY')
                    ->assertDontSee('PHONE')
                    ->assertSee('COMPANY')
                    ->assertSee('DOG')
                    ->assertSee('HOBBY')
                    ->assertSee('SKILL LEVEL')
                    ->assertDontSee('FRIEND NAME')
                    ->assertSee('OPTIONS')
                    ->assertSee('TESTNAME01')
                    ->assertDontSee('testname01@example.com')
                    ->clickLink('Create');

                    // create user TESTNAME02 as #32
            $browser->assertPathIs('/users/create')
                    ->assertSee('USER / Create')
                    ->type('model[name]', 'TESTNAME02')
                    ->type('model[email]', 'testname02@example.com')
                    ->type('model[password]', 'password')
                    ->type('model[password_confirmation]', 'password')
                    ->type('model[birth_day]', '2000-01-01')
                    ->type('model[phone]', '09011112222')
	                  ->select('model[company_id]', '31');

	                // hobby 1(skill_level:999,firendName:John)
            $browser->check('pivots[hobby][31][id]')
	                  ->whenAvailable('.modal', function ($modal) {
                      $modal->pause(200)
                            ->assertSee('Hobby Option')
	                          ->type('pivots-option[skill_level]', '999')
                            ->type('pivots-option[firend_name]', 'John')
	                          ->press('Save changes')
	                          ;
	                })
	                ->assertChecked('pivots[hobby][31][id]')

                    ->press('Create')
                    ->assertPathIs('/users')
                    ->assertSee('TESTNAME02')
                    ->assertSee('2000-01-01')
                    ->assertSee('COMPANY01')
                    ->assertSee('HOBBY01')
                    ->assertSee('SKILL LEVEL:999')
                    ->assertDontSee('testname02@example.com')
                    ->clickLink('32');

                    // show detail of user TESTNAME01
            $browser->assertPathIs('/users/32')
                    ->assertSee('USER / Show #32')
                    ->assertSee('TESTNAME02')
                    ->assertSee('testname02@example.com')
                    ->assertSee('2000-01-01')
                    ->assertSee('09011112222')
                    ->assertSee('COMPANY01')
                    ->assertSee('HOBBY01')
                    ->assertSee('SKILL LEVEL:999')
                    ->assertSee('FRIEND NAME:John')
                    ->assertDontSee('PASSWORD')
                    ->assertSee('testname02@example.com')
                    ->click('a[data-original-title=Edit]');

                    // edit user TESTNAME02 -> TESTNAME02_EDIT
            $browser->assertPathIs('/users/32/edit')
                    ->assertSee('USER / Edit #32')
                    ->assertInputValue('model[name]', 'TESTNAME02')
                    ->assertInputValue('model[email]', 'testname02@example.com')
                    ->assertInputValue('model[password]', '')
                    ->assertInputValue('model[password_confirmation]', '')
                    ->clear('model[name]')
                    ->clear('model[email]')
                    ->type('model[name]', 'TESTNAME02_EDIT')
                    ->type('model[email]', 'testname02@example.com')
                    ->press('Save')
                    ->assertPathIs('/users')
                    ->assertSee('TESTNAME02')
                    ->clickLink('32')
                    ->click('a[data-original-title=Duplicate]');

                    // duplicate #31 TESTNAME02 to TESTNAME03
            $browser->assertPathIs('/users/32/duplicate')
                    ->assertSee('USER / Duplicate #32')
                    ->assertInputValue('model[name]', 'TESTNAME02_EDIT')
                    ->assertInputValue('model[email]', 'testname02@example.com')
                    ->assertInputValue('model[password]', '')
                    ->assertInputValue('model[password_confirmation]', '')
                    ->clear('model[name]')
                    ->type('model[name]', 'TESTNAME03')
                    ->press('Duplicate')
					          ->assertPathIs('/users/32/duplicate')
                    ->assertSee('Invalid!')
                    ->type('model[password]', 'password')
                    ->type('model[password_confirmation]', 'password')
                    ->press('Duplicate')
				          	->assertPathIs('/users/32/duplicate')
                    ->assertSee('The model.email has already been taken.')
                    ->clear('model[email]')
                    ->type('model[email]', 'testname03@example.com')
                    ->clear('model[password]')
                    ->clear('model[password_confirmation]')
                    ->type('model[password]', 'password')
                    ->type('model[password_confirmation]', 'password')
                    ->press('Duplicate')
                    ->assertPathIs('/users')
                    ->assertSee('TESTNAME03');

                    // search item
            $browser->click('button[data-target="#search-area"]')
                    ->pause(3000)
                    ->type('q[name_cont]', 'TESTNAME03')
                    ->assertInputValue('q[name_cont]', 'TESTNAME03')
                    ->click('#search-area input.btn')
                    ->pause(5000)
                    ->assertSee('TESTNAME03')
                    ->assertSee('2000-01-01')
                    ->assertSee('HOBBY01')
                    ->assertSee('SKILL LEVEL:999')
                    ->assertDontSee('TESTNAME02');

                    // delete item
            $browser->visit('/users')
                    ->clickLink('33')
                    ->click('h1 [data-original-title=Delete]')
                    ->driver->switchTo()->alert()->accept();

            $browser->assertPathIs('/users')
                    ->assertDontSee('TESTNAME03');
        });

		//clean
        // shell_exec('git checkout .');
        // shell_exec("git clean -df");
    }
}