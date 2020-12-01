File Analysis
=============

This directory contains the classes to scan the filesystem for tests.


### Find files that may contain tests

Possible files are '.php', '.md', or '.markdown' files.

```php
$testPath = __DIR__;

$fileProvider = new \Cundd\TestFlight\FileAnalysis\FileProvider();
$files = $fileProvider->findMatchingFiles($testPath);
assert(is_array($files));

usort($files, function($a, $b) {
  return strcmp($a->getName(), $b->getName());
});
assert('ClassProvider.php' === $files[0]->getName());
```


### Retrieve the classes from PHP test files

```php
$testPath = __DIR__ . '/FileProvider.php';
	
$fileProvider = new \Cundd\TestFlight\FileAnalysis\FileProvider();
$classProvider = new \Cundd\TestFlight\FileAnalysis\ClassProvider();
$classes = $classProvider->findClassesInFiles($fileProvider->findMatchingFiles($testPath));

assert(is_array($classes));
assert(__DIR__ . '/FileProvider.php' === reset($classes)->getPath());
```
