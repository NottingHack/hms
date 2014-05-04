<?php

require_once ('HmsContext.php');

class FileContext extends HmsContext {

/**
 * Given /^I copy all files from "(?P<source>.*)" to "(?P<destination>.)*)"$/
 */
	public function recursiveCopy($source, $dest, $permissions = 0755) {
		$this->_logger()->logInfo("Copying: $source to $dest");
		// Check for symlinks
		if (is_link($source)) {
			return symlink(readlink($source), $dest);
		}

		// Simple copy for a file
		if (is_file($source)) {
			return copy($source, $dest);
		}

		// Make destination directory
		if (!is_dir($dest)) {
			mkdir($dest, $permissions);
		}

		// Loop through the folder
		$dir = dir($source);
		while (false !== $entry = $dir->read()) {
			// Skip pointers
			if ($entry == '.' || $entry == '..') {
				continue;
			}

			// Deep copy directories
			if (!$this->recursiveCopy("$source/$entry", "$dest/$entry")) {
				// Error!
				return false;
			}
		}

		// Clean up
		$dir->close();
		return true;
	}

/**
 * Given /^I delete the "(?P<directory>.*)" directory$/
 */
	public function recursiveDelete($directory) {
		$this->_logger()->logInfo("Deleting: $directory");
		if (!file_exists($directory)) {
			return true;
		}
		// Simple copy for a file
		if (is_file($directory)) {
			return unlink($directory);
		}

		// Loop through the folder
		$dir = dir($directory);
		while (false !== $entry = $dir->read()) {
			// Skip pointers
			if ($entry == '.' || $entry == '..') {
				continue;
			}

			// Deep delete directories
			if (!$this->recursiveDelete("$directory/$entry")) {
				// Error!
				return false;
			}
		}

		// Clean up
		$dir->close();
		return rmdir($directory);
	}

/**
 * Given /^I move the "(?P<from>.)*)" directory to "(?P<to>.*)"$/
 */
	public function moveFolderTo($from, $to) {
		$this->_logger()->logInfo("Move folder from: $from to: $to");
		if ($this->recursiveDelete($to)) {
			return $this->recursiveCopy($from, $to);
		}
	}
}