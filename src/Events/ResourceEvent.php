<?php

namespace LogExpander\Events;

class ResourceEvent extends ModuleEvent {
	/**
	 * Reads data for an event.
	 *
	 * @param
	 *        	[String => Mixed] $opts
	 * @return [String => Mixed]
	 *         @override Event
	 */
	public function read(array $opts) {
		// Read file information of resource
		$file = $this->repo->readResourceFile ( $opts ['objectid'], $opts ['contextid'], false );
		
		return array_merge ( parent::read ( $opts ), [ 
				'resourcefile' => $file 
		] );
	}
}