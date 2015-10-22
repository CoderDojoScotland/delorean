# Coderdojo Delorean Time Machine

This PHP command line (console) app allows you to generate screenshots of websites from the past and present, courtesy of [Wayback Machine](http://archive.org/web/) by The Internet Archive.

## Installation

Install this application through [Composer](getcomposer.org).

If you don't have Composer installed yet, run 

```
curl -sS https://getcomposer.org/installer | php
mv composer.phar /usr/local/bin/composer
```

Once you have Composer, you can install the app an it's depencies with

```
composer create-project coderdojo/delorean
```

Alternatively, if you prefer to clone this Git repository, you can install the dependencies only with the following (inside the app root).

```
composer install
```

### Requirements

This package is designed to work with PHP >= 5.6 over the command line.

## Usage

### Generate Screenshots

You can generate a past and present screenshot of a specific website by using the command: `php delorean screenshot <url> [--years=10]`

In other words,

```
php delorean screenshot http://google.com
```

will generate a screenshot for the present day, plus one from the past using the default number of years (which is 10).

To change the number of years ago for the past screenshot, you can pass this as an option.

```
php delorean screenshot http://google.com --years=5
```

## How it Works

The `delorean` file in the project root is a command line php script that registers a new [Symfony](http://symfony.com/) console application, currently with one command.

The `src/ScreenshotCommand.php` file contains the code that is run when you put `php delorean screenshot http://google.com` in the command line. Have a look in this file to check the process of getting the URL and number of years. The `getSnapshotUrl()` method in this file connects to the [Wayback Machine API](https://archive.org/help/wayback_api.php) to find the right URL for the past copy of the website.

The `src/ScreenshotGenerator.php` file contains the code that takes a screenshot. Because this is contained within it's own PHP class / file, we can re-use this in future by just including it from wherever it's needed. See [Wikipedia - DRY](https://en.wikipedia.org/wiki/Don%27t_repeat_yourself).

Finally, this ap makes use of a number of Open-source projects. These are listed below:

 - [**Symfony Console**](http://symfony.com/doc/current/components/console) - Used to register a command-line app and get input from the user.
 - [**Symfony Process**](http://symfony.com/doc/current/components/process.html) - Component used to run other command line processes from our code.
 - [**PhantomJs**](http://phantomjs.org/screen-capture.html) - A 'headless' browser that we use to load up webpages and take screenshots in our code.
 - [**Guzzle**](http://guzzlephp.org/) - A great HTTP client used to 'talk' to the Wayback Machine API.
 - [**Carbon**](http://carbon.nesbot.com/) - A simple PHP DateTime helper that makes working with timestamps super easy.

## TODO

 - [] Update to check if URL exists before taking past/present screenshots
 - [] Add command option to only generate past screenshot (rather than present and past)
 - [] New command to take multiple past screenshots at the one time

## Why Delorean?

In the feature film series *Back to the Future*, Dr. Emmett Brown builds a time machine based on an automobile; a [*DeLorean*](https://en.wikipedia.org/wiki/DeLorean_time_machine) DMC-12.

## Futher Reading.

- Open-source packages listed above
- [How to Build Command-Line Apps](https://laracasts.com/series/how-to-build-command-line-apps-in-php) video series on Laracasts.com

## Credits / License

Made by David Rushton, Developer at [Papertank Limited](http://papertank.co.uk)
[Using the MIT License](http://github.com/CoderDojoScotland/delorean/blob/master/LICENSE.txt)