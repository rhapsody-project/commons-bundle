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

use Rhapsody\CommonsBundle\Tools\UniqueIdGenerator;
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
class UniqueIdGeneratorCommand extends Command
{
	/**
	 * @see Command
	 */
	protected function configure()
	{
		$this
		->setName('rhapsody:tools:uniqueid:generate')
		->setDefinition(array(
				new InputOption('schema', '-sc', InputOption::VALUE_OPTIONAL, 'The type of ID to generate.'),
				new InputOption('quantity', '-qty', InputOption::VALUE_OPTIONAL, 'The nuber of UUIDs to generate.'),
				//new InputOption('version', '', InputOption::VALUE_OPTIONAL, 'The version of the UUIDs to generate.'),
		))
		->setDescription('.')
		->setHelp(<<<EOF
TBW
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
			$schema = $input->hasOption('schema') ? $input->getOption('schema') : UniqueIdGenerator::SCHEMA_UUID;
			$qty    = $input->hasOption('quantity') ? $input->getOption('quantity') : 1;
			$ver    = 5; //$input->hasOption('version') ? $input->getOption('version') : 5;

			$generator = UniqueIdGenerator::instance();
			for ($i = 0; $i < $qty; $i++) {
				$id = $generator->generate($schema, $ver);
				$output->writeln($id);
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
}