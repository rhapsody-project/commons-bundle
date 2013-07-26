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
namespace Rhapsody\CommonsBundle\Twig\Extension;

use Rhapsody\CommonsBundle\Model\TemplateManagerInterface;

use Rhapsody\CommonsBundle\Model\MarkupProcessor;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\Generator\UrlGenerator;

/**
 *
 * @author 	  Sean W. Quinn
 * @category  Rhapsody CommonsBundle
 * @package   Rhapsody\CommonsBundle\Twig\Extension
 * @copyright Copyright (c) 2013 Rhapsody Project
 * @license   http://opensource.org/licenses/MIT
 * @version   $Id$
 * @since     1.0
 */
class RhapsodyExtension extends \Twig_Extension
{

	/**
	 *
	 * @var unknown
	 */
	protected $markupProcessor;

	/**
	 *
	 * @var \Rhapsody\CommonsBundle\Model\TemplateManagerInterface
	 * @access protected
	 */
	protected $templateManager;

	public function __construct(MarkupProcessor $markupProcessor, TemplateManagerInterface $templateManager)
	{
		$this->markupProcessor = $markupProcessor;
		$this->templateManager = $templateManager;
	}

	/**
	 *
	 * @param unknown $source
	 * @param unknown $limit
	 * @param string $byWord
	 * @return Ambigous <string, unknown>
	 */
	public function doAbbreviate($source, $limit, $byWord = true)
	{
		$text = $this->doTruncate($source, $limit, $byWord);
		return $text.'...';
	}

	public function doAttribute($array = array(), $key)
	{
		if (array_key_exists($key, $array)) {
			return $key.'="'.$array[$key].'"';
		}
		return null;
	}

	public function doAttributeValue($array = array(), $key)
	{
		if (array_key_exists($key, $array)) {
			return $array[$key];
		}
		return null;
	}

	/**
	 *
	 * @param unknown $source
	 * @param unknown $limit
	 * @param string $byWord
	 * @return Ambigous <string, unknown>
	 */
	public function doTruncate($source, $limit, $byWord = true)
	{
		$text = $source;
		$threshold = $byWord === true ? str_word_count($text, 0) : strlen($text);
		if ($threshold > $limit) {
			$pos = $limit;
			if ($byWord === true) {
				$words = str_word_count($text, 2);
				$wordPos = array_keys($words);
				$pos = $wordPos[$limit];
			}
			$text = substr($text, 0, $pos);
		}
		return $text;
	}

	/**
	 * Returns an <tt>array</tt> of numbers from <tt>$start</tt> to
	 * <tt>$limit</tt>, provided that neither <tt>$start</tt> nor
	 * <tt>$limit</tt> exceed the ranges boundaries.
	 *
	 * @param mixed $start the first value of the sequence.
	 * @param mixed $limit the last value of the sequence.
	 * @param mixed $lowerBound the lower bound of the sequence, if
	 * 		<tt>$start</tt> is less than this value, <tt>$lowerBound</tt> will
	 * 		be the start of the sequence.
	 * @param mixed $upperBound the upper bound of the sequence. A negative
	 * 		value for the <tt>$upperBound</tt> means that it is unbounded on
	 * 		the right hand side of the range.
	 * @param mixed $step if a <tt>step</tt> value is given, it will be used as
	 * 		the increment between elements in the sequence. step should be given
	 * 		as a positive number. If not specified, step will default to 1.
	 * @return array
	 */
	public function doBoundedRange($start, $limit, $lowerBound = 0, $upperBound = -1, $step = 1)
	{
		$lower = $start;
		$upper = $limit;
		$carry = 0;
		if ($lower < $lowerBound) {
			$lower = $lowerBound;
			$upper += abs($lowerBound - $start);
		}

		if ($upper > $upperBound) {
			$upper = $upperBound < 0 ? $upper : $upperBound;
		}
		return range($lower, $upper, $step);
	}

	public function doMarkup($source, $markup)
	{
		if ($this->markupProcessor->supports($markup)) {
			return $this->markupProcessor->run($source, $markup);
		}
		throw new \InvalidArgumentException('The markup: '.$markup.' is unsupported');
	}

	public function doMarkupAndStripTags($source, $markup, $allow = '')
	{
		if ($this->markupProcessor->supports($markup)) {
			$str = $this->markupProcessor->run($source, $markup);
			return strip_tags($str);
		}
		throw new \InvalidArgumentException('The markup: '.$markup.' is unsupported');
	}

	/**
	 *
	 * @param unknown $index
	 * @param unknown $padding
	 * @param number $alignment the alignment of the padding; 0 = center,
	 * 		1 = left, and 2 = right.
	 * @param mixed $lowerBound
	 * @param mixed $upperBound
	 * @param number $step
	 * @return NULL
	 */
	public function doPaddedRange($index, $padding, $align = 'center', $lowerBound = 0, $upperBound = -1, $step = 1)
	{
		if (!is_int($padding)) {
			throw new \InvalidArgumentException('A padded range expects an integer value as the range padding. Given '.gettype($padding));
		}

		$range = $this->getRangeBoundaries($index, $padding, $align);
		return $this->doBoundedRange($range['left'], $range['right'], $lowerBound, $upperBound, $step);
	}

	public function doDaysSinceFilter($source)
	{
		$timestamp = $source instanceof \DateTime ? $source->getTimestamp() : $source;

		$now = strtotime('now');
		if ($timestamp <= strtotime('today')) {
			return 'Today';
		}
		if ($timestamp <= strtotime('yesterday')) {
			return 'Yesterday';
		}
		return round(abs($now - $timestamp) / (60 * 60 * 24));
	}

	public function doJsonFilter($source)
	{
		$object = json_decode($source);
		if ($object === null) {
			return '{}';
		}
		return $source;
	}

	/**
	 * (non-PHPdoc)
	 * @see Twig_Extension::getFilters()
	 */
	public function getFilters()
	{
		$filters = array(
			// ** math function filters
			new \Twig_SimpleFilter('floor', 'floor'),
			new \Twig_SimpleFilter('ceil', 'ceil'),
		);

		$filters['abbreviate'] = new \Twig_Filter_Method($this, 'doAbbreviate');
		$filters['days_since'] = new \Twig_Filter_Method($this, 'doDaysSinceFilter');
		$filters['json'] = new \Twig_Filter_Method($this, 'doJsonFilter', array('is_safe' => array('all')));
		$filters['markup'] = new \Twig_Filter_Method($this, 'doMarkup', array('is_safe' => array('all')));
		$filters['markup_striptags'] = new \Twig_Filter_Method($this, 'doMarkupAndStripTags', array('is_safe' => array('all')));
		$filters['truncate'] = new \Twig_Filter_Method($this, 'doTruncate');
		return $filters;
	}

	public function getFunctions()
	{
		$functions = array();

		$functions['attr'] = new \Twig_Function_Method($this, 'doAttribute', array('is_safe' => array('all')));
		$functions['attr_value'] = new \Twig_Function_Method($this, 'doAttributeValue');
		$functions['bounded_range'] = new \Twig_Function_Method($this, 'doBoundedRange');
		$functions['padded_range'] = new \Twig_Function_Method($this, 'doPaddedRange');
		$functions['rhapsody_template'] = new \Twig_Function_Method($this, 'renderTemplatedWidget', array('is_safe' => array('all')));
		$functions['rhapsody_template_block'] = new \Twig_Function_Method($this, 'renderTemplatedWidgetBlock', array('is_safe' => array('all')));
		return $functions;
	}

	/**
	 *
	 * @param mixed $align
	 */
	protected function getRangeAlignment($align = 0) {
		$align = strtolower(trim($align));
		if (in_array($align, array(1, 'left'))) return 'left';
		if (in_array($align, array(2, 'right'))) return 'right';
		return 'center';
	}

	protected function getRangeBoundaries($index, $padding, $align = 'center') {
		$align = $this->getRangeAlignment($align);
		if ($align === 'left') return array('left' => $index, 'right' => intval($index + $padding));
		if ($align === 'right') return array('left' => intval($index - $padding), 'right' => $index);

		$padding = floor($padding / 2);
		return array('left' => intval($index - $padding), 'right' => intval($index + $padding));
	}

	public function getName()
	{
		return 'rhapsody_commons_twig_core_extension';
	}


	/**
	 * <p>
	 * Renders a templated widget.
	 * </p>
	 *
	 * @param mixed $widget the widget to be rendered.
	 * @param array $options the options passed to be considered when rendering.
	 */
	public function renderTemplatedWidget($widget, array $options = array())
	{
		return $this->templateManager->render($widget);
	}

	public function renderTemplatedWigetBlock($block, $widget, $options)
	{
		return $this->templateManager->renderBlock($block, $widget);
	}
}