<?php
/**
 * This file is part of the holonet project management software
 * (c) Matthias Lantsch
 *
 * Class file for the Change class
 *
 * @package holonet project management tool
 * @license http://opensource.org/licenses/gpl-license.php  GNU Public License
 * @author  Matthias Lantsch <matthias.lantsch@bluewin.ch>
 */

namespace holonet\hgit\helpers\phphgit;

/**
 * The Change class represents a git change of a file
 *
 * @author  Matthias Lantsch
 * @version 2.0.0
 * @package HIS5\hgit\helpers\phphgit
 */
class Change {

	/**
	 * property for the old object blob hash
	 *
	 * @access public
	 * @var    string the old blob hash
	 */
	public $oldBlob;

	/**
	 * property for the new object blob hash
	 *
	 * @access public
	 * @var    string the new blob hash
	 */
	public $newBlob;

	/**
	 * property for a change type character
	 *
	 * @access public
	 * @var    string the change type character
	 */
	public $type;

	/**
	 * property for the changed filename
	 *
	 * @access public
	 * @var    string the changed filename
	 */
	public $filename;

	/**
	 * constructor method taking the name for the branch
	 *
	 * @access 	public
	 * @param   string the old blob object hash
	 * @param   string the new blob object hash
	 * @param   string a type of change determing character
	 * @param   string the filename changed
	 */
	public function __construct($repository, $oldBlob, $newBlob, $type, $filename) {
		$this->newBlob = new objects\Blob($repository, $newBlob, $filename);
		$this->type = $type;

		if($type == 'A') {
			//new file created
			$this->oldBlob = null;
		} else {
			$this->oldBlob = new objects\Blob($repository, $oldBlob, $filename);
		}

		$this->filename = $filename;
	}

}
