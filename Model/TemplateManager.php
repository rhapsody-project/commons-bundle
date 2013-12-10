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

/**
 *
 * @author Sean.Quinn
 *
 */
abstract class TemplateManager implements TemplateManagerInterface
{

	/**
	 * Map of templates.
	 * @var array
	 * @access protected
	 */
	protected $templateMap = array();

	/**
	 * Returns the managed template for a given object if one exists, looks for
	 * templates along the object's ancestors until it finds one. If no template
	 * is found, an <code>InvalidArgumentException</code> is thrown.
	 *
	 * @param unknown $object
	 * @throws \InvalidArgumentException
	 * @return multitype:
	 */
	public function getManagedTemplate($object)
	{
		// ** Collection to hold the list of class names that we are looking for templates in...
		$classes = array();

		// ** Get the object class and push it into the list of classes, also check to see if the array key exists.
		$objectClass = get_class($object);
		array_push($classes, $objectClass);
		if(array_key_exists($objectClass, $this->templateMap)) {
			return $this->templateMap[$objectClass];
		}

		// ** Search through the ancestors of the object for a matching template...
		$class = new \ReflectionClass($objectClass);
		$interfaces = $class->getInterfaceNames();
		while ($class = $class->getParentClass()) {
			$key = $class->getName();
			$classes[] = $key;
			if (array_key_exists($key, $this->templateMap)) {
				return $this->templateMap[$key];
			}
		}

		// ** Finally look for a template defined at the interface level...
		foreach ($interfaces as $interface) {
			$classes[] = $interface;
			if (array_key_exists($interface, $this->templateMap)) {
				return $this->templateMap[$interface];
			}
		}
		throw new \InvalidArgumentException('Unable to find template for object: '.$objectClass.' in the template manager. Looked for template in: '.implode(', ', $classes));
	}

	public function getManagedTemplates()
	{
		$templates = array_values($this->templateMap);
		return $templates;
	}

	/**
	 * Returns the view for the managed <tt>$object</tt>.
	 * @param mixed $object the object.
	 * @return the view.
	 */
	public function getView($object)
	{
		$template = $this->getManagedTemplate($object);
		return $template->getView();
	}

	public function addManagedTemplate(ManagedTemplate $template)
	{
		$class = $template->getClass();
		$this->templateMap[$class] = $template;
	}
}