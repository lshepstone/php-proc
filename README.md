PhpProc
=======

Basic wrapper for the PHP proc_* functions (blocking, single-thread only).

```php
use \PhpProc\Process;

$process = new Process();
$process
    ->setCommand("/usr/bin/php -r \"echo getenv('USER');\"");
    ->setWorkingDirectory(__DIR__);
    ->setEnvironmentVars(array(
        'USER' => 'developer'
    ));

$result = $process->execute();

echo 'Status: ' . $result->getStatus() . PHP_EOL;
if ($result->hasErrors()) {
    echo 'Errors: ' . $result->getStdErrContents();
} else {
    echo 'Output: ' . $result->getStdOutContents();
}
```

Results in:

```
Status: 0
Output: developer
```