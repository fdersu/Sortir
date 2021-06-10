<?php
namespace App\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class fixturesReloadCommand permet de lancer un script de commande de façon automatique
 * Il suffit dans une invite de commande de faire ceci : php bin/console app:fixturesReload
 * @package App\Command
 */
class fixturesReloadCommand extends Command
{
    protected function configure()
    {
        $this
            // the name of the command (the part after "bin/console")
            ->setName('app:fixturesReload')

            // the short description shown while running "php bin/console list"
            ->setDescription('Drop/Create Database and load Fixtures ....')

            // the full command description shown when running the command with
            // the "--help" option
            ->setHelp('This command allows you to load dummy data by recreating database and loading fixtures...');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $application = $this->getApplication();
        $application->setAutoExit(false);

        $output->writeln([
            '===================================================',
            '*********        Dropping DataBase        *********',
            '===================================================',
            '',
        ]);

        $options = array('command' => 'doctrine:database:drop',"--force" => true);
        try {
            $application->run(new \Symfony\Component\Console\Input\ArrayInput($options));
        } catch (\Exception $e) {
            $output->writeln([
                '===================================================',
                '*********       Error         *********',
                '===================================================',
                '',
            ]);
            $output->writeln([$e->getCode()
                ,
            ]);
            $output->writeln([$e->getMessage()
                ,
            ]);
            $output->writeln([$e->getTraceAsString()
                ,
            ]);
        }


        $output->writeln([
            '===================================================',
            '*********        Creating DataBase        *********',
            '===================================================',
            '',
        ]);

        $options = array('command' => 'doctrine:database:create',"--if-not-exists" => true);
        try {
            $application->run(new \Symfony\Component\Console\Input\ArrayInput($options));
        } catch (\Exception $e) {
            $output->writeln([
                '===================================================',
                '*********       Error         *********',
                '===================================================',
                '',
            ]);
            $output->writeln([$e->getCode()
                ,
            ]);
            $output->writeln([$e->getMessage()
                ,
            ]);
            $output->writeln([$e->getTraceAsString()
                ,
            ]);
        }

        $output->writeln([
            '===================================================',
            '*********         Updating Schema         *********',
            '===================================================',
            '',
        ]);

        //Create de Schema
        $options = array('command' => 'doctrine:schema:update',"--force" => true);
        try {
            $application->run(new \Symfony\Component\Console\Input\ArrayInput($options));
        } catch (\Exception $e) {
            $output->writeln([
                '===================================================',
                '*********       Error         *********',
                '===================================================',
                '',
            ]);
            $output->writeln([$e->getCode()
                ,
            ]);
            $output->writeln([$e->getMessage()
                ,
            ]);
            $output->writeln([$e->getTraceAsString()
                ,
            ]);
        }

        $output->writeln([
            '===================================================',
            '*********          Load Fixtures          *********',
            '===================================================',
            '',
        ]);

        //Loading Fixtures
        $options = array('command' => 'doctrine:fixtures:load',"--no-interaction" => true);
        try {
            $application->run(new \Symfony\Component\Console\Input\ArrayInput($options));
        } catch (\Exception $e) {
            $output->writeln([
                '===================================================',
                '*********       Error         *********',
                '===================================================',
                '',
            ]);
            $output->writeln([$e->getCode()
                ,
            ]);
            $output->writeln([$e->getMessage()
                ,
            ]);
            $output->writeln([$e->getTraceAsString()
                ,
            ]);
        }
        return 1;
    }
}