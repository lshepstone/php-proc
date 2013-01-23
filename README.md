PhpProc
=======

Basic wrapper for the PHP proc_* functions (blocking, single-thread only).

[![Build Status](https://travis-ci.org/lshepstone/php-proc.png?branch=master)](https://travis-ci.org/lshepstone/php-proc)

Using

```php
use \PhpProc\Process;

$process = new Process();
$result = $process
    ->setCommand("/usr/bin/php -r \"echo getenv('USER');\"");
    ->setWorkingDirectory(__DIR__);
    ->setEnvironmentVars(array('USER' => 'developer'))
    ->execute();

echo 'Status: ' . $result->getStatus() . PHP_EOL;
if ($result->hasErrors()) {
    echo 'Errors: ' . $result->getStdErrContents();
} else {
    echo 'Output: ' . $result->getStdOutContents();
}
```

produces

```
Status: 0
Output: developer
```