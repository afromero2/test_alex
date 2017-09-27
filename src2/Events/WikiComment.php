<?php

namespace LogExpander\Events;

class WikiComment extends Event {
	/**
	 * Reads data for an event.
	 *
	 * @param
	 *        	[String => Mixed] $opts
	 * @return [String => Mixed]
	 *         @override Event
	 */
	public function read(array $opts) {
		$comment = $this->repo->readWikiComment ( $opts ['objectid'] );
		$wiki = $this->repo->readObject ( $comment->wikiid, 'wiki' );
		
		return array_merge ( parent::read ( $opts ), [ 
				'wikicomment' => $comment,
				'wiki' => $wiki 
		] );
	}
}