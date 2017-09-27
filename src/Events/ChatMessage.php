<?php

namespace LogExpander\Events;

class ChatMessage extends Event {
	/**
	 * Reads data for an event.
	 *
	 * @param
	 *        	[String => Mixed] $opts
	 * @return [String => Mixed]
	 *         @override Event
	 */
	public function read(array $opts) {
		$message = $this->repo->readObject ( $opts ['objectid'], $opts ['objecttable'] );
		
		return array_merge ( parent::read ( $opts ), [ 
				'chatmessage' => $message,
				'module' => $this->repo->readModule ( $message->chatid, 'chat' ) 
		] );
	}
}