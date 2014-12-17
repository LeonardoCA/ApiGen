<?php

/**
 * This file is part of the ApiGen (http://apigen.org)
 *
 * For the full copyright and license information, please view
 * the file license.md that was distributed with this source code.
 */

namespace ApiGen\FileSystem;

use Nette;


class FileSystem
{

	/**
	 * @param string $path
	 * @return string
	 */
	public static function normalizePath($path)
	{
		return str_replace(['\\'], '/', $path);
	}


	/**
	 * @param string $path
	 * @return string
	 */
	public static function forceDir($path)
	{
		@mkdir(dirname($path), 0755, TRUE);
		return $path;
	}


	/**
	 * @param string $path
	 */
	public static function deleteDir($path)
	{
		self::purgeDir($path);
		rmdir($path);
	}


	/**
	 * @param string $path
	 */
	public static function purgeDir($path)
	{
		if ( ! is_dir($path)) {
			mkdir($path, 0755, TRUE);
		}

		foreach (Nette\Utils\Finder::find('*')->from($path)->childFirst() as $item) {
			/** @var \SplFileInfo $item */
			if ($item->isDir()) {
				rmdir($item);

			} elseif ($item->isFile()) {
				unlink($item);
			}
		}
	}


	/**
	 * @param string $path
	 * @param array $baseDirectories List of base directories
	 * @return string
	 */
	public static function getAbsolutePath($path, array $baseDirectories = [])
	{
		if (self::isAbsolutePath($path)) {
			return $path;
		}
		if (count($baseDirectories)) {
			foreach ($baseDirectories as $directory) {
				$fileName = $directory . '/' . $path;
				if (is_file($fileName)) {
					return realpath($fileName);
				}
			}
		}
		$path = FileSystem::normalizePath($path);
		if (file_exists($path)) {
			return realpath($path);
		}
		return $path;
	}


	/**
	 * @param string $path
	 * @return bool
	 */
	private static function isAbsolutePath($path)
	{
		if (preg_match('~/|[a-z]:~Ai', $path)) {
			return TRUE;
		}
		return FALSE;
	}

}