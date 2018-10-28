<?php

namespace Stylex\Command;

use Symfony\Component\Console\Helper\DescriptorHelper;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Command\Command;
use Stylex\Loader\DirectoryEntityLoader;
use LightnCandy\LightnCandy;
use RuntimeException;

class TemplateCommand extends Command
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this->ignoreValidationErrors();

        $this
            ->setName('template')
            ->setDescription('Render a stylex guide through a template file')
            ->addArgument(
                'templatePath',
                InputArgument::REQUIRED,
                'Path with template files'
            )
            ->addArgument(
                'outputPath',
                InputArgument::REQUIRED,
                'Output or build path'
            )
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
            throw new RuntimeException("Path not found: " . $path);
        }

        $templatePath = $input->getArgument('templatePath');
        if (!file_exists($templatePath)) {
            throw new RuntimeException("TemplatePath not found: " . $templatePath);
        }

        $outputPath = $input->getArgument('outputPath');
        if (!file_exists($outputPath)) {
            mkdir($outputPath, 0777, true);
        }

        $loader = new DirectoryEntityLoader();
        $guide = $loader->load($path);

        $template = file_get_contents($templatePath . '/index.hbs');

        $php = LightnCandy::compile($template, Array(
            'flags' => LightnCandy::FLAG_RENDER_DEBUG | LightnCandy::FLAG_HANDLEBARSJS_FULL
        ));
        $renderer = LightnCandy::prepare($php);

        $extension = '.html';
        file_put_contents($outputPath . '/index' . $extension,  $renderer($guide));

    }
}
