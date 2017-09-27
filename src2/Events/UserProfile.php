<?php

namespace LogExpander\Events;

class UserProfile extends Event {
	/**
	 * Reads data for an event.
	 *
	 * @param
	 *        	[String => Mixed] $opts
	 * @return [String => Mixed]
	 *         @override Event
	 */
	public function read(array $opts) {
		$user2 = $this->repo->readUserProfile ( $opts ['objectid'] );
		
		return array_merge ( parent::read ( $opts ), [ 
				'user2' => $user2 
		] );
	}
}