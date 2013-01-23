PhpProc
=======

Basic wrapper for the PHP proc_* functions (blocking, single-thread only).

Usage
-----

```php
use \PhpProc\Process;

$process = new Process();

$process->setCommand("/usr/bin/php -r \"echo 'Hello, world!';\"");
$process->setWorkingDirectory(__DIR__);
$process->setEnvironmentVars(array(
    'USER' => 'developer'
));

$result = $process->execute();

echo 'Result Status: ' . $result->getStatus();
if ($result->hasErrors()) {
    'Errors: ' . $result->getStdErrContents();
} else {
    'Output: ' . $result->getStdOutContents();
}
```
