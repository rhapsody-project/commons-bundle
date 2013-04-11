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
namespace Rhapsody\CommonsBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;

/**
 *
 * @author Sean.Quinn
 *
 */
class ManagedTemplateCompilerPass implements CompilerPassInterface
{
	//


	public function process(ContainerBuilder $container)
	{
		if (false === $container->hasDefinition('rhapsody_commons.template_manager')) {
			return;
		}

		$definition = $container->getDefinition('rhapsody_commons.template_manager');

		// Extensions must always be registered before everything else.
		// For instance, global variable definitions must be registered
		// afterward. If not, the globals from the extensions will never
		// be registered.
		$calls = $definition->getMethodCalls();
		$definition->setMethodCalls(array());
		foreach ($container->findTaggedServiceIds('rhapsody.commons.managed_template') as $id => $attributes) {
			$definition->addMethodCall('addManagedTemplate', array(new Reference($id)));
		}
		$definition->setMethodCalls(array_merge($definition->getMethodCalls(), $calls));
	}
}

