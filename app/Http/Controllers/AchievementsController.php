<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;

class AchievementsController extends Controller
{
    public function index(User $user)
    {
		$lesson_achievement = Config::get('constants.lesson_achievement'); 
		$comment_achievement = Config::get('constants.comment_achievement');
		$badge = Config::get('constants.badge');
		$count_watched = count($user->watched);
		$count_comments = count($user->comments);
		$count_badge = $count_watched + $count_comments;
		
		if (!empty($lesson_achievement) && !empty($comment_achievement) && !empty($badge))
		{
			list($achieved_lesson, $next_lesson) = $this->getAchievement($count_watched, $lesson_achievement, 0);
			list($achieved_comments, $next_comments) = $this->getAchievement($count_comments, $comment_achievement, 1);
			list($achieved_badge, $nextbadge, $remaining_badge) = $this->getAchievement($count_badge, $badge, 2);
			
			return response()->json([
				'unlocked_achievements' => [$achieved_lesson, $achieved_comments],
				'next_available_achievements' => [$next_lesson, $next_comments],
				'current_badge' => $achieved_badge,
				'next_badge' => $nextbadge,
				'remaing_to_unlock_next_badge' => ($remaining_badge > 1) ? $remaining_badge . " Achievements" : $remaining_badge . " Achievement"
			]);
		} else {
			return response()->json();
		}
    }
	
	public function getAchievement($count, $achievement, $les_comm_badg)
	{
		if(!empty($achievement)) {
			$max = max(array_keys($achievement));
			if ($count > 0) {
				if ($count >= $max) {
					if ($les_comm_badg == 0) {
						$next = Config::get('constants.max_lesson');
					} else if ($les_comm_badg == 1) {
						$next = Config::get('constants.max_comment');
					} else {
						$next = $achievement[$max];
						$next_badge = $max;
					}
					$achieved = $achievement[$max];
				} else {
					foreach($achievement as $key => $value) {
						if ($key > $count) {
							$next = $value;
							if ($les_comm_badg == 2) {
								$next_badge = $key;
							}
							break;
						} else {
							$achieved = $value;
						}
					}
				}
			} else {
				if ($les_comm_badg == 0) {
					$next = current(array_slice($achievement, 0, 1));
					$achieved = Config::get('constants.no_lesson');
				} else if ($les_comm_badg == 1) {
					$next = current(array_slice($achievement, 0, 1));
					$achieved = Config::get('constants.no_comment');
				} else {
					$next = current(array_slice($achievement, 1, 1));
					$next_badge = array_search($next, $achievement);
					$achieved = current(array_slice($achievement, 0, 1));
				}
			}
			if  ($les_comm_badg == 0 || $les_comm_badg == 1) {
				return [$achieved, $next];
			} else {
				$remaining_badge = $next_badge - $count;
				$remaining_badge = ($remaining_badge > 0) ? $remaining_badge : 0;
				return [$achieved, $next, $remaining_badge];
			}
		} else {
			return '';
		}
	}
}