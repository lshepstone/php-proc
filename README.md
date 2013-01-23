PhpProc
=======

Basic wrapper for the PHP proc_* functions (blocking, single-thread only).

```php
use \PhpProc\Process;

$process = new Process();

$process->setCommand("/usr/bin/php -r \"echo getenv('USER');\"");
$process->setWorkingDirectory(__DIR__);
$process->setEnvironmentVars(array(
    'USER' => 'developer'
));

$result = $process->execute();

echo 'Status: ' . $result->getStatus();
if ($result->hasErrors()) {
    'Errors: ' . $result->getStdErrContents();
} else {
    'Output: ' . $result->getStdOutContents();
}
```

Results in:

```
Status: 0
Output: developer
```