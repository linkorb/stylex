<?php

namespace Stylex\Command;

use Symfony\Component\Console\Helper\DescriptorHelper;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Command\Command;
use Stylex\Loader\DirectoryEntityLoader;
use Stylex\Generator;
use RuntimeException;

class LoadCommand extends Command
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this->ignoreValidationErrors();

        $this
            ->setName('load')
            ->setDescription('Load a stylex guide')
            ->addOption(
                'path',
                'p',
                InputOption::VALUE_REQUIRED,
                'Configuration path',
                null
            )
        ;
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $path = $input->getOption('path');
        if (!$path) {
            $path = getcwd();
        }
        if (!file_exists($path)) {
            throw new RuntimeException("Path not found: " . $configFilename);
        }
        
        $loader = new DirectoryEntityLoader();
        $guide = $loader->load($path);

        $output->writeLn(json_encode($guide, JSON_PRETTY_PRINT|JSON_UNESCAPED_SLASHES));
    }
}
