<?php

namespace LogExpander\Events;

class WikiCommentDeleted extends Event {
	/**
	 * Reads data for an event.
	 *
	 * @param
	 *        	[String => Mixed] $opts
	 * @return [String => Mixed]
	 *         @override Event
	 */
	public function read(array $opts) {
		$wiki = $this->repo->readWiki ( $opts ['contextinstanceid'] );
		
		return array_merge ( parent::read ( $opts ), [ 
				'module' => $this->repo->readModule ( $wiki->id, 'wiki' ) 
		] );
	}
}