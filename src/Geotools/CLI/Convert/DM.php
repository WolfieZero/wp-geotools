<?php

/**
 * This file is part of the Geotools library.
 *
 * (c) Antoine Corcy <contact@sbin.dk>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Geotools\CLI\Convert;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Formatter\OutputFormatterStyle;
use Geotools\Geotools;
use Geotools\Coordinate\Coordinate;
use Geotools\Coordinate\Ellipsoid;
use Geotools\Convert\ConvertInterface;

/**
 * Command-line convert:dm class
 *
 * @author Antoine Corcy <contact@sbin.dk>
 */
class DM extends Command
{
    protected function configure()
    {
        $availableEllipsoids = Ellipsoid::getAvailableEllipsoidNames();

        $this
            ->setName('convert:dm')
            ->setDescription('Convert and format decimal degrees coordinates to decimal minutes coordinate')
            ->addArgument('coordinate', InputArgument::REQUIRED, 'The "Lat,Long" coordinate')
            ->addOption('format', null, InputOption::VALUE_REQUIRED,
                'If set, the format of the converted decimal minutes coordinate', ConvertInterface::DEFAULT_DM_FORMAT)
            ->addOption('ellipsoid', null, InputOption::VALUE_REQUIRED,
                'If set, the name of the ellipsoid to use', Ellipsoid::WGS84)
            ->setHelp(<<<EOT
<info>Available ellipsoids</info>: $availableEllipsoids

<info>Example with an output format</info>:

    %command.full_name% "40.446195, -79.948862" <comment>--format="%P%D°%N %p%d°%n"</comment>

<info>Example with FISCHER_1968 ellipsoid</info>:

    %command.full_name% "40.446195, -79.948862" <comment>--ellipsoid=FISCHER_1968</comment>
EOT
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $ellipsoid  = Ellipsoid::createFromName($input->getOption('ellipsoid'));
        $coordinate = new Coordinate($input->getArgument('coordinate'), $ellipsoid);
        $geotools   = new Geotools();

        $output->getFormatter()->setStyle('value', new OutputFormatterStyle('green', 'black'));
        $output->writeln(sprintf(
            '<value>%s</value>',
            $geotools->convert($coordinate)->toDM($input->getOption('format'))
        ));
    }
}
