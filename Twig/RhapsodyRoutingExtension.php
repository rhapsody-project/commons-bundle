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
namespace Rhapsody\CommonsBundle\Twig;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

/**
 *
 * @author 	  Sean W. Quinn
 * @category  Rhapsody CommonsBundle
 * @package   Rhapsody\CommonsBundle\Twig
 * @copyright Copyright (c) 2013 Rhapsody Project
 * @license   http://opensource.org/licenses/MIT
 * @version   $Id$
 * @since     1.0
 */
class RhapsodyRoutingExtension extends \Twig_Extension
{

	private $container;
	private $generator;

	public function __construct(ContainerInterface $container, UrlGeneratorInterface $generator)
	{
		$this->container = $container;
		$this->generator = $generator;
	}

	public function getFunctions()
	{
		$functions = array();

		$functions['pageurl'] = new \Twig_Function_Method($this, 'getPageUrl');
		$functions['pagepath'] = new \Twig_Function_Method($this, 'getPagePath');
		return $functions;
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