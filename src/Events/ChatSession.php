<?php

namespace LogExpander\Events;

class ChatSession extends Event {
	/**
	 * Reads data for an event.
	 *
	 * @param
	 *        	[String => Mixed] $opts
	 * @return [String => Mixed]
	 *         @override Event
	 */
	public function read(array $opts) {
		$session = $this->repo->readChatSession ( $opts ['other'] );
		
		if (empty ( $session )) {
			throw new Exception ();
		}
		
		return array_merge ( parent::read ( $opts ), [ 
				'chatsession' => $session,
				'module' => $this->repo->readModule ( $opts ['objectid'], $opts ['objecttable'] ) 
		] );
	}
}