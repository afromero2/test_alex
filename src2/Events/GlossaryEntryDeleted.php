<?php

namespace LogExpander\Events;

class GlossaryEntryDeleted extends Event {
	/**
	 * Reads data for an event.
	 *
	 * @param
	 *        	[String => Mixed] $opts
	 * @return [String => Mixed]
	 *         @override Event
	 */
	public function read(array $opts) {
		// Gets entry concept from other field
		$concept = $this->repo->getEntryConcept ( $opts ['other'] );
		$glossary = $this->repo->readGlossary ( $opts ['contextinstanceid'] );
		
		return array_merge ( parent::read ( $opts ), [ 
				'concept' => $concept,
				'module' => $this->repo->readModule ( $glossary->id, 'glossary' ) 
		] );
	}
}