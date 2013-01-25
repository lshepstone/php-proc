PhpProc
=======

Basic wrapper for the PHP proc_* functions (blocking, single-thread, limited Windows support).

[![Build Status](https://travis-ci.org/lshepstone/php-proc.png?branch=master)](https://travis-ci.org/lshepstone/php-proc)

```php
use \PhpProc\Process;

$process = new Process();
$result = $process
    ->setCommand("/usr/bin/php -r \"echo getenv('USER');\"");
    ->setWorkingDirectory(__DIR__);
    ->setEnvironmentVars(array(
        'PATH' => getenv('PATH'),
        'SHELL' => getenv('SHELL'),
        'USER' => 'developer'))
    ->execute();

echo 'Status: ' . $result->getStatus() . PHP_EOL;
if ($result->hasErrors()) {
    echo 'Errors: ' . $result->getStdErrContents();
} else {
    echo 'Output: ' . $result->getStdOutContents();
}
```

```
Status: 0
Output: developer
```
