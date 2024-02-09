<?php

namespace Symfony\Upgrade\Console\Command;

use PhpCsFixer\Error\ErrorsManager;
use PhpCsFixer\Finder;
use Symfony\CS\Fixer\Contrib\OrderedUseFixer;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Stopwatch\Stopwatch;
use Symfony\Upgrade\Fixer;

class FixCommand extends Command
{
    protected function configure()
    {
        $this
            ->setName('fix')
            ->setDefinition(
                [
                    new InputArgument('path', InputArgument::REQUIRED),
                    new InputOption('dry-run', '', InputOption::VALUE_NONE, 'Only shows which files would have been modified'),
                    new InputOption('no-use-reorder', '', InputOption::VALUE_NONE, 'Dot not reorder USE statements alphabetically'),
                ]
            )
            ->setDescription('Fixes a directory or a file')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $errorsManager = new ErrorsManager();
        $stopwatch = new Stopwatch();

        $this->fixer = new Fixer(
            $this->getFinder($input->getArgument('path')),
            $errorsManager,
            $stopwatch
        );
        $this->fixer->registerBuiltInFixers();
        
        if (! $input->getOption('no-use-reorder')) {
            $this->fixer->addFixer(new OrderedUseFixer());
        }

        $stopwatch->start('fixFiles');

        $changed = $this->fixer->fix(
            $input->getOption('dry-run')
        );

        $stopwatch->stop('fixFiles');

        $verbosity = $output->getVerbosity();

        $i = 1;

        foreach ($changed as $file => $fixResult) {
            $output->write(sprintf('%4d) %s', $i++, $file));

            if (OutputInterface::VERBOSITY_VERBOSE <= $verbosity) {
                $output->write(sprintf(' (<comment>%s</comment>)', implode(', ', $fixResult)));
            }

            $output->writeln('');
        }

        if (OutputInterface::VERBOSITY_DEBUG <= $verbosity) {
            $output->writeln('Fixing time per file:');

            foreach ($stopwatch->getSectionEvents('fixFile') as $file => $event) {
                if ('__section__' === $file) {
                    continue;
                }

                $output->writeln(sprintf('[%.3f s] %s', $event->getDuration() / 1000, $file));
            }

            $output->writeln('');
        }

        $fixEvent = $stopwatch->getEvent('fixFiles');
        $output->writeln(sprintf('Fixed all files in %.3f seconds, %.3f MB memory used', $fixEvent->getDuration() / 1000, $fixEvent->getMemory() / 1024 / 1024));

        if (!$errorsManager->isEmpty()) {
            $output->writeLn('');
            $output->writeLn('Files that were not fixed due to internal error:');

            foreach ($errorsManager->getErrors() as $i => $error) {
                $output->writeLn(sprintf('%4d) %s', $i + 1, $error['filepath']));
                $output->writeln($error['message']);
            }
        }

        return empty($changed) ? 0 : 1;
    }

    private function getFinder($path)
    {
        if (is_file($path)) {
            return new \ArrayIterator([new \SplFileInfo($path)]);
        }

        $finder = new Finder();
        $finder->in($path);

        return $finder;
    }
}
