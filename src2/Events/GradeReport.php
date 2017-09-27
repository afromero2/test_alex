<?php

namespace LogExpander\Events;

class GradeReport extends Event {
	/**
	 * Reads data for an event.
	 *
	 * @param
	 *        	[String => Mixed] $opts
	 * @return [String => Mixed]
	 *         @override Event
	 */
	public function read(array $opts) {
		$gradeurl = $this->repo->getGradeReportUrl ( $opts ['target'], $opts ['userid'], $opts ['courseid'] );
		
		return array_merge ( parent::read ( $opts ), [ 
				'gradeurl' => $gradeurl 
		] );
	}
}