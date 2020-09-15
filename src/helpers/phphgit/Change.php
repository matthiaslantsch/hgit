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

class Change {
	public string $filename;

	public ?Blob $newBlob;

	public ?Blob $oldBlob;

	public Repository $repository;

	public string $type;

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
