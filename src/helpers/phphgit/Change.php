<?php
/**
 * This file is part of the holonet project management software
 * (c) Matthias Lantsch.
 *
 * @license http://opensource.org/licenses/gpl-license.php  GNU Public License
 * @author  Matthias Lantsch <matthias.lantsch@bluewin.ch>
 */

namespace holonet\hgit\helpers\phphgit;

use holonet\hgit\helpers\phphgit\objects\Blob;

/**
 * The Change class logically represents a git change of a file.
 */
class Change {
	/**
	 * @var string the changed filename
	 */
	public $filename;

	/**
	 * @var Blob|null $newBlob the new blob object hash
	 */
	public $newBlob;

	/**
	 * @var Blob|null $oldBlob the old blob object hash
	 */
	public $oldBlob;

	/**
	 * @var Repository $repository Reference to the repository object this git object belongs to
	 */
	public $repository;

	/**
	 * @var string the change type character
	 */
	public $type;

	/**
	 * @param Repository $repository Reference to the git repository this change happened in
	 * @param string $oldBlob The old blob object hash
	 * @param string $newBlob The new blob object hash
	 * @param string $type Type of change indicating character
	 * @param string $filename The filename changed
	 */
	public function __construct(Repository $repository, string $oldBlob, string $newBlob, string $type, string $filename) {
		$this->type = $type;

		if ($type === 'A') {
			//new file created
			$this->oldBlob = null;
		} else {
			$this->oldBlob = new objects\Blob($repository, $oldBlob, $filename);
		}

		if ($type === 'D') {
			$this->newBlob = null;
		} else {
			$this->newBlob = new objects\Blob($repository, $newBlob, $filename);
		}

		$this->filename = $filename;
		$this->repository = $repository;
	}

	/**
	 * @return string git diff output
	 */
	public function getDiff(): string {
		if ($this->newBlob === null) {
			return '';
		}

		if ($this->oldBlob === null) {
			return $this->newBlob->getContent();
		}

		return $this->repository->execGit("diff {$this->oldBlob->hash} {$this->newBlob->hash}");
	}
}
