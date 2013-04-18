<?php
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