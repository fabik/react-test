<?php

namespace Fabik\ReactTest;

use React;

require __DIR__ . '/vendor/autoload.php';

class ProcessTest
{
	private $eventLoop;


	public function run()
	{
		$this->eventLoop = React\EventLoop\Factory::create();
		for ($i = 1; $i <= 10; $i++) {
			$this->createProcess($i);
		}
		$this->eventLoop->run();
	}


	private function createProcess($n)
	{
		$process = new React\ChildProcess\Process('php -f ' . escapeshellarg(__DIR__ . '/worker.php'));

		$process->start($this->eventLoop);

		$process->stdout->on('data', function($output) use ($n) {
			if ($output) {
				$this->log('Received data from stdout of a process.'
					. ' Process number: ' . json_encode($n)
					. ' Data: ' . json_encode($output));
			}
		});

		$process->stderr->on('data', function($output) use ($n) {
			if ($output) {
				$this->log('Received data from stderr of a process.'
					. ' Process number: ' . json_encode($n)
					. ' Data: ' . json_encode($output));
			}
		});

		$process->on('exit', function($exitCode) {
			$this->log('Process has exitted.'
				. ' Process number: ' . json_encode($n)
				. ' Exit code: ' . json_encode($exitCode));
		});
	}


	private function log($message)
	{
		echo '[' . date('Y-m-d H:i:s') . '] ' . $message . "\n";
	}
}

$test = new ProcessTest();
$test->run();
