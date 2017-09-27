<?php

namespace LogExpander\Events;

class DiscussionSubscription extends Event {
	/**
	 * Reads data for an event.
	 *
	 * @param
	 *        	[String => Mixed] $opts
	 * @return [String => Mixed]
	 *         @override Event
	 */
	public function read(array $opts) {
		$subs = $this->repo->readObject ( $opts ['objectid'], $opts ['objecttable'] );
		$discussion = $this->repo->readDiscussion ( $subs->discussion );
		
		return array_merge ( parent::read ( $opts ), [ 
				'discussion' => $discussion,
				'module' => $this->repo->readModule ( $discussion->forum, 'forum' ) 
		] );
	}
}