<?php

namespace LogExpander\Events;

class GlossaryEntry extends Event {
	/**
	 * Reads data for an event.
	 *
	 * @param
	 *        	[String => Mixed] $opts
	 * @return [String => Mixed]
	 *         @override Event
	 */
	public function read(array $opts) {
		$entry = $this->repo->readObject ( $opts ['objectid'], $opts ['objecttable'] );
		
		return array_merge ( parent::read ( $opts ), [ 
				'entry' => $entry,
				'module' => $this->repo->readModule ( $entry->glossaryid, 'glossary' ) 
		] );
	}
}