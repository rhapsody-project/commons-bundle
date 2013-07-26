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
namespace Rhapsody\CommonsBundle\Model;

use Rhapsody\Commons\Markup\MarkupParserInterface;

class MarkupProcessor
{

	/**
	 *
	 * @var array
	 * @access protected
	 */
	protected $parserMap = array();

	public function __construct()
	{
		// TODO
	}

	/**
	 *
	 * @param MarkupParserInterface $parser
	 */
	public function addParser(MarkupParserInterface $parser)
	{
		$markup = $parser->getName();
		$canonicalMarkup = $this->canonicalizeMarkup($markup);
		$this->parserMap[$canonicalMarkup] = $parser;
	}

	/**
	 *
	 * @param unknown $markup
	 * @return string
	 */
	private function canonicalizeMarkup($markup)
	{
		$canonicalMarkup = trim($markup);
		$canonicalMarkup =strtolower($canonicalMarkup);
		return $canonicalMarkup;
	}

	/**
	 *
	 * @param unknown $text
	 * @param unknown $markup
	 * @throws \IllegalArgumentException
	 * @return unknown
	 */
	public function run($text, $markup)
	{
		$canonicalMarkup = $this->canonicalizeMarkup($markup);
		if ($this->supports($canonicalMarkup)) {
			$parser = $this->parserMap[$canonicalMarkup];
			$transformed = $parser->parse($text);
			return $transformed;
		}
		throw new \IllegalArgumentException('Unable to find markup processor for: '.$markup.'. Please make sure the processor is registered.');
	}

	/**
	 * Checks to see if the given markup is supported by the markup processor.
	 * @param string $markup the markup language.
	 * @return boolean <tt>true</tt> if the markup language is supported;
	 * 		otherwise <tt>false</tt>.
	 */
	public function supports($markup)
	{
		$canonicalMarkup = $this->canonicalizeMarkup($markup);
		return array_key_exists($canonicalMarkup, $this->parserMap);
	}
}