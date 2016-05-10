<?php
namespace In2code\In2studycourses\ViewHelpers;

class ForViewHelper {

	/**
	 * @param array $for
	 * @param string $as
	 * @param string $letter
	 * @return array
	 */
	public function render($for, $as, $letter = 'letter') {
		$sortedStudyCourses = array();
		/** @var \In2code\In2studyfinder\Domain\Model\StudyCourse $studyCourse */
		foreach($for as $studyCourse) {
			$sortedStudyCourses[substr($studyCourse->getTitle(), 0, 1)][] = $studyCourse;
		}
		$results = array();
		foreach($sortedStudyCourses as $capitalLetter => $course) {
			$this->setVariable($letter, $capitalLetter);
			$this->setVariable($as, $course);
			$results[] = $this->renderChildren();
		}
		return implode($results);
	}
}
