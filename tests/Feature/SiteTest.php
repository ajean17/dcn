<?php

use App\User;
use App\Friends;
use App\Blocked;
use Tests\DuskTestCase;
use Laravel\Dusk\Chrome;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class SiteTest extends DuskTestCase
{

/**
 * click('') function, can put in id= , name= , or simply the text assigned to button/link links
 * visit('/home') to visit homepage
 *
 * */

public function pgFormTests()
 {
    //Not used for registration
    $user = factory(User::class)->create([
        'username' => 'capincook',
        'email' => 'guptagupta@gmail.com',
        'password' => 'heisenbergWW',
        'name' => 'Walter'
        ]);

    //Test Register form data fill
    $this->visit('/')
        ->click('Register')
        ->type('username', $user->username)
        ->type('email', $user->email)
        ->type('password', $user->password)
        ->type('password_confirmation', $user->password)
        ->press('termsLink')
        ->press('signupbtn')
        ->assertPathIs('/home');

    $this->browse(function ($browser) use ($user)
        {
            //Test Register form data fill
            $browser->visit('/login')
                ->type('username', $user->username)
                ->type('password', $user->password)
                ->press('Login')
                ->assertPathIs('/home');

            //Test Register form data fill
            $browser->visit('/stargazer')
                ->type('searchBar', 'Something') //Something
                ->press('startSearch')
                ->assertSee('Something');

            /**
             * Test Messanger form data fill, expecting correct dialogue opens
             *      between defined user and selected user below.
             * */
            $browser->visit('/inbox/$user->name')
                ->type('startConvo', 'Alvin') //Something
                ->keys('startConvo', '{enter}')
                ->click('Alvin') //clicks only links
                ->assertSee('MessageAlvin');
        });

}

/**
 * Testing proper links show for guest and users respectively.
 * assertion might have to be seepageis() rather than assertpathis
 * Much redundancey, such repeat -CodeDoge
 */
public function pglinkTests()
{
    //Below three test these links are accessable
    $this->visit('/')
        ->click('Home')
        ->assertPathIs('/');

    $this->visit('/')
        ->click('Login')
        ->assertPathIs('/Login');

    $this->visit('/')
        ->click('Register')
        ->assertPathIs('/Register');

    //Following five test assures links are not visible
    $this->visit('/')
          ->assertDontSeeLink('Dashboard');

    $this->visit('/')
          ->assertDontSeeLink('Profile');

    $this->visit('/')
          ->assertDontSeeLink('Search');

    $this->visit('/')
          ->assertDontSeeLink('Inbox');

    $this->visit('/')
          ->assertDontSeeLink('Logout');

    //Tests personal links are properly displayed as users profile,settings,inbox etc.
    $this->browse(function ($first, $second)
    {
    //Check Dashboard Link as Auth User
    $first->loginAs(User::find(1))
        ->visit('/')
        ->click('Dashboard')
        ->seePageIs('/dashboard');

    //Check profile Link as Auth User
    $second->loginAs(User::find(2)) //find user(2) to add post /profile
        ->visit('/')
        ->click('profile')
        ->seePageIs('/profile/$user->name');
    });

    //Following two assure links are not visible to auth user
    $this->loginAs(User::find(1))
         ->visit('/')
         ->assertDontSeeLink('Login');

    $this->loginAs(User::find(1))
         ->visit('/')
         ->assertDontSeeLink('Register');

    //Following six assure links visible to auth user
    $this->loginAs(User::find(1))
          ->visit('/')
          ->assertSeeLink('Home');

    $this->loginAs(User::find(1))
          ->visit('/')
          ->assertSeeLink('Dashboard');

    $this->loginAs(User::find(1))
          ->visit('/')
          ->assertSeeLink('Profile');

    $this->loginAs(User::find(1))
          ->visit('/')
          ->assertSeeLink('Search');

    $this->loginAs(User::find(1))
          ->visit('/')
          ->assertSeeLink('Inbox');

    $this->loginAs(User::find(1))
          ->visit('/')
          ->assertSeeLink('Logout');
}

//tests for testing file upload, mock future DB tests after this.
public function avaUpTest()
{
        User::fake('avatars');

        $response = $this->json('GET', '/images/{filename}', [
            'avatar' => UploadedFile::fake()->image('avatar.jpg')
        ]);

        // Assert the file was stored...
        User::disk('avatars')->assertExists('avatar.jpg');

        // Assert a file does not exist...
        User::disk('avatars')->assertMissing('missing.jpg');
}

//Testing the message functions properly.
public function messageTests()
{
    //for user find, probably just going to user factory for two fake users.

    //Creating multiple browser isntances to test messaging between two parties!
    $this->browse(function ($first, $second)
    {
    $first->loginAs(User::find(1))
          ->visit('/inbox/$user->name')
          ->type('messageInput', 'Yo, science BITCH!')
          ->keys('messageInput', '{enter}'); //sends the text

    $second->loginAs(User::find(2))
           ->visit('/inbox/$user2->name')
           ->waitForText('Yo, science BITCH!')
           ->type('messageInput', 'Calm down Jesse.')
           ->keys('messageInput', '{enter}');

    $first->waitForText('Calm down Jesse.')
          ->assertSeeIn('appearMessage', $user2->name);
});
}
