# Babelfish [![Latest Stable Version](https://poser.pugx.org/tgerbet/babelfish/v/stable)](https://packagist.org/packages/tgerbet/babelfish) [![Build Status](https://travis-ci.com/LeSuisse/babelfish.svg?branch=master)](https://travis-ci.com/LeSuisse/babelfish) [![Type Coverage](https://shepherd.dev/github/lesuisse/babelfish/coverage.svg)](https://shepherd.dev/github/lesuisse/babelfish)

Babelfish is a file programming language detector based on [github/linguist](https://github.com/github/linguist) and
[src-d/enry](https://github.com/src-d/enry).

## Examples

```php
$source_file = new ContentFile('Babelfish.php', \file_get_contents(__DIR__ . '/src/Babelfish/Babelfish.php'));
$language = Babelfish::getWithDefaultStrategies()->getLanguage($source_file);
if ($language !== null) {
    echo 'Language detected: ' . $language->getName();
}
```

## How Babelfish works?

The language is determined by using an ordered set of strategies. Each strategy will either identify the precise
language or reduce the number of likely languages for the next strategy. The default set of strategies is:
 * [Vim or Emacs modeline](src/Babelfish/Strategy/Modeline.php)
 * [commonly used filename](src/Babelfish/Strategy/Filename.php)
 * [shell shebang](src/Babelfish/Strategy/Shebang.php)
 * [file extension](src/Babelfish/Strategy/Extension.php)
 * [XML header](src/Babelfish/Strategy/XML.php)
 * [heuristics](src/Babelfish/Strategy/Heuristic.php)
 * [naïve Bayesian classification](src/Babelfish/Strategy/Classifier.php)
 
See also [How Linguist works](https://github.com/github/linguist/#how-linguist-works).