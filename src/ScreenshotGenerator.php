<?php

namespace Coderdojo\Delorean;

use Carbon\Carbon;
use SplFileInfo;
use Symfony\Component\Process\Process;

class ScreenshotGenerator {

    /**
     * Get an SplFileInfo instance for the url with the given data.
     *
     * @param  string $url
     * @param  string|null $path
     * @return \SplFileInfo
     */
    public function save($url, $path = null)
    {
        $path = $path ?: __DIR__;

        return $this->writeImage($url, $path);
    }

    /**
     * Write the raw PNG bytes for the map via PhantomJS.
     *
     * @param Walk $walk
     * @param  string $path
     * @return string
     */
    protected function writeImage($url, $path)
    {
        $this->getPhantomProcess($url, $path)
             ->setTimeout(120)->mustRun();

        return $path;
    }

    /**
     * Get the PhantomJS process instance.
     *
     * @param  string  $html
     * @return \Symfony\Component\Process\Process
     */
    public function getPhantomProcess($html, $image)
    {
        $system = $this->getSystem();

        $phantom = 'bin/'.$system.'/phantomjs'.$this->getExtension($system);
        $js = 'src/screenshot.js';

        $cwd = dirname(__DIR__);

        return new Process($phantom.' '.$js.' '.$html.' '.$image, $cwd);
    }

    protected function getSystem()
    {
        $uname = strtolower(php_uname());

        if ( strpos($uname, 'darwin') !== FALSE ) {
            return 'macosx';
        } elseif ( strpos($uname, 'win') !== FALSE ) {
            return 'windows';
        } elseif ( strpos($uname, 'linux') !== FALSE ) {
            return PHP_INT_SIZE === 4 ? 'linux-i686' : 'linux-x86_64';
        } else {
            throw new \RuntimeException('Unknown operating system.');
        }
    }

    /**
     * Get the binary extension for the system.
     *
     * @param  string  $system
     * @return string
     */
    protected function getExtension($system)
    {
        return $system == 'windows' ? '.exe' : '';
    }

}