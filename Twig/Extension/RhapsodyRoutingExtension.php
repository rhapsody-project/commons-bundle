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

use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Routing\Generator\UrlGenerator;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

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
class RhapsodyRoutingExtension extends \Twig_Extension
{

	/**
	 * @var Symfony\Component\DependencyInjection\ContainerInterface
	 * @access protected
	 */
	private $container;

	/**
	 * @var Symfony\Component\Routing\Generator\UrlGeneratorInterface
	 * @access protected
	 */
	private $generator;

	/**
	 *
	 * @param ContainerInterface $container
	 * @param UrlGeneratorInterface $generator
	 */
	public function __construct(ContainerInterface $container, UrlGeneratorInterface $generator)
	{
		$this->container = $container;
		$this->generator = $generator;
	}

	public function doParseUrl($route, array $params = array(), $component = null)
	{
		$url = $this->generator->generate($route, $params, UrlGenerator::ABSOLUTE_URL);
		$components = parse_url($url);
		if (!empty($component) && array_key_exists($component, $components)) {
			return $components[$component];
		}
		return $components;
	}

	public function getFunctions()
	{
		return array(
			new \Twig_SimpleFunction('pageurl', array($this, 'getPageUrl')),
			new \Twig_SimpleFunction('pagepath', array($this, 'getPagePath')),
			new \Twig_SimpleFunction('parse_url', array($this, 'doParseUrl')),
		);
	}

    public function getPagePath()
    {
    	$router  = $this->container->get('router');
    	$request = $this->container->get('request');

    	$name = $request->attributes->get('_route');
    	$params = $request->query->all();
    	foreach ($router->getRouteCollection()->get($name)->compile()->getVariables() as $variable) {
    		$params[$variable] = $request->attributes->get($variable);
    	}
        return $this->generator->generate($name, $params, false);
    }

    public function getPageUrl()
    {
    	$router  = $this->container->get('router');
    	$request = $this->container->get('request');

    	$name = $request->attributes->get('_route');
    	$params = $request->query->all();
    	foreach ($router->getRouteCollection()->get($name)->compile()->getVariables() as $variable) {
    		$params[$variable] = $request->attributes->get($variable);
    	}
        return $this->generator->generate($name, $params, true);
    }


	public function getName()
	{
		return 'rhapsody_commons_twig_routing_extension';
	}

}
