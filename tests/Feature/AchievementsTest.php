<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Illuminate\Testing\Fluent\AssertableJson;
use Illuminate\Support\Facades\Config;
use App\Http\Controllers\AchievementsController;

class AchievementsTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_checkUser()
    {
		//When User Available
		$response = $this->getJson('/users/1/achievements');

		$response->assertStatus(200);
			
		//When User not Available
		$response = $this->getJson('/users/5/achievements');

		$response->assertStatus(404);
    }
	
	public function test_currentAchievement()
	{
		//Checking for Unlocked Achievement
		$response = $this->getJson('/users/1/achievements');

		$response->assertStatus(200)
				 ->assertJson(fn (AssertableJson $json) =>
					$json->hasAll('unlocked_achievements', 'next_available_achievements', 'current_badge', 'next_badge', 'remaing_to_unlock_next_badge')
						->where('unlocked_achievements',["First Lesson Watched","First Comment Written"])
				);
	}

	public function test_nextAvailableAchievement()
	{
		//Checking for Next Available Achievement
		$response = $this->getJson('/users/1/achievements');

		$response->assertStatus(200)
				 ->assertJson(fn (AssertableJson $json) =>
					$json->hasAll('unlocked_achievements', 'next_available_achievements', 'current_badge', 'next_badge', 'remaing_to_unlock_next_badge')
						->where('next_available_achievements',["5 Lessons Watched","3 Comments Written"])
				);
	}
	
	public function test_currentBadge() 
	{
		//Checking for Current Badge
		$response = $this->getJson('/users/1/achievements');

		$response->assertStatus(200)
				 ->assertJson(fn (AssertableJson $json) =>
					$json->hasAll('unlocked_achievements', 'next_available_achievements', 'current_badge', 'next_badge', 'remaing_to_unlock_next_badge')
						->where('current_badge','Beginner')
				);
	}
	
	public function test_nextBadge()
	{
		//Checking for Next Badge
		$response = $this->getJson('/users/1/achievements');

		$response->assertStatus(200)
				 ->assertJson(fn (AssertableJson $json) =>
					$json->hasAll('unlocked_achievements', 'next_available_achievements', 'current_badge', 'next_badge', 'remaing_to_unlock_next_badge')
						->where('next_badge','Intermediate')
				);
	}
	
	public function test_remainingAchievement()
	{
		//Checking for Remaining Achievement
		$response = $this->getJson('/users/1/achievements');

		$response->assertStatus(200)
				 ->assertJson(fn (AssertableJson $json) =>
					$json->hasAll('unlocked_achievements', 'next_available_achievements', 'current_badge', 'next_badge', 'remaing_to_unlock_next_badge')
						->where('remaing_to_unlock_next_badge','1 Achievement')
				);
	}
	
	public function test_watched0Achievement()
	{
		//When user not watched any lesson
		$myachievement = new AchievementsController();
        list($achieved_lesson, $next_lesson) = $myachievement->getAchievement(0,Config::get('constants.lesson_achievement'),0);
		
		$this->assertEquals('No Lesson Watched',$achieved_lesson);
		$this->assertEquals('First Lesson Watched',$next_lesson);
	}
	
	public function test_watchedAchievement()
	{
		//When user watched 1 lesson
		$myachievement = new AchievementsController();
        list($achieved_lesson, $next_lesson) = $myachievement->getAchievement(1,Config::get('constants.lesson_achievement'),0);
		
		$this->assertEquals('First Lesson Watched',$achieved_lesson);
		$this->assertEquals('5 Lessons Watched',$next_lesson);
	}
	
	public function test_watched5Achievement()
	{
		//When user watched 5 lessons
		$myachievement = new AchievementsController();
        list($achieved_lesson, $next_lesson) = $myachievement->getAchievement(5,Config::get('constants.lesson_achievement'),0);
		
		$this->assertEquals('5 Lessons Watched',$achieved_lesson);
		$this->assertEquals('10 Lessons Watched',$next_lesson);
	}
	
	public function test_watched10Achievement()
	{
		//When user watched 10 lessons
		$myachievement = new AchievementsController();
        list($achieved_lesson, $next_lesson) = $myachievement->getAchievement(10,Config::get('constants.lesson_achievement'),0);
		
		$this->assertEquals('10 Lessons Watched',$achieved_lesson);
		$this->assertEquals('25 Lessons Watched',$next_lesson);
	}
	
	public function test_watched25Achievement()
	{
		//When user watched 25 lessons
		$myachievement = new AchievementsController();
        list($achieved_lesson, $next_lesson) = $myachievement->getAchievement(25,Config::get('constants.lesson_achievement'),0);
		
		$this->assertEquals('25 Lessons Watched',$achieved_lesson);
		$this->assertEquals('50 Lessons Watched',$next_lesson);
	}
	
	public function test_watched50Achievement()
	{
		//When user watched 50 lessons
		$myachievement = new AchievementsController();
        list($achieved_lesson, $next_lesson) = $myachievement->getAchievement(50,Config::get('constants.lesson_achievement'),0);
		
		$this->assertEquals('50 Lessons Watched',$achieved_lesson);
		$this->assertEquals('Max Lessons Watched',$next_lesson);
	}
	
	public function test_written0Achievement()
	{
		//When user wrote no comment
		$myachievement = new AchievementsController();
        list($achieved_comments, $next_comments) = $myachievement->getAchievement(0,Config::get('constants.comment_achievement'),1);
		
		$this->assertEquals('No Comment Written',$achieved_comments);
		$this->assertEquals('First Comment Written',$next_comments);
	}
	
	public function test_writtenAchievement()
	{
		//When user written 1 comment
		$myachievement = new AchievementsController();
        list($achieved_comments, $next_comments) = $myachievement->getAchievement(1,Config::get('constants.comment_achievement'),1);
		
		$this->assertEquals('First Comment Written',$achieved_comments);
		$this->assertEquals('3 Comments Written',$next_comments);
	}
	
	public function test_written3Achievement()
	{
		//When user written 3 comments
		$myachievement = new AchievementsController();
        list($achieved_comments, $next_comments) = $myachievement->getAchievement(3,Config::get('constants.comment_achievement'),1);
		
		$this->assertEquals('3 Comments Written',$achieved_comments);
		$this->assertEquals('5 Comments Written',$next_comments);
	}
	
	public function test_written5Achievement()
	{
		//When user written 5 comments
		$myachievement = new AchievementsController();
        list($achieved_comments, $next_comments) = $myachievement->getAchievement(5,Config::get('constants.comment_achievement'),1);
		
		$this->assertEquals('5 Comments Written',$achieved_comments);
		$this->assertEquals('10 Comments Written',$next_comments);
	}
	
	public function test_written10Achievement()
	{
		//When user written 10 comments
		$myachievement = new AchievementsController();
        list($achieved_comments, $next_comments) = $myachievement->getAchievement(10,Config::get('constants.comment_achievement'),1);
		
		$this->assertEquals('10 Comments Written',$achieved_comments);
		$this->assertEquals('20 Comments Written',$next_comments);
	}
	
	public function test_written20Achievement()
	{
		//When user written 20 comments
		$myachievement = new AchievementsController();
        list($achieved_comments, $next_comments) = $myachievement->getAchievement(20,Config::get('constants.comment_achievement'),1);
		
		$this->assertEquals('20 Comments Written',$achieved_comments);
		$this->assertEquals('Max Comments Written',$next_comments);
	}
	
	public function test_badgeAchievement()
	{
		//When user earned Beginner badge
		$mybadge = new AchievementsController();
        list($achieved_badge, $nextbadge, $remaining_badge) = $mybadge->getAchievement(0,Config::get('constants.badge'),2);
		
		$this->assertEquals('Beginner',$achieved_badge);
		$this->assertEquals('Intermediate',$nextbadge);
		$this->assertEquals('4',$remaining_badge);
	}
	
	public function test_badge4Achievement()
	{
		//When user got 4 achievements and earned Intermediate badge
		$mybadge = new AchievementsController();
        list($achieved_badge, $nextbadge, $remaining_badge) = $mybadge->getAchievement(4,Config::get('constants.badge'),2);
		
		$this->assertEquals('Intermediate',$achieved_badge);
		$this->assertEquals('Advanced',$nextbadge);
		$this->assertEquals('4',$remaining_badge);
	}
	
	public function test_badge8Achievement()
	{
		//When user got 8 achievements and earned Advanced badge
		$mybadge = new AchievementsController();
        list($achieved_badge, $nextbadge, $remaining_badge) = $mybadge->getAchievement(8,Config::get('constants.badge'),2);
		
		$this->assertEquals('Advanced',$achieved_badge);
		$this->assertEquals('Master',$nextbadge);
		$this->assertEquals('2',$remaining_badge);
	}
	
	public function test_badge10Achievement()
	{
		//When user got 10 achievements and earned Master badge
		$mybadge = new AchievementsController();
        list($achieved_badge, $nextbadge, $remaining_badge) = $mybadge->getAchievement(10,Config::get('constants.badge'),2);
		
		$this->assertEquals('Master',$achieved_badge);
		$this->assertEquals('Master',$nextbadge);
		$this->assertEquals('0',$remaining_badge);
	}
}
