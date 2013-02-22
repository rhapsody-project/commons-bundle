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
namespace Rhapsody\CommonsBundle\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Formatter\OutputFormatterStyle;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * <p>
 * </p>
 *
 * @author 	  Sean W. Quinn
 * @category  Rhapsody CommonsBundle
 * @package   Rhapsody\CommonsBundle\Command
 * @copyright Copyright (c) 2013 Rhapsody Project
 * @license   http://opensource.org/licenses/MIT
 * @version   $Id$
 * @since     1.0
 */
class XslTransformCommand extends Command
{
	/**
	 * @see Command
	 */
	protected function configure()
	{
		$this
		->setName('rhapsody:xsl-transform')
		->setDefinition(array(
				new InputOption('file', '-f', InputOption::VALUE_REQUIRED | InputOption::VALUE_IS_ARRAY, 'The files to transform with the XSL.'),
				new InputOption('xsl', '-x', InputOption::VALUE_REQUIRED, 'The XSL file to use to transform the files.'),
				new InputOption('output', '-o', InputOption::VALUE_OPTIONAL, 'The file to output the results to, if not supplied defaults to: output'),
		))
		->setDescription('Transforms the file(s) with the specified XSL file.')
		->setHelp(<<<EOF
The <info>rhapsody:xsl:transform</info> command will transform the
passed file(s) using the specified XSL file as the transformer.
EOF
		);
	}

	/**
	 * {@inheritdoc}
	 */
	protected function execute(InputInterface $input, OutputInterface $output)
	{
		$environment = $input->getOption('env');

		try {
			$transformer = $input->getOption('xsl');
			$xsl = new \DOMDocument();
			$xsl->load($transformer);

			$xslt = new \XSLTProcessor();
			$xslt->registerPHPFunctions();
			$xslt->importStylesheet($xsl);

			$files = $input->getOption('file');
			foreach ($files as $file)
			{
				$output->writeln('Transforming file: '.$file);

				$doc = new \DOMDocument();
				$doc->load($file);

				$xml = $xslt->transformToXml($doc);
				$xmlobj = new \SimpleXMLElement($xml);
				$xmlobj->asXML('output.xml');
			}
		}
		catch (\Exception $ex) {
			$exception = new OutputFormatterStyle('red');
			$output->getFormatter()->setStyle('exception', $exception);

			$output->writeln("\n\n");
			$output->writeln('<exception>[Exception in: '.get_class($this).']</exception>');
			$output->writeln('<exception>Exception: '.get_class($ex).' with message: '.$ex->getMessage().'</exception>');
			$output->writeln('<exception>Stack Trace:</exception>');
			$output->writeln('<exception>'.$ex->getTraceAsString().'</exception>');
			exit(1);
		}
		exit(0);
	}

	private function errorHandler($xh, $error_level, $error_code, $messages)
	{
		// Empty.
	}
}