<?php

namespace LogExpander\Events;

class ForumPost extends Event {
	/**
	 * Reads data for an event.
	 *
	 * @param
	 *        	[String => Mixed] $opts
	 * @return [String => Mixed]
	 *         @override Event
	 */
	public function read(array $opts) {
		$post = $this->repo->readForumPost ( $opts ['objectid'], $opts ['contextid'] );
		$discussion = $this->repo->readDiscussion ( $post->discussion );
		
		return array_merge ( parent::read ( $opts ), [ 
				'postmessage' => $post,
				'module' => $this->repo->readModule ( $discussion->forum, 'forum' ) 
		] );
	}
}