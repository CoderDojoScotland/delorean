<?php

namespace Coderdojo\Delorean;

use Symfony\Component\Process\Process;
use RuntimeException;

class ScreenshotGenerator {

    /**
     * Get an file path for the url after saving screenshot
     *
     * @param  string $url
     * @param  string|null $path
     * @return string
     */
    public function save($url, $path = null)
    {
        $path = $path ?: __DIR__;

        return $this->writeImage($url, $path);
    }

    /**
     * Write the raw PNG bytes for the screenshot via PhantomJS.
     *
     * @param string $url
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

    /**
     * Get the folder name for the installed system.
     *
     * @return string
     */
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
            throw new RuntimeException('Unknown operating system.');
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