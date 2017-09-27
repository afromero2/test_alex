<?php

namespace LogExpander;

use \stdClass as PhpObj;
use Exception;

class Repository extends PhpObj {
	protected $store;
	protected $cfg;
	
	/**
	 * Constructs a new Repository.
	 *
	 * @param
	 *        	$store
	 * @param PhpObj $cfg        	
	 */
	public function __construct($store, PhpObj $cfg) {
		$this->store = $store;
		$this->cfg = $cfg;
	}
	
	/**
	 * Reads an object from the store with the given type and query.
	 *
	 * @param String $type        	
	 * @param
	 *        	[String => Mixed] $query
	 * @return PhpObj
	 */
	protected function readStoreRecord($type, array $query) {
		$model = $this->store->get_record ( $type, $query );
		if ($model === false) {
			throw new Exception ( 'Record not found.' );
		}
		return $model;
	}
	
	/**
	 * Reads an array of objects from the store with the given type and query.
	 *
	 * @param String $type        	
	 * @param
	 *        	[String => Mixed] $query
	 * @return PhpArr
	 */
	protected function readStoreRecords($type, array $query) {
		$model = $this->store->get_records ( $type, $query );
		return $model;
	}
	
	/**
	 * Calls the Moodle core fullname function
	 *
	 * @param PHPObj $user        	
	 * @return Str
	 */
	protected function fullname($user) {
		return fullname ( $user );
	}
	
	/**
	 * Reads an object from the store with the given id.
	 *
	 * @param String $id        	
	 * @param String $type        	
	 * @return PhpObj
	 */
	public function readObject($id, $type) {
		$model = $this->readStoreRecord ( $type, [ 
				'id' => $id 
		] );
		$model->type = $type;
		return $model;
	}
	
	/**
	 * Reads an object from the store with the given id.
	 *
	 * @param String $id        	
	 * @param String $type        	
	 * @return PhpObj
	 */
	public function readModule($id, $type) {
		$model = $this->readObject ( $id, $type );
		$module = $this->readStoreRecord ( 'modules', [ 
				'name' => $type 
		] );
		$course_module = $this->readStoreRecord ( 'course_modules', [ 
				'instance' => $id,
				'module' => $module->id,
				'course' => $model->course 
		] );
		$model->url = $this->cfg->wwwroot . '/mod/' . $type . '/view.php?id=' . $course_module->id;
		return $model;
	}
	
	/**
	 * Reads a quiz attempt from the store with the given id.
	 *
	 * @param String $id        	
	 * @return PhpObj
	 */
	public function readAttempt($id) {
		$model = $this->readObject ( $id, 'quiz_attempts' );
		$model->url = $this->cfg->wwwroot . '/mod/quiz/attempt.php?attempt=' . $id;
		$model->name = 'Attempt ' . $id;
		return $model;
	}
	
	/**
	 * Reads question attempts from the store with the given quiz attempt id.
	 *
	 * @param String $id        	
	 * @return PhpArr
	 */
	public function readQuestionAttempts($id) {
		$questionAttempts = $this->readStoreRecords ( 'question_attempts', [ 
				'questionusageid' => $id 
		] );
		foreach ( $questionAttempts as $questionIndex => $questionAttempt ) {
			$questionAttemptSteps = $this->readStoreRecords ( 'question_attempt_steps', [ 
					'questionattemptid' => $questionAttempt->id 
			] );
			foreach ( $questionAttemptSteps as $stepIndex => $questionAttemptStep ) {
				$questionAttemptStep->data = $this->readStoreRecords ( 'question_attempt_step_data', [ 
						'attemptstepid' => $questionAttemptStep->id 
				] );
			}
			$questionAttempt->steps = $questionAttemptSteps;
		}
		return $questionAttempts;
	}
	
	/**
	 * Reads questions from the store with the given quiz id.
	 *
	 * @param String $quizId        	
	 * @param String $courseid        	
	 * @return PhpArr
	 */
	public function readQuestions($quizId, $courseid) {
		$quizSlots = $this->readStoreRecords ( 'quiz_slots', [ 
				'quizid' => $quizId 
		] );
		$questions = [ ];
		foreach ( $quizSlots as $index => $quizSlot ) {
			try {
				$question = $this->readStoreRecord ( 'question', [ 
						'id' => $quizSlot->questionid 
				] );
				$question->answers = $this->readStoreRecords ( 'question_answers', [ 
						'question' => $question->id 
				] );
				$question->url = $this->cfg->wwwroot . '/question/preview.php?id=' . $question->id . '&courseid=' . $courseid;
				$questions [$question->id] = $question;
			} catch ( \Exception $e ) {
				// Question not found; maybe it was deleted since the event.
				// Don't add the question to the list, but also don't block the attempt event.
			}
		}
		
		return $questions;
	}
	
	/**
	 * Reads a quiz attempt from other field.
	 *
	 * @param String $otherfield        	
	 * @return PhpObj
	 */
	public function readAttemptGraded($otherfield) {
		if (! empty ( $otherfield )) {
			$pos = strpos ( $otherfield, 'attemptid' ) + 16;
			$other = substr ( $otherfield, $pos );
			$attemptid = explode ( "\"", $other );
			if (! is_null ( $attemptid ) and ! empty ( $attemptid [0] )) {
				$attempt = $this->readAttempt ( $attemptid [0] );
				return $attempt;
			} else {
				return false;
			}
		}
		return false;
	}
	
	/**
	 * Reads grade metadata from the store with the given type and id.
	 *
	 * @param String $id        	
	 * @param String $type        	
	 * @return PhpObj
	 */
	public function readGradeItems($id, $type) {
		return $this->readStoreRecord ( 'grade_items', [ 
				'itemmodule' => $type,
				'iteminstance' => $id 
		] );
	}
	
	/**
	 * Reads assignemnt grade comment from the store for a given grade and assignment id
	 *
	 * @param String $id        	
	 * @return PhpObj
	 */
	public function readGradeComment($grade_id, $assignment_id) {
		$model = $this->readStoreRecord ( 'assignfeedback_comments', [ 
				'assignment' => $assignment_id,
				'grade' => $grade_id 
		] );
		return $model;
	}
	
	/**
	 * Reads a feedback attempt from the store with the given id.
	 *
	 * @param String $id        	
	 * @return PhpObj
	 */
	public function readFeedbackAttempt($id) {
		$model = $this->readObject ( $id, 'feedback_completed' );
		$model->url = $this->cfg->wwwroot . '/mod/feedback/complete.php?id=' . $id;
		$model->name = 'Attempt ' . $id;
		$model->responses = $this->readStoreRecords ( 'feedback_value', [ 
				'completed' => $id 
		] );
		return $model;
	}
	
	/**
	 * Reads questions from the store with the given feedback id.
	 *
	 * @param String $id        	
	 * @return PhpArr
	 */
	public function readFeedbackQuestions($id) {
		$questions = $this->readStoreRecords ( 'feedback_item', [ 
				'feedback' => $id 
		] );
		foreach ( $questions as $index => $question ) {
			$question->template = $this->readStoreRecord ( 'feedback_template', [ 
					'id' => $question->template 
			] );
			$question->url = $this->cfg->wwwroot . '/mod/feedback/edit_item.php?id=' . $question->id;
			$questions [$index] = $question;
		}
		return $questions;
	}
	
	/**
	 * Reads a course from the store with the given id.
	 *
	 * @param String $id        	
	 * @return PhpObj
	 */
	public function readCourse($id) {
		// set to index page. to correct log in and log out issue.
		if ($id == 0) {
			$courses = $this->store->get_records ( 'course', array () );
			// since get_records will return the ids as Key values for the array,
			// just use key to find the first id in the course table for the index page
			$id = key ( $courses );
		}
		$model = $this->readObject ( $id, 'course' );
		$model->url = $this->cfg->wwwroot . ($id > 0 ? '/course/view.php?id=' . $id : '');
		return $model;
	}
	
	/**
	 * Reads a user from the store with the given id.
	 *
	 * @param String $id        	
	 * @return PhpObj
	 */
	public function readUser($id) {
		$model = $this->readObject ( $id, 'user' );
		$model->url = $this->cfg->wwwroot;
		$model->fullname = $this->fullname ( $model );
		return $model;
	}
	
	/**
	 * Reads a user profile from the store with the given id.
	 *
	 * @param String $id        	
	 * @return PhpObj
	 */
	public function readUserProfile($id) {
		$model = $this->readObject ( $id, 'user' );
		$model->fullname = $this->fullname ( $model );
		$model->url = $this->cfg->wwwroot . '/user/profile.php?id=' . $model->id;
		return $model;
	}
	
	/**
	 * Reads a discussion from the store with the given id.
	 *
	 * @param String $id        	
	 * @return PhpObj
	 */
	public function readDiscussion($id) {
		$model = $this->readObject ( $id, 'forum_discussions' );
		$model->url = $this->cfg->wwwroot . '/mod/forum/discuss.php?d=' . $id;
		return $model;
	}
	
	/**
	 * Reads the Moodle release number.
	 *
	 * @return String
	 */
	public function readRelease() {
		return $this->cfg->release;
	}
	
	/**
	 * Reads the Moodle site
	 *
	 * @return PhpObj
	 */
	public function readSite() {
		$model = $this->readCourse ( 1 );
		$model->url = $this->cfg->wwwroot;
		$model->type = "site";
		return $model;
	}
	
	/**
	 * Reads a event from the store with the given id.
	 *
	 * @param String $id        	
	 * @return PhpObj
	 */
	public function readCalendarEvent($id) {
		$model = $this->readObject ( $id, 'event' );
		$model->url = $this->cfg->wwwroot . '/calendar/view.php?view=day&course=1&time=' . $model->timestart . '#event_' . $model->id;
		return $model;
	}
	
	/**
	 * Reads a contact message from the store with the given id.
	 *
	 * @param String $action        	
	 * @param String $otherfield        	
	 * @param String $id        	
	 * @return PhpObj
	 */
	public function readContactMessage($action, $otherfield, $id) {
		if ($action == 'viewed') {
			$model = $this->readObject ( $id, 'message_read' );
			$model->url = $this->cfg->wwwroot . '/message/index.php?user1=' . $model->useridto . '&user2=' . $model->useridfrom;
			return $model;
		} else if ($action == 'sent') {
			if (! empty ( $otherfield )) {
				$messageid = end ( explode ( '"', strip_tags ( $otherfield ) ) );
				$messageid = intval ( preg_replace ( '/[^0-9]+/', '', $messageid ), 10 );
				$model = $this->readObject ( $messageid, 'message' );
				$model->url = $this->cfg->wwwroot . '/message/index.php?user1=' . $model->useridto . '&user2=' . $model->useridfrom;
				
				$usuarioguid = '';
				$user = $this->readObject ( $model->useridto, 'user' );
				$sql = 'SELECT wsid FROM {nsgradesup_wsuserinfo} WHERE identification=:cedulausuario';
				$paramssql = array (
						'cedulausuario' => $user->idnumber 
				);
				$paramsUTPL = $this->store->get_records_sql ( $sql, $paramssql );
				foreach ( $paramsUTPL as $paramUTPL ) {
					$usuarioguid = $paramUTPL->wsid;
				}
				
				$receiver = [ 
						'userid' => $user->id,
						'usuarioguid' => $usuarioguid 
				];
				
				$model->receiver = $receiver;
				return $model;
			}
		}
		return false;
	}
	
	/**
	 * Gets a forum id from the other field of log_store_standard table.
	 *
	 * @param String $otherfield        	
	 * @return String
	 */
	public function getForumId($otherfield) {
		if (! empty ( $otherfield )) {
			$pos = strpos ( $otherfield, 'forumid' ) + 14;
			$other = substr ( $otherfield, $pos );
			$forumid = explode ( "\"", $other );
			if (! is_null ( $forumid ) and ! empty ( $forumid [0] )) {
				return $forumid [0];
			} else {
				return '0';
			}
		}
		return '0';
	}
	
	/**
	 * Gets a deleted entry concept from the other field of log_store_standard table.
	 *
	 * @param String $otherfield        	
	 * @return String
	 */
	public function getEntryConcept($otherfield) {
		if (! empty ( $otherfield )) {
			$pos = strpos ( $otherfield, 'concept' ) + 14;
			$other = substr ( $otherfield, $pos );
			$concept = explode ( "\";}", $other );
			if (! is_null ( $concept ) and ! empty ( $concept [0] )) {
				return $concept [0];
			} else {
				return '';
			}
		}
		return '';
	}
	
	/**
	 * Reads resource file from the store with the given contextid and itemid.
	 *
	 * @param String $id        	
	 * @param String $contextid        	
	 * @param boolean $attachment        	
	 * @return PhpObj
	 */
	public function readResourceFile($id, $contextid, $attachment) {
		if ($attachment) {
			$files = $this->readStoreRecords ( 'files', [ 
					'itemid' => $id,
					'contextid' => $contextid 
			] );
		} else {
			$files = $this->readStoreRecords ( 'files', [ 
					'contextid' => $contextid 
			] );
		}
		foreach ( $files as $index => $file ) {
			$filename = $file->filename;
			if (! empty ( $filename ) and $filename != '.') {
				$file->type = 'files';
				return $file;
			}
		}
		return false;
	}
	
	/**
	 * Gets urls of Grade Report.
	 *
	 * @param
	 *        	String target
	 * @param String $userid        	
	 * @param String $courseid        	
	 * @return ArrayObject
	 */
	public function getGradeReportUrl($target, $userid, $courseid) {
		$url = '';
		if ($target == 'grade_report') {
			$url = $this->cfg->wwwroot . '/grade/report/overview/index.php';
		} else if ($target == 'course_user_report') {
			$url = $this->cfg->wwwroot . '/course/user.php?mode=grade&id=' . $courseid . '&user=' . $userid;
		}
		return $url;
	}
	
	/**
	 * Reads a glossary from the store with the given contextinstanceid.
	 *
	 * @param String $contextinstanceid        	
	 * @return PhpObj
	 */
	public function readGlossary($contextinstanceid) {
		$course_module = $this->readStoreRecord ( 'course_modules', [ 
				'id' => $contextinstanceid 
		] );
		$model = $this->readObject ( $course_module->instance, 'glossary' );
		return $model;
	}
	
	/**
	 * Reads a wiki from the store with the given contextinstanceid.
	 *
	 * @param String $contextinstanceid        	
	 * @return PhpObj
	 */
	public function readWiki($contextinstanceid) {
		$course_module = $this->readStoreRecord ( 'course_modules', [ 
				'id' => $contextinstanceid 
		] );
		$model = $this->readObject ( $course_module->instance, 'wiki' );
		return $model;
	}
	
	/**
	 * Reads a wiki page from the store with the given id.
	 *
	 * @param String $target        	
	 * @param String $id        	
	 * @return PhpObj
	 */
	public function readWikiPage($target, $id) {
		$model = $this->readObject ( $id, 'wiki_pages' );
		if ($target == 'page') {
			$model->url = $this->cfg->wwwroot . '/mod/wiki/view.php?pageid=' . $model->id;
		} else if ($target == 'comments') {
			$model->url = $this->cfg->wwwroot . '/mod/wiki/comments.php?pageid=' . $model->id;
		}
		$subwiki = $this->readObject ( $model->subwikiid, 'wiki_subwikis' );
		$model->wikiid = $subwiki->wikiid;
		return $model;
	}
	
	/**
	 * Reads a wiki comment from the store with the given id.
	 *
	 * @param String $id        	
	 * @return PhpObj
	 */
	public function readWikiComment($id) {
		$model = $this->readObject ( $id, 'comments' );
		$model->url = $this->cfg->wwwroot . '/mod/wiki/comments.php?pageid=' . $model->itemid;
		$wikipage = $this->readWikiPage ( 'page', $model->itemid );
		$model->wikiid = $wikipage->wikiid;
		return $model;
	}
	
	/**
	 * Reads extra data of specific project.
	 *
	 * @param PhpObj $user        	
	 * @param PhpObj $course        	
	 * @return array
	 */
	public function readExtraData($user, $course) {
		$periodoguid = '';
		$periodoid = '';
		$paraleloguid = '';
		$componenteid = '';
		$componenteguid = '';
		$componentecode = '';
		$usuarioguid = '';
		$userrole = '';
		if ($course->id > 1) {
			
			$sql = 'SELECT wscomponentid, wsid, componentid FROM {nsacademic_parallel} WHERE courseid = :courseid';
			$paramssql = array (
					'courseid' => $course->id 
			);
			$paramsUTPL = $this->store->get_records_sql ( $sql, $paramssql );
			foreach ( $paramsUTPL as $paramUTPL ) {
				$paraleloguid = $paramUTPL->wsid;
				$componenteguid = $paramUTPL->wscomponentid;
				$componenteid = $paramUTPL->componentid;
			}
			
			$sql = 'SELECT wscode, periodid FROM {nsacademic_component} WHERE id=:componenteid';
			$paramssql = array (
					'componenteid' => $componenteid 
			);
			$paramsUTPL = $this->store->get_records_sql ( $sql, $paramssql );
			foreach ( $paramsUTPL as $paramUTPL ) {
				$componentecode = $paramUTPL->wscode;
				$periodoid = $paramUTPL->periodid;
			}
			
			$sql = 'SELECT wsid FROM {nsacademic_period} WHERE id= :periodoid';
			$paramssql = array (
					'periodoid' => $periodoid 
			);
			$paramsUTPL = $this->store->get_records_sql ( $sql, $paramssql );
			foreach ( $paramsUTPL as $paramUTPL ) {
				$periodoguid = $paramUTPL->wsid;
			}
			
			$role = get_user_roles_in_course ( $user->id, $course->id );
			if (! empty ( $role )) {
				$userrole = end ( explode ( '"', strip_tags ( $role ) ) );
			}
		} else {
			$sql = 'SELECT id,wsid FROM {nsacademic_period} WHERE active=1';
			$paramssql = array (
					'periodoid' => $periodoid 
			);
			$paramsUTPL = $this->store->get_record_sql ( $sql, $paramssql );
			$periodoguid = $paramsUTPL->wsid;
			$periodoid = $paramsUTPL->id;
		}
		
		$sql = 'SELECT wsid FROM {nsgradesup_wsuserinfo} WHERE identification=:cedulausuario';
		$paramssql = array (
				'cedulausuario' => $user->idnumber 
		);
		$paramsUTPL = $this->store->get_records_sql ( $sql, $paramssql );
		foreach ( $paramsUTPL as $paramUTPL ) {
			$usuarioguid = $paramUTPL->wsid;
		}
		
		$data = [ 
				'courseid' => $course->id,
				'coursefullname' => $course->fullname,
				'courseshortname' => $course->shortname,
				'idnumber' => $course->idnumber,
				'userid' => $user->id,
				'userrole' => $userrole,
				'periodoguid' => $periodoguid,
				'periodoid' => $periodoid,
				'paraleloguid' => $paraleloguid,
				'componenteid' => $componenteid,
				'componenteguid' => $componenteguid,
				'componentecode' => $componentecode,
				'usuarioguid' => $usuarioguid 
		];
		
		return $data;
	}
	
	/**
	 * Reads a new course module when module has created from the store.
	 *
	 * @param String $otherfield        	
	 * @return PhpObj
	 */
	public function readNewModule($otherfield) {
		if (! empty ( $otherfield )) {
			if (strpos ( $otherfield, 'modulename' ) !== false) {
				$pos = strpos ( $otherfield, 'modulename' ) + 17;
				$other = substr ( $otherfield, $pos );
				$modulename = explode ( "\";", $other );
				if (! is_null ( $modulename ) and ! empty ( $modulename [0] )) {
					$pos = strpos ( $otherfield, 'instanceid' ) + 14;
					$other = substr ( $otherfield, $pos );
					$instanceid = explode ( ";", $other );
					if (! is_null ( $instanceid ) and ! empty ( $instanceid [0] )) {
						$model = $this->readModule ( $instanceid [0], $modulename [0] );
						return $model;
					}
				}
			}
		}
		return false;
	}
	
	/**
	 * Reads a course module of Tutor from the store.
	 *
	 * @param String $courseid        	
	 * @param String $otherfield        	
	 * @return PhpObj
	 */
	public function readTutorModule($courseid, $otherfield) {
		if (! empty ( $otherfield )) {
			if (strpos ( $otherfield, 'tutor' ) !== false) {
				$module = $this->readStoreRecord ( 'modules', [ 
						'name' => 'tutorship' 
				] );
				$course_module = $this->readStoreRecord ( 'course_modules', [ 
						'module' => $module->id,
						'course' => $courseid 
				] );
				$model = $this->readObject ( $course_module->instance, 'tutorship' );
				$model->url = $this->cfg->wwwroot . '/mod/tutorship/view.php?id=' . $course_module->id;
				return $model;
			}
		}
		return false;
	}
	
	/**
	 * Reads a periods of Tutor from the store.
	 *
	 * @param String $courseid        	
	 * @return PhpArr
	 */
	public function readTutorPeriods($courseid) {
		$periods = $this->readStoreRecords ( 'tutorship_periods', array () );
		if (count ( $periods ) > 0) {
			foreach ( $periods as $index => $period ) {
				$data = $this->readStoreRecords ( 'tutorship_timetables', [ 
						'periodid' => $period->id,
						'curso' => $courseid 
				] );
				if (count ( $data ) > 0) {
					$period->time = $data;
				}
			}
		}
		return $periods;
	}
	
	/**
	 * Reads a forum post from the store with the given id.
	 *
	 * @param String $id        	
	 * @param String $contextid        	
	 * @return PhpObj
	 */
	public function readForumPost($id, $contextid) {
		$model = $this->readObject ( $id, 'forum_posts' );
		$model->replied = [ ];
		$model->attachedfile = [ ];
		if ($model->parent != 0) {
			$parent = $this->readObject ( $model->parent, 'forum_posts' );
			if (! is_null ( $parent )) {
				$usuarioguid = '';
				$user = $this->readObject ( $parent->userid, 'user' );
				$sql = 'SELECT wsid FROM {nsgradesup_wsuserinfo} WHERE identification=:cedulausuario';
				$paramssql = array (
						'cedulausuario' => $user->idnumber 
				);
				$paramsUTPL = $this->store->get_records_sql ( $sql, $paramssql );
				foreach ( $paramsUTPL as $paramUTPL ) {
					$usuarioguid = $paramUTPL->wsid;
				}
				
				$replied = [ 
						'userid' => $user->id,
						'usuarioguid' => $usuarioguid 
				];
				
				$model->replied = $replied;
			}
		}
		if ($model->attachment == 1) {
			$file = $this->readResourceFile ( $id, $contextid, true );
			$attachment = [ 
					'filename' => $file->filename,
					'mimetype' => $file->mimetype 
			];
			
			$model->attachedfile = $attachment;
		}
		return $model;
	}
	
	/**
	 * Reads a forum graded user from the store with the given id.
	 *
	 * @param String $id        	
	 * @return PhpObj
	 */
	public function readForumGraded($id) {
		$model = $this->readObject ( $id, 'grade_grades' );
		$item = $this->readObject ( $model->itemid, 'grade_items' );
		if ($item->itemmodule == 'forum')
			$model->forum = $item->iteminstance;
		return $model;
	}
	
	/**
	 * Reads a scorm from the store with the given contextinstanceid.
	 *
	 * @param String $contextinstanceid        	
	 * @return PhpObj
	 */
	public function readScorm($contextinstanceid) {
		$course_module = $this->readStoreRecord ( 'course_modules', [ 
				'id' => $contextinstanceid 
		] );
		$model = $this->readObject ( $course_module->instance, 'scorm' );
		$model->url = $this->cfg->wwwroot . '/mod/scorm/view.php?id=' . $contextinstanceid;
		return $model;
	}
	
	/**
	 * Reads a scorm tracks from the store with the given id and timecreated.
	 *
	 * @param String $scormid        	
	 * @param String $timecreated        	
	 * @return PhpArr
	 */
	public function readScormTracks($scormid, $timecreated) {
		$tracks = $this->readStoreRecords ( 'scorm_scoes_track', [ 
				'scormid' => $scormid 
		] );
		$tracksresult = [ ];
		if (count ( $tracks ) > 0) {
			foreach ( $tracks as $index => $track ) {
				$trackdate = strval ( $track->timemodified );
				$eventdate = strval ( $timecreated );
				if ($trackdate >= $eventdate) {
					$tracksresult [] = $track;
				}
			}
		}
		return $tracksresult;
	}
	
	/**
	 * Reads sessions of chat from the store.
	 *
	 * @param String $otherfield        	
	 * @return array
	 */
	public function readChatSession($otherfield) {
		$session = [ ];
		if (! empty ( $otherfield )) {
			$pos = strpos ( $otherfield, 'start' ) + 9;
			$other = substr ( $otherfield, $pos );
			$start = explode ( ";", $other );
			if (! is_null ( $start ) and ! empty ( $start [0] )) {
				$end = end ( explode ( '"', strip_tags ( $otherfield ) ) );
				$end = intval ( preg_replace ( '/[^0-9]+/', '', $end ), 10 );
				
				$startint = ( int ) $start [0];
				$endint = ( int ) $end;
				$interval = $endint - $startint;
				$days = floor ( $interval / 86400 );
				$hours = floor ( ($interval - ($days * 86400)) / 3600 );
				$minutes = floor ( ($interval - ($days * 86400) - ($hours * 3600)) / 60 );
				
				$session = [ 
						'start' => date ( 'c', $startint ),
						'end' => date ( 'c', $endint ),
						'time' => $days . ' days ' . $hours . ' hours ' . $minutes . ' minutes' 
				];
			}
		}
		return $session;
	}
}
