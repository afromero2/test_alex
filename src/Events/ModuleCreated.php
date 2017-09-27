<?php

namespace LogExpander\Events;

class ModuleCreated extends Event {
	/**
	 * Reads data for an event.
	 *
	 * @param
	 *        	[String => Mixed] $opts
	 * @return [String => Mixed]
	 *         @override Event
	 */
	public function read(array $opts) {
		$module = $this->repo->readNewModule ( $opts ['other'] );
		
		if ($module->type == 'tutorship') {
			$tutorship = $this->repo->readTutorModule ( $opts ['courseid'], $opts ['other'] );
			$periods = $this->repo->readTutorPeriods ( $opts ['courseid'] );
			
			return array_merge ( parent::read ( $opts ), [ 
					'module' => $module,
					'tutorship' => $tutorship,
					'periods' => $periods 
			] );
		} else {
			return array_merge ( parent::read ( $opts ), [ 
					'module' => $module 
			] );
		}
	}
}