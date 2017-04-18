<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class UnitTest extends TestCase
{
  public function fillTest()
  {
    //Unit tests are
    //$user = factory(App\User::class)->create();
    /**
    $this->actingAs($user)
             ->withSession(['foo' => 'bar'])
             ->visit('/')
             ->see('Hello, '.$user->name);
      **/
      //testing login form fill and event click
      $this->visit('/register')
        ->type('pmoney', 'username')
        ->type('pmoney@gmail.com', 'email')
        ->type('dcnStar1', 'password')
        ->type('dcnStar1', 'password_confirmation')
        ->press('signupbtn')
        ->seePageIs('/');

     //testing login form fill and event click
      $this->visit(/'login')
        ->type('pmoney', 'name')
        ->type('dcnStar1' , 'password')
        ->press('login')
        ->seePageIs('/');

     //tests logout properly re-routes to homepage
      $this->visit('/')
        ->click('Logout')
        ->seePageIs('/');

     $this->visit('/stargazer')
        ->type('pmoney', 'searchBar')
        ->check('video')
        ->check('mentor')
        ->check('investments')
        ->check('ROI')
        ->press('Search')
        ->seePageIs('/stargazer');

     $this->visit('/inbox/{inboxOwner}')
        ->type('pmoney','startConvo')
        ->press('pmoney')
        ->seePageIs('');
  }

 public function frndSysTest()
  {
    $userF = factory(App\Friend::class)->create();
    $userB = factory(App\Block::class)->create();

   $this->visit('/')
      ->press('friend');

   $this->visit('/') // /profile/{profileOwner} /profile/{{Auth::user()->name}
      ->press('block
}
