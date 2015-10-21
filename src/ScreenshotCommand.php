<?php 

namespace Coderdojo\Delorean;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Carbon\Carbon;
use GuzzleHttp\Client as Api;
use Exception;

class ScreenshotCommand extends Command {

    /**
     * Configure the command.
     */
    public function configure()
    {
        $this->setName('screenshot')
             ->setDescription('Generate screenshots')
             ->addArgument('url', InputArgument::REQUIRED)
             ->addOption('years', null, InputOption::VALUE_OPTIONAL, 'Number of years to go back in time');
    }

    /**
     * Execute the command.
     *
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return void
     */
    public function execute(InputInterface $input, OutputInterface $output)
    {
        $url = $input->getArgument('url');
        $url = $this->filterUrl($url);

        $years = (int) $input->getOption('years') ?: 10;

        $path = dirname(__DIR__).'/data';

        try {

            $this->savePresentDay($url, $path);

            $this->saveYearsAgo($url, $years, $path);

            $output->writeln('Saved screenshots');

        } catch ( Exception $e ) {
            $output->writeln($e->getMessage());
        }
    }

    protected function savePresentDay($url, $path)
    {
        $screenshot = new ScreenshotGenerator;

        $image = $screenshot->save($url, $path.'/present.png');
    }

    protected function saveYearsAgo($url, $years, $path)
    {
        $screenshot = new ScreenshotGenerator;

        $timestamp = Carbon::now()->subYears($years)->format('Ymd');

        $url = $this->getSnapshotUrl($url, $timestamp);

        $image = $screenshot->save($url, $path.'/past.png');
    }

    protected function getSnapshotUrl($url, $timestamp = null)
    {
        $url = str_replace(['http://','https://'], '', $url);

        $query = ['url' => $url];

        if ( ! is_null($timestamp) ) {
            $query['timestamp'] = $timestamp;
        }

        $client = new Api;
        $response = $client->get('http://archive.org/wayback/available', [
            'query' => $query
        ]);

        if ( $response->getStatusCode() !== 200 ) {
            throw new Exception('Error with archive.org');
        }

        $body = $response->getBody();
        $json = json_decode($body, true);

        $snapshots = $json['archived_snapshots'];

        if ( empty($snapshots) ) {
            throw new Exception('There are no snapshots of '.$url);
        }

        return $snapshots['closest']['url'];
    }

    protected function filterUrl($url)
    {
        if ( strpos($url, 'https') === 0 ) {
            $url = 'http'.substr($url, 5);
        }

        if ( strpos($url, 'http') !== 0 ) {
            $url = 'http://'.$url;
        }

        if ( filter_var($url, FILTER_VALIDATE_URL) === FALSE ) {
            throw new \RuntimeException($url . 'is not a valid URL');
        }

        return $url;
    }

}