<?php

namespace LogExpander\Events;

class WikiPage extends Event {
	/**
	 * Reads data for an event.
	 *
	 * @param
	 *        	[String => Mixed] $opts
	 * @return [String => Mixed]
	 *         @override Event
	 */
	public function read(array $opts) {
		$page = $this->repo->readWikiPage ( $opts ['target'], $opts ['objectid'] );
		$wiki = $this->repo->readObject ( $page->wikiid, 'wiki' );
		
		return array_merge ( parent::read ( $opts ), [ 
				'wikipage' => $page,
				'wiki' => $wiki 
		] );
	}
}