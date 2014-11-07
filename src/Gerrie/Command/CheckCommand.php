<?php
/**
 * This file is part of the Gerrie package.
 *
 * (c) Andreas Grunwald <andygrunwald@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Gerrie\Command;

use Gerrie\Check\CurlExtensionCheck;
use Gerrie\Check\PDOMySqlExtensionCheck;
use Gerrie\Check\SSHCheck;
use Gerrie\Check\CheckInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class CheckCommand extends Command
{
    /**
     * OK sign from unicode table
     *
     * @link http://apps.timwhitlock.info/emoji/tables/unicode
     * @var string
     */
    const EMOJI_OK = "\xE2\x9C\x85";

    /**
     * Failure sign from unicode table
     *
     * @link http://apps.timwhitlock.info/emoji/tables/unicode
     * @var string
     */
    const EMOJI_FAILURE = "\xE2\x9D\x8C";

    /**
     * Stores the overall result of all checks.
     *
     * @var bool
     */
    private $overallResult = true;

    protected function configure()
    {
        $this
            ->setName('gerrie:check')
            ->setDescription('Checks if the setup is working');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        // Run gerrie:create-database-Command
        $output->writeln('<info>Gerrie will check if the current setup will work as expected.</info>');
        $output->writeln('<info>Lets start!</info>');

        $output->writeln('');
        $output->writeln('<comment>System:</comment>');

        // Check if curl is installed
        $curlCheck = new CurlExtensionCheck();
        $this->checkProperty($output, $curlCheck);

        // Check if PDO and MySQL are installed
        $pdoCheck = new PDOMySqlExtensionCheck();
        $this->checkProperty($output, $pdoCheck);

        // Check if SSH is installed
        $sshCheck = new SSHCheck();
        $this->checkProperty($output, $sshCheck);

        $output->writeln('');
        $output->writeln('<comment>Connection:</comment>');

        $output->writeln('');
        $output->writeln('<comment>Config:</comment>');
        /*
        $output->writeln('');
        $output->writeln("Check mark: \xE2\x9C\x85");
        $output->writeln("Heart: \xE2\x9D\xA4 \xF0\x9F\x92\x93");
        $output->writeln("Cross mark: \xE2\x9D\x8C");
        $output->writeln("Coffee: \xE2\x98\x95");
        $output->writeln("One beer: \xF0\x9F\x8D\xBA");
        $output->writeln("Two beer: \xF0\x9F\x8D\xBB");
        $output->writeln("Two beer: \xE2\x9D\x93");
        $output->writeln('');
        $output->writeln("Two beer: \xE2\x9D\x93");
        */

        // SSH Connection works (only with host, request Gerrie version)

        // Curl Connection works (only with host, request Gerrie version)

        // MySQL Connection works

        // Config file found
        // Config file correct
    }

    /**
     * Checks a single property
     *
     * @param OutputInterface $output
     * @param CheckInterface $property
     * @return bool
     */
    protected function checkProperty(OutputInterface $output, CheckInterface $property)
    {
        $result = $property->check();

        if($result === false) {
            $outputLevel = 'error';
            $sign = self::EMOJI_FAILURE;
            $message = $property->getFailureMessage();

            // The default value from overallResult is true.
            // So it is enough to set it to false once.
            $this->overallResult = false;

        } elseif ($result === true) {
            $outputLevel = 'info';
            $sign = self::EMOJI_OK;
            $message = $property->getSuccessMessage();
        }

        $outputMessage = "  <info>%s</info>   <%s>%s</%s>";
        $outputMessage = sprintf($outputMessage, $sign, $outputLevel, $message, $outputLevel);
        $output->writeln($outputMessage);

        return $result;
    }
}