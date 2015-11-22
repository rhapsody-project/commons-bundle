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

use Rhapsody\Commons\Markup\Markdown\Parser;

class RhapsodyMarkdownExtension extends \Twig_Extension
{

	public function getFilters()
	{
		return array(
			new \Twig_SimpleFilter('markdown', array($this, 'doMarkdown'), array('is_safe' => array('all'))),
			new \Twig_SimpleFilter('markdown_striptags', array($this, 'doMarkdownAndStripTags'), array('is_safe' => array('all'))),
		);
	}

	public function doMarkdown($source)
	{
		$parser = new Parser();
		return $parser->transform($source);
	}

	public function doMarkdownAndStripTags($source, $allow = '')
	{
		$parser = new Parser();
		$str = $parser->transform($source);
		return strip_tags($str);
	}

	public function getName()
	{
		return 'rhapsody_markdown_extension';
	}

}
