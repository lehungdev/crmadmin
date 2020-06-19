<?php
/**
 * Code generated using IdeaGroup
 * Help: lehung.hut@gmail.com
 * CrmAdmin is open-sourced software licensed under the MIT license.
 * Developed by: Lehungdev IT Solutions
 * Developer Website: http://ideagroup.vn
 */

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class CrmAdminTest extends TestCase
{
	use DatabaseMigrations;

	/**
     * Basic setup before testing
     *
     * @return void
     */
    public function setUp()
    {
		parent::setUp();
		// Generate Seeds
		$this->artisan('db:seed');

		// Register Super Admin
		$this->visit('/register')
			->type('Taylor Otwell', 'name')
			->type('test@example.com', 'email')
			->type('12345678', 'password')
			->type('12345678', 'password_confirmation')
			->press('Register')
			->seePageIs('/');
    }

	/**
     * A basic test example.
     *
     * @return void
     */
    public function testExample()
    {
		$this->visit('/')
             ->see('CrmAdmin')
			 ->see('Taylor Otwell');
    }

	/**
     * Test Login Page.
     *
     * @return void
     */
    public function testLoginPage()
    {
		$this->visit('/login')
            ->seePageIs('/');
    }

	/**
     * Test Login.
     *
     * @return void
     */
    public function testLoginRequiredFields()
    {
        $this->visit('/logout')
			->seePageIs('/')
			->click('Login')
			->type('', 'email')
            ->type('', 'password')
            ->press('Sign In')
            ->see('The email field is required')
            ->see('The password field is required');
    }

	/**
     * Test Login Page.
     *
     * @return void
     */
    public function testLogin()
    {
		$this->visit('/login')
            ->seePageIs('/')
			->visit('/logout')
			->seePageIs('/')
			->click('Login')
			->see('Sign in to start your session')
			->type('test@example.com', 'email')
			->type('12345678', 'password')
			->press('Sign In')
			->seePageIs('/')
			->see('Taylor Otwell');
    }
}
