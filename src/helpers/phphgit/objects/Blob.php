<?php
/**
 * This file is part of the holonet project management software
 * (c) Matthias Lantsch.
 *
 * @license http://opensource.org/licenses/gpl-license.php  GNU Public License
 * @author  Matthias Lantsch <matthias.lantsch@bluewin.ch>
 */

namespace holonet\hgit\helpers\phphgit\objects;

use holonet\hgit\helpers\phphgit\Repository;

class Blob extends GitFile {
	/**
	 * The size of the blob in bytes.
	 */
	public int $size;

	protected ?string $content = null;

	public function __construct(Repository $repo, string $hash, string $filename, ?int $size = null) {
		parent::__construct($repo, $hash, $filename);

		//if not given, get the size with a separate command
		if ($size === null) {
			$size = (int)($this->execGit("cat-file -s {$this->hash}"));
		}

		$this->size = $size;
	}

	public function __toString(): string {
		return $this->getContent();
	}

	/**
	 * @return string binary blob string
	 */
	public function getContent(): string {
		if (!isset($this->content)) {
			$this->content = $this->execGit("cat-file blob {$this->hash}");
		}

		return $this->content;
	}

	/**
	 * @return string size neatly formatted string byte count with BKMGTP
	 */
	public function getFileSize(): string {
		$sz = 'BKMGTP';
		$factor = (int)((mb_strlen((string)$this->size) - 1) / 3);
		/** @psalm-suppress InvalidArrayOffset */
		return sprintf('%.2f', $this->size / 1024 ** $factor).@$sz[$factor];
	}

	public function type(): string {
		return 'blob';
	}
}
