File Analysis
=============

This directory contains the classes to scan the filesystem for tests and extract test definitions.

```php
$codeExtractor = new \Cundd\TestFlight\CodeExtractor();
	
$testPath = __DIR__;
	
$fileProvider = new \Cundd\TestFlight\FileAnalysis\FileProvider();
$classProvider = new \Cundd\TestFlight\FileAnalysis\ClassProvider();
$classes = $classProvider->findClassesInFiles($fileProvider->findMatchingFiles($testPath));
assert(is_array($classes));
```
