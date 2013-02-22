<?php
/* Copyright (c) 2013 Rhapsody Project
 *
 * Licensed under the MIT License (http://opensource.org/licenses/MIT)
 *
 * Permission is hereby granted, free of charge, to any
 * person obtaining a copy of this software and associated
 * documentation files (the "Software"), to deal in the
 * Software without restriction, including without limitation
 * the rights to use, copy, modify, merge, publish,
 * distribute, sublicense, and/or sell copies of the Software,
 * and to permit persons to whom the Software is furnished
 * to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice
 * shall be included in all copies or substantial portions of
 * the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY
 * KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE
 * WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR
 * PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS
 * OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR
 * OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT
 * OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE
 * SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
 */
namespace Rhapsody\CommonsBundle\Tools;

/**
 *
 * @author 	  Sean W. Quinn
 * @category  Rhapsody CommonsBundle
 * @package   Rhapsody\CommonsBundle\Tools
 * @copyright Copyright (c) 2013 Rhapsody Project
 * @license   http://opensource.org/licenses/MIT
 * @version   $Id$
 * @since     1.0
 */
class UniqueIdGenerator
{
	const SCHEMA_UUID = 'uuid';
	const SCHEMA_GUID = 'guid';
	const SCHEMA_MONGO_ID = 'mongoid';

	/**
	 * The instance of the generator.
	 * @var UniqueIdGenerator
	 * @access private
	 * @static
	 */
	private static $instance = null;

	private function __construct()
	{
		// Empty.
	}

	public static function instance()
	{
		if (self::$instance === null) {
			self::$instance = new UniqueIdGenerator();
		}
		return self::$instance;
	}

	public static function generate($schema, $version = null)
	{
		$generator = UniqueIdGenerator::instance();
		$schema = trim(strtolower($schema));
		if ($schema === self::SCHEMA_MONGO_ID) {
			return $generator->generateMongoId();
		}
		if ($schema === self::SCHEMA_UUID) {
			return $generator->generateUUID($version);
		}

		throw new \InvalidArgumentException('Unknown schema: '.$schema);
	}

	public function generateMongoId()
	{
		return new \MongoId();
	}

	public function generateUUID($version = null)
	{
        $guid = $this->generateV4();
        return $this->generateV5($guid, php_uname('n'));
	}

	// ** UUID Generation functions should __probably__ be moved to their own class...
	/**
	 * Checks that a given string is a valid uuid.
	 *
	 * @param string $uuid The string to check.
	 * @return boolean
	 */
	public function isValid($guid)
	{
		return preg_match('/^\{?[0-9a-f]{8}\-?[0-9a-f]{4}\-?[0-9a-f]{4}\-?[0-9a-f]{4}\-?[0-9a-f]{12}\}?$/i', $guid) === 1;
	}

	/**
	 * Generates a v4 GUID
	 *
	 * @return string
	 */
	public function generateV4()
	{
		return sprintf('%04x%04x%04x%04x%04x%04x%04x%04x',
				// 32 bits for "time_low"
				mt_rand(0, 0xffff), mt_rand(0, 0xffff),

				// 16 bits for "time_mid"
				mt_rand(0, 0xffff),

				// 16 bits for "time_hi_and_version",
				// four most significant bits holds version number 4
				mt_rand(0, 0x0fff) | 0x4000,

				// 16 bits, 8 bits for "clk_seq_hi_res",
				// 8 bits for "clk_seq_low",
				// two most significant bits holds zero and one for variant DCE1.1
				mt_rand(0, 0x3fff) | 0x8000,

				// 48 bits for "node"
				mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff));
	}

	/**
	 * Generates a v5 GUID
	 *
	 * @param string $namespace The GUID to seed with
	 * @param string $salt The string to salt this new UUID with
	 * @return string
	 */
	public function generateV5($namespace, $salt)
	{
		if (!$this->isValid($namespace)) {
			throw new \Exception('Provided $namespace is invalid: ' . $namespace);
		}

		// Get hexadecimal components of namespace
		$nhex = str_replace(array('-','{','}'), '', $namespace);

		// Binary Value
		$nstr = '';

		// Convert Namespace UUID to bits
		for ($i = 0; $i < strlen($nhex); $i += 2) {
			$nstr .= chr(hexdec($nhex[$i] . $nhex[$i+1]));
		}

		// Calculate hash value
		$hash = sha1($nstr . $salt);

		$guid = sprintf('%08s%04s%04x%04x%12s',
				// 32 bits for "time_low"
				substr($hash, 0, 8),

				// 16 bits for "time_mid"
				substr($hash, 8, 4),

				// 16 bits for "time_hi_and_version",
				// four most significant bits holds version number 3
				(hexdec(substr($hash, 12, 4)) & 0x0fff) | 0x3000,

				// 16 bits, 8 bits for "clk_seq_hi_res",
				// 8 bits for "clk_seq_low",
				// two most significant bits holds zero and one for variant DCE1.1
				(hexdec(substr($hash, 16, 4)) & 0x3fff) | 0x8000,

				// 48 bits for "node"
				substr($hash, 20, 12)
		);

		return $guid;
	}
}