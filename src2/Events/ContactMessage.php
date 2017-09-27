<?php

namespace LogExpander\Events;

class ContactMessage extends Event {
	/**
	 * Reads data for an event.
	 *
	 * @param
	 *        	[String => Mixed] $opts
	 * @return [String => Mixed]
	 *         @override Event
	 */
	public function read(array $opts) {
		$message = $this->repo->readContactMessage ( $opts ['action'], $opts ['other'], $opts ['objectid'] );
		$courseid = '0';
		if (isset ( $message->courseid )) {
			$courseid = $message->courseid;
		}
		$opts ['courseid'] = $courseid;
		
		return array_merge ( parent::read ( $opts ), [ 
				'contactmessage' => $message,
				'course' => $this->repo->readCourse ( $courseid ) 
		] );
	}
}