# Babelfish

Babelfish is a file programming language detector based on [github/linguist](https://github.com/github/linguist) and
[src-d/enry](https://github.com/src-d/enry).

# Examples

```php
$source_file = new ContentFile('Babelfish.php', \file_get_contents(__DIR__ . '/src/Babelfish/Babelfish.php'));
$language = Babelfish::getWithDefaultStrategies()->getLanguage($source_file);
if ($language !== null) {
    echo 'Language detected: ' . $language->getName();
}
```