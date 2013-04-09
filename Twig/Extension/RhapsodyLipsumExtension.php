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

use Rhapsody\CommonsBundle\Tools\LoremIpsumGenerator;

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
class RhapsodyLipsumExtension extends \Twig_Extension
{

	public function getFunctions()
	{
		$functions = array(
			'lipsum' => new \Twig_Filter_Method($this, 'generateLipsum', array('words' => 120, 'format' => 'plain', 'loremipsum' => true))
		);
		return $functions;
	}

	public function generateLipsum($words = 120, $format = 'plain', $loremipsum = true)
	{
		$lipsum = new LoremIpsumGenerator();
		return $lipsum->getContent($words, $format, $loremipsum);
	}

	public function getName()
	{
		return 'rhapsody_commons_twig_lipsum_extension';
	}

}