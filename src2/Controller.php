<?php

namespace LogExpander;

use \stdClass as PhpObj;

class Controller extends PhpObj {
	protected $repo;
	public static $routes = [ 
			'\core\event\course_viewed' => 'Event',
			'\mod_page\event\course_module_viewed' => 'ModuleEvent',
			'\mod_url\event\course_module_viewed' => 'ModuleEvent',
			'\mod_folder\event\course_module_viewed' => 'ModuleEvent',
			'\mod_forum\event\course_module_viewed' => 'ModuleEvent',
			'\mod_forum\event\discussion_viewed' => 'DiscussionEvent',
			'\mod_forum\event\discussion_created' => 'DiscussionEvent',
			'\mod_forum\event\discussion_subscription_created' => 'DiscussionSubscription',
			'\mod_forum\event\user_report_viewed' => 'ModuleEvent',
			'\mod_forum\event\post_created' => 'ForumPost',
			'\mod_forum\event\post_updated' => 'ForumPost',
			'\mod_forum\event\post_deleted' => 'ForumPostDeleted',
			'\core\event\user_graded' => 'ForumGraded',
			'\mod_book\event\course_module_viewed' => 'ModuleEvent',
			'\mod_scorm\event\sco_launched' => 'ScormLaunched',
			'\mod_scorm\event\course_module_viewed' => 'ModuleEvent',
			'\mod_scorm\event\report_viewed' => 'ScormEvent',
			'\mod_scorm\event\attempt_deleted' => 'ScormEvent',
			'\mod_resource\event\course_module_viewed' => 'ResourceEvent',
			'\mod_choice\event\course_module_viewed' => 'ModuleEvent',
			'\mod_data\event\course_module_viewed' => 'ModuleEvent',
			'\mod_feedback\event\course_module_viewed' => 'ModuleEvent',
			'\mod_lesson\event\course_module_viewed' => 'ModuleEvent',
			'\mod_lti\event\course_module_viewed' => 'ModuleEvent',
			'\mod_wiki\event\course_module_viewed' => 'ModuleEvent',
			'\mod_wiki\event\page_viewed' => 'ModuleEvent',
			'\mod_wiki\event\page_created' => 'WikiPage',
			'\mod_wiki\event\page_updated' => 'WikiPage',
			'\mod_wiki\event\comments_viewed' => 'WikiPage',
			'\mod_wiki\event\comment_created' => 'WikiComment',
			'\mod_wiki\event\comment_deleted' => 'WikiCommentDeleted',
			'\mod_workshop\event\course_module_viewed' => 'ModuleEvent',
			'\mod_chat\event\course_module_viewed' => 'ModuleEvent',
			'\mod_chat\event\sessions_viewed' => 'ChatSession',
			'\mod_chat\event\message_sent' => 'ChatMessage',
			'\mod_glossary\event\course_module_viewed' => 'ModuleEvent',
			'\mod_glossary\event\entry_approved' => 'GlossaryEntry',
			'\mod_glossary\event\entry_created' => 'GlossaryEntry',
			'\mod_glossary\event\entry_updated' => 'GlossaryEntry',
			'\mod_glossary\event\entry_deleted' => 'GlossaryEntryDeleted',
			'\mod_imscp\event\course_module_viewed' => 'ModuleEvent',
			'\mod_survey\event\course_module_viewed' => 'ModuleEvent',
			'\mod_facetoface\event\course_module_viewed' => 'ModuleEvent',
			'\mod_quiz\event\course_module_viewed' => 'ModuleEvent',
			'\mod_quiz\event\attempt_started' => 'AttemptEvent',
			'\mod_quiz\event\attempt_abandoned' => 'AttemptEvent',
			'\mod_quiz\event\attempt_reviewed' => 'AttemptEvent',
			'\mod_quiz\event\attempt_viewed' => 'AttemptEvent',
			'\mod_quiz\event\attempt_submitted' => 'AttemptEvent',
			'\mod_quiz\event\attempt_summary_viewed' => 'AttemptEvent',
			'\mod_quiz\event\question_manually_graded' => 'AttemptEvent',
			'\core\event\user_loggedin' => 'Event',
			'\core\event\user_loggedout' => 'Event',
			'\core\event\user_created' => 'Event',
			'\core\event\user_enrolment_created' => 'Event',
			'\core\event\user_list_viewed' => 'Event',
			'\core\event\user_profile_viewed' => 'UserProfile',
			'\core\event\user_updated' => 'UserProfile',
			'\mod_assign\event\submission_graded' => 'AssignmentGraded',
			'\mod_assign\event\assessable_submitted' => 'AssignmentSubmitted',
			'\mod_feedback\event\response_submitted' => 'FeedbackSubmitted',
			'\core\event\course_completed' => 'CourseCompleted',
			'\core\event\calendar_event_created' => 'CalendarEvent',
			'\core\event\calendar_event_updated' => 'CalendarEvent',
			'\core\event\calendar_event_deleted' => 'Event',
			'\core\event\message_viewed' => 'ContactMessage',
			'\core\event\message_sent' => 'ContactMessage',
			'\gradereport_overview\event\grade_report_viewed' => 'GradeReport',
			'\core\event\course_user_report_viewed' => 'GradeReport',
			'\core\event\course_module_created' => 'ModuleCreated',
			'\core\event\course_module_updated' => 'ModuleCreated',
			'\core\event\course_module_deleted' => 'Event' 
	];
	/**
	 * Constructs a new Controller.
	 *
	 * @param Repository $repo        	
	 */
	public function __construct(Repository $repo) {
		$this->repo = $repo;
	}
	/**
	 * Creates new events.
	 *
	 * @param
	 *        	[String => Mixed] $events
	 * @return [String => Mixed]
	 */
	public function createEvents(array $events) {
		$results = [ ];
		foreach ( $events as $index => $opts ) {
			$route = isset ( $opts ['eventname'] ) ? $opts ['eventname'] : '';
			if (isset ( static::$routes [$route] ) && ($opts ['userid'] > 0 || $opts ['relateduserid'] > 0)) {
				try {
					$event = '\LogExpander\Events\\' . static::$routes [$route];
					array_push ( $results, (new $event ( $this->repo ))->read ( $opts ) );
				} catch ( \Exception $e ) {
					// Error processing event; skip it.
				}
			}
		}
		return $results;
	}
}
