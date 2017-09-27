<?php

namespace LogExpander\Events;

class CalendarEvent extends Event {
	/**
	 * Reads data for an event.
	 *
	 * @param
	 *        	[String => Mixed] $opts
	 * @return [String => Mixed]
	 *         @override Event
	 */
	public function read(array $opts) {
		$event = $this->repo->readCalendarEvent ( $opts ['objectid'] );
		
		return array_merge ( parent::read ( $opts ), [ 
				'calendarevent' => $event 
		] );
	}
}