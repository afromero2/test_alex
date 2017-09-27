<?php

namespace LogExpander\Events;

class ScormEvent extends Event {
	/**
	 * Reads data for an event.
	 *
	 * @param
	 *        	[String => Mixed] $opts
	 * @return [String => Mixed]
	 *         @override Event
	 */
	public function read(array $opts) {
		$scorm = $this->repo->readScorm ( $opts ['contextinstanceid'] );
		return array_merge ( parent::read ( $opts ), [ 
				'module' => $this->repo->readModule ( $scorm->id, 'scorm' ),
				'scorm' => $scorm 
		] );
	}
}