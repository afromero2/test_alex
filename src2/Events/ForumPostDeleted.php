<?php

namespace LogExpander\Events;

class ForumPostDeleted extends Event {
	/**
	 * Reads data for an event.
	 *
	 * @param
	 *        	[String => Mixed] $opts
	 * @return [String => Mixed]
	 *         @override Event
	 */
	public function read(array $opts) {
		// Gets a forum id from other field
		$forumid = $this->repo->getForumId ( $opts ['other'] );
		
		return array_merge ( parent::read ( $opts ), [ 
				'module' => $this->repo->readModule ( $forumid, 'forum' ) 
		] );
	}
}