<?php
namespace Tests\Browser;
use Tests\DuskTestCase;
use Laravel\Dusk\Browser;
use Illuminate\Foundation\Testing\DatabaseMigrations;
class Case0010Test extends DuskTestCase
{
  /**
   * @group case0010
   */
  public function testCode()
  {
  // // copy crud-d-scaffold.json
  // shell_exec('copy .\vendor\dog-ears\crud-d-scaffold\crud-d-scaffold_case0010.json .\crud-d-scaffold.json');
  
  // // clear db
  // if( file_exists('.\database\database.sqlite') ){
  //     shell_exec('del .\database\database.sqlite');
  // }
  // shell_exec('type nul > .\database\database.sqlite');
  // shell_exec('php artisan crud-d-scaffold:setup -f');
  shell_exec('php artisan migrate:refresh');
  shell_exec('php artisan db:seed');

    $this->browse(function (Browser $browser) {

      /* ------------------------------------------------------------------
      01_toppage
      ------------------------------------------------------------------ */

      echo('01_toppage start!'."\n");

      $browser
        ->visit('/')
        ->assertSee('Laravel');

      $browser->screenshot('01_toppage');
      echo('01_toppage ok'."\n");

      /* ------------------------------------------------------------------
      02_nicePost_index
      ------------------------------------------------------------------ */

      echo('02_nicePost_index start!'."\n");

      $browser
        ->visit('/nicePosts')
        ->assertSee('NICE POST')
        ->assertSee('ID')
        ->assertSee('BIG TITLE')
        ->assertDontSee('SHALLOW BODY')
        ->assertSee('TAG')
        ->assertSee('CATEGORY')
        ->assertSee('COMMENT')
        ->assertSee('OPTIONS')
        ;

      $browser->screenshot('02_nicePost_index');
      echo('02_nicePost_index ok'."\n");

      /* ------------------------------------------------------------------
      03_beautifulTag_index
      ------------------------------------------------------------------ */

      echo('03_beautifulTag_index start!'."\n");

      $browser->clickLink('Menu level 1')
        ->clickLink('TAG')
        ->assertPathIs('/beautifulTags')
        ->assertSee('TAG')
        ->assertSee('ID')
        ->assertSee('TAG NAME')
        ->assertSee('NICE POST')
        ->assertSee('OPTIONS')
        ;

      $browser->screenshot('03_beautifulTag_index');
      echo('03_beautifulTag_index ok'."\n");

      /* ------------------------------------------------------------------
      04_beautifulTag_create_x3
      ------------------------------------------------------------------ */

      echo('04_beautifulTag_create_x3 start!'."\n");

      $dataArr = ['TAG_NAME_01', 'TAG_NAME_02', 'TAG_NAME_03'];
      foreach($dataArr as $data){
        $browser
          ->clickLink('Create')
          ->pause(200)
          ->assertPathIs('/beautifulTags/create')
          ->assertSee('TAG / Create')
          ->waitFor('#tag_name-field')->type('model[tag_name]', $data)
          ;

          $browser->screenshot('04_beautifulTag_create_x3_p01_'. $data);

          // html5 validation hack
          $current_url = $browser->driver->getCurrentURL();
          $browser->pause(200)->press('Create');
          if( $current_url === $browser->driver->getCurrentURL() ){
              $browser->press('Create');
          }
        $browser->assertPathIs('/beautifulTags');
      }

      $browser->screenshot('04_beautifulTag_create_x3_p02');
      echo('04_beautifulTag_create_x3 ok'."\n");

      /* ------------------------------------------------------------------
      05_brilliantCategory_index
      ------------------------------------------------------------------ */

      echo('05_brilliantCategory_index start!'."\n");

      $browser
        ->clickLink('Menu level 1')
        ->clickLink('CATEGORY')
        ->assertPathIs('/brilliantCategories')
        ->assertSee('CATEGORY')
        ->assertSee('ID')
        ->assertSee('CATEGORY NAME')
        ->assertSee('NICE POST')
        ->assertSee('OPTIONS')
        ;

        $browser->screenshot('05_brilliantCategory_index');
        echo('05_brilliantCategory_index ok'."\n");

      /* ------------------------------------------------------------------
      06_brilliantCategory_create_x3
      ------------------------------------------------------------------ */

      echo('06_brilliantCategory_create_x3 start!'."\n");

      $dataArr = ['CATEGORY_NAME_01', 'CATEGORY_NAME_02', 'CATEGORY_NAME_03'];
      foreach($dataArr as $data){
        $browser
          ->clickLink('Create')
          ->pause(200)
          ->assertPathIs('/brilliantCategories/create')
          ->assertSee('CATEGORY / Create')
          ->waitFor('#category_name-field')->type('model[category_name]', $data)
          ;

          $browser->screenshot('06_brilliantCategory_create_x3_p01_'. $data);

          // html5 validation hack
          $current_url = $browser->driver->getCurrentURL();
          $browser->pause(200)->press('Create');
          if( $current_url === $browser->driver->getCurrentURL() ){
            $browser->press('Create');
          }
        $browser->assertPathIs('/brilliantCategories');
      }

      $browser->screenshot('06_brilliantCategory_create_x3_p02');
      echo('06_brilliantCategory_create_x3 ok'."\n");

      /* ------------------------------------------------------------------
      07_nicePost_create_x1
      ------------------------------------------------------------------ */

      echo('07_nicePost_create_x1 start!'."\n");
      // tag 1(priority:999->777,note:NOTE_01),3()
      // category 2

      $browser
        ->visit('/brilliantCategories')
        ->clickLink('Menu level 1')
        ->clickLink('NICE POST')
        ->clickLink('Create')
        ->assertPathIs('/nicePosts/create')
        ->assertSee('NICE POST / Create')
        ->waitFor('#big_title-field')->type('model[big_title]', 'BIG_TITLE_01')
        ->waitFor('#shallow_body-field')->type('model[shallow_body]', 'SHALLOW_BODY_01')
        ->waitFor('#brilliant_category_id-field')->select('model[brilliant_category_id]', '32');

      $browser->screenshot('07_nicePost_create_x1_p01_basic');
        
      // tag 1(priority:999,note:NOTE_01)
      echo('tag 1(priority:999,note:NOTE_01)'."\n");
      $browser
        ->waitFor('#pivotsBeautifulTagCheckbox31')->check('pivots[beautiful_tag][31][id]')
        ->whenAvailable('.modal', function ($modal) {
          $modal
            ->pause(200)
            ->assertSee('BeautifulTag Option')
            ->waitFor('#priority-field')->type('pivots-option[priority]', '999')
            ->waitFor('#note-field')->type('pivots-option[note]', 'NOTE_01');

          $modal->screenshot('07_nicePost_create_x1_p02_tag1');

          $modal->press('Save changes');
        })
        ->waitUntilMissing('.modal')
        ->waitUntilMissing('.modal-backdrop')
        ->assertChecked('pivots[beautiful_tag][31][id]');

      // tag 2 check and uncheck
      echo('tag 2 check and uncheck'."\n");
      $browser
        ->waitFor('#pivotsBeautifulTagCheckbox32')->check('pivots[beautiful_tag][32][id]')
        ->whenAvailable('.modal', function ($modal) {
          $modal
            ->pause(200)
            ->assertSee('BeautifulTag Option')
            ->waitFor('#priority-field')->type('pivots-option[priority]', '888')
            ->waitFor('#note-field')->type('pivots-option[note]', 'NOTE_02')
            ->assertSee('Save changes');

          $modal->screenshot('07_nicePost_create_x1_p03_tag2');

          $modal->press('Save changes');
        })
        ->waitUntilMissing('.modal')
        ->waitUntilMissing('.modal-backdrop')
        ->assertChecked('pivots[beautiful_tag][32][id]')
        ->waitFor('#pivotsBeautifulTagCheckbox32')->uncheck('pivots[beautiful_tag][32][id]');	//uncheck

      // tag 3()
      echo('tag 3()'."\n");
      $browser
        ->waitFor('#pivotsBeautifulTagCheckbox33')->check('pivots[beautiful_tag][33][id]')
        ->whenAvailable('.modal', function ($modal) {
          $modal
            ->pause(200)
            ->assertSee('BeautifulTag Option')
            ->assertSee('Save changes');

          $modal->screenshot('07_nicePost_create_x1_p04_tag3');

          $modal->press('Save changes');
        })
        ->waitUntilMissing('.modal')
        ->waitUntilMissing('.modal-backdrop')
        ->assertChecked('pivots[beautiful_tag][33][id]');

      // check tag 1 property is saved correctly
      echo('check tag 1 property is saved correctly - 01 edit property'."\n");
      $browser
        ->waitFor('#pivotsBeautifulTagCheckbox31')->uncheck('pivots[beautiful_tag][31][id]')
        ->assertNotChecked('pivots[beautiful_tag][31][id]')
        ->waitFor('#pivotsBeautifulTagCheckbox31')->check('pivots[beautiful_tag][31][id]')
        ->whenAvailable('.modal', function ($modal) {
          $modal
            ->pause(200)
            ->assertSee('BeautifulTag Option')
            ->assertInputValue('pivots-option[priority]', '999')
            ->assertInputValue('pivots-option[note]', 'NOTE_01')
            ->waitFor('#priority-field')->type('pivots-option[priority]', '777')
            ->assertSee('Save changes');

          $modal->screenshot('07_nicePost_create_x1_p05_tag1_edit');

          $modal->press('Save changes');
        })
        ->waitUntilMissing('.modal')
        ->waitUntilMissing('.modal-backdrop');
      $browser->uncheck('pivots[beautiful_tag][31][id]')
        ->check('pivots[beautiful_tag][31][id]')
        ->whenAvailable('.modal', function ($modal) {
          $modal
            ->pause(200)
            ->assertSee('BeautifulTag Option')
            ->assertInputValue('pivots-option[priority]', '777')
            ->assertInputValue('pivots-option[note]', 'NOTE_01')
            ->assertSee('Cancel');

          $modal->screenshot('07_nicePost_create_x1_p06_tag1_editcheck');

          $modal->press('Cancel');
        })
        ->waitUntilMissing('.modal')
        ->waitUntilMissing('.modal-backdrop');

      // html5 validation hack
      $current_url = $browser->driver->getCurrentURL();
      $browser->press('Create');
      if( $current_url === $browser->driver->getCurrentURL() ){
          $browser->press('Create');
      }
      $browser->assertPathIs('/nicePosts');

      $browser->screenshot('07_nicePost_create_x1_p07_allcheck');

      echo('07_nicePost_create_x1 ok'."\n");

      /* ------------------------------------------------------------------
      08_nicePost_index
      ------------------------------------------------------------------ */

      echo('08_nicePost_index start!'."\n");

      $browser->assertSee('BIG_TITLE_01')
        ->assertSee('TAG_NAME_01( PRIORITY:777 NOTE:NOTE_01 )')
        ->assertDontSee('TAG_NAME_02')
        ->assertSee('TAG_NAME_03')
        ->assertSee('CATEGORY_NAME_02')
        ;

      $browser->screenshot('08_nicePost_index');
      echo('08_nicePost_index ok'."\n");

      /* ------------------------------------------------------------------
      09_nicePost_show
      ------------------------------------------------------------------ */

      echo('09_nicePost_show start!'."\n");

      // nicePost_show - check
      echo('nicePost - show - check'."\n");
      $browser->clickLink('31')
        ->assertPathIs('/nicePosts/31')
        ->assertSee('BIG_TITLE_01')
        ->assertSee('CATEGORY_NAME_02')
        ->assertSee('TAG_NAME_01( PRIORITY:777 NOTE:NOTE_01 ) , TAG_NAME_03( PRIORITY: NOTE: )')
        ;

      $browser->screenshot('09_nicePost_show');
      echo('09_nicePost_show ok'."\n");

      /* ------------------------------------------------------------------
      10_nicePost_edit
      ------------------------------------------------------------------ */

      echo('10_nicePost_edit start!'."\n");

      $browser->click('a[data-original-title=Edit]')
        ->assertPathIs('/nicePosts/31/edit')
        ->assertInputValue('model[big_title]', 'BIG_TITLE_01')
        ->assertInputValue('model[shallow_body]', 'SHALLOW_BODY_01')
        ->assertSelected('model[brilliant_category_id]', '32')
        ->assertChecked('pivots[beautiful_tag][31][id]')
        ->assertChecked('pivots[beautiful_tag][33][id]');

      $browser->screenshot('10_nicePost_edit_p01_before_edit');

      $browser->select('model[brilliant_category_id]', '31')
        ->uncheck('pivots[beautiful_tag][31][id]')
        ->check('pivots[beautiful_tag][31][id]')
        ->whenAvailable('.modal', function ($modal) {
          $modal
            ->pause(200)
            ->assertSee('BeautifulTag Option')
            ->assertInputValue('pivots-option[priority]', '777')
            ->assertInputValue('pivots-option[note]', 'NOTE_01')
            ->type('pivots-option[priority]', '555')
            ->type('pivots-option[note]', 'NOTE_01_EDIT');

          $modal->screenshot('10_nicePost_edit_p02_tag1');

          $modal->press('Save changes');
        })
        ->waitUntilMissing('.modal')
        ->waitUntilMissing('.modal-backdrop')
        ->uncheck('pivots[beautiful_tag][33][id]')
        ->check('pivots[beautiful_tag][33][id]')
        ->whenAvailable('.modal', function ($modal) {
          $modal
            ->pause(200)
            ->assertSee('BeautifulTag Option')
            ->assertInputValue('pivots-option[priority]', '')
            ->assertInputValue('pivots-option[note]', '')
            ->type('pivots-option[priority]', '333')
            ->type('pivots-option[note]', 'NOTE_03');

          $modal->screenshot('10_nicePost_edit_p03_tag3');

          $modal->press('Save changes');
        })
        ->waitUntilMissing('.modal')
        ->waitUntilMissing('.modal-backdrop')
        ->press('Save')
        ->assertPathIs('/nicePosts')
        ;

        $browser->screenshot('10_nicePost_edit_p04');
        echo('10_nicePost_edit ok'."\n");

      /* ------------------------------------------------------------------
      11_nicePost_index
      ------------------------------------------------------------------ */

      echo('11_nicePost_index start!'."\n");

      $browser->assertSee('BIG_TITLE_01')
        ->assertDontSee('TAG_NAME_01( PRIORITY:777 NOTE:NOTE_01 )')
        ->assertSee('TAG_NAME_01( PRIORITY:555 NOTE:NOTE_01_EDIT )')
        ->assertSee('TAG_NAME_03( PRIORITY:333 NOTE:NOTE_03 )')
        ->assertSee('CATEGORY_NAME_01')
        ;

      $browser->screenshot('11_nicePost_index');
      echo('11_nicePost_index ok'."\n");

      /* ------------------------------------------------------------------
      12_nicePost_duplicate
      ------------------------------------------------------------------ */

      echo('12_nicePost_duplicate start!'."\n");

      $browser->clickLink('31')
        ->assertPathIs('/nicePosts/31')
        ->click('a[data-original-title=Duplicate]')
        ->assertPathIs('/nicePosts/31/duplicate')
        ->assertSee('NICE POST / Duplicate #31');

      $browser->screenshot('12_nicePost_duplicate_p01_before_edit');

      $browser->assertInputValue('model[big_title]', 'BIG_TITLE_01')
        ->type('model[big_title]', 'BIG_TITLE_01_DUPLICATE')
        ->assertInputValue('model[shallow_body]', 'SHALLOW_BODY_01')
        ->type('model[shallow_body]', 'SHALLOW_BODY_01_DUPLICATE')
        ->assertSelected('model[brilliant_category_id]', '31')
        ->select('model[brilliant_category_id]', '33')
        ->assertChecked('pivots[beautiful_tag][31][id]')
        ->assertChecked('pivots[beautiful_tag][33][id]')
        ->uncheck('pivots[beautiful_tag][31][id]')
        ->uncheck('pivots[beautiful_tag][33][id]');

      $browser->screenshot('12_nicePost_duplicate_p02_after_edit');

      $browser->press('Duplicate')
        ->assertPathIs('/nicePosts')
        ;

      $browser->screenshot('12_nicePost_duplicate_p03');
      echo('12_nicePost_duplicate ok'."\n");

      /* ------------------------------------------------------------------
      13_nicePost_index
      ------------------------------------------------------------------ */

      echo('13_nicePost_index start!'."\n");

      $browser->assertSee('BIG_TITLE_01_DUPLICATE')
        ->assertDontSee('SHALLOW_BODY_01_DUPLICATE')
        ;

      $browser->screenshot('13_nicePost_index');
      echo('13_nicePost_index ok'."\n");

      /* ------------------------------------------------------------------
      14_nicePost_show
      ------------------------------------------------------------------ */

      echo('14_nicePost_show start!'."\n");

      // nicePost - show - check
      echo('nicePost - show - check'."\n");
      $browser->clickLink('32')
        ->assertPathIs('/nicePosts/32')
        ->assertSee('BIG_TITLE_01_DUPLICATE')
        ->assertDontSee('TAG_NAME_01( PRIORITY:555 NOTE:NOTE_01_EDIT )')
        ->assertDontSee('TAG_NAME_03( PRIORITY:333 NOTE:NOTE_03 )')
        ->assertDontSee('CATEGORY_NAME_01')
        ->assertDontSee('CATEGORY_NAME_02')
        ->assertSee('CATEGORY_NAME_03')
        ;

      $browser->screenshot('14_nicePost_show');
      echo('14_nicePost_show ok'."\n");

      /* ------------------------------------------------------------------
      15_nicePost_delete
      ------------------------------------------------------------------ */

      echo('15_nicePost_delete start!'."\n");

      $browser->click('[data-original-title=Delete]')
        ->driver->switchTo()->alert()->accept()
        ;
      $browser->assertPathIs('/nicePosts');

      $browser->screenshot('15_nicePost_delete_p01');
      echo('15_nicePost_delete ok'."\n");

      /* ------------------------------------------------------------------
      16_goodComment_index
      ------------------------------------------------------------------ */

      echo('16_goodComment_index start!'."\n");

      // goodComment - index
      echo('goodComment - index'."\n");
      $browser->clickLink('Menu level 1')
        ->clickLink('COMMENT')
        ->assertSee('COMMENT')
        ->assertSee('ID')
        ->assertSee('SUPER TITLE')
        ->assertDontSee('ELIGIBLE BODY')
        ->assertSee('NICE POST')
        ->assertSee('OPTIONS')
        ;

      $browser->screenshot('16_goodComment_index_p01');
      echo('16_goodComment_index ok'."\n");

      /* ------------------------------------------------------------------
      17_goodComment_create
      ------------------------------------------------------------------ */

      echo('17_goodComment_create start!'."\n");

      $browser->clickLink('Create')
        ->assertPathIs('/goodComments/create')
        ->assertSee('COMMENT / Create')
        ->type('model[super_title]', 'SUPER_TITLE_01')
        ->type('model[eligible_body]', 'ELIGIBLE_BODY_01')
        ->select('model[nice_post_id]', '31')
        ;

        $browser->screenshot('17_goodComment_create_p01');

        // html5 validation hack
        $current_url = $browser->driver->getCurrentURL();
        $browser->press('Create');
        if( $current_url === $browser->driver->getCurrentURL() ){
            $browser->press('Create');
        }
      $browser->assertPathIs('/goodComments')
        ->assertSee('SUPER_TITLE_01');

      $browser->screenshot('17_goodComment_create_p02');
      echo('17_goodComment_create ok'."\n");

      /* ------------------------------------------------------------------
      18_nicePost_index
      ------------------------------------------------------------------ */

      echo('18_nicePost_index start!'."\n");

      // nicePost - index
      echo('nicePost - index'."\n");
      $browser->clickLink('Menu level 1')
        ->clickLink('NICE POST')
        ->assertSee('SUPER_TITLE_01')
        ->clickLink('31')
        ->assertPathIs('/nicePosts/31')
        ->assertSee('SUPER_TITLE_01')
        ;

      $browser->screenshot('18_nicePost_index_p01');
      echo('18_nicePost_index ok'."\n");

    });

    //clean
    // shell_exec('git checkout .');
    // shell_exec("git clean -df");
  }
}