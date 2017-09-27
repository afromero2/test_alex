<?php

namespace LogExpander\Events;

class ForumGraded extends Event {
	/**
	 * Reads data for an event.
	 *
	 * @param
	 *        	[String => Mixed] $opts
	 * @return [String => Mixed]
	 *         @override Event
	 */
	public function read(array $opts) {
		$graded = $this->repo->readForumGraded ( $opts ['objectid'] );
		return array_merge ( parent::read ( $opts ), [ 
				'graded' => $graded,
				'module' => $this->repo->readModule ( $graded->forum, 'forum' ) 
		] );
	}
}