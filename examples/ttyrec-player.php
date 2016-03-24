<?php

require_once __DIR__ . '/../vendor/autoload.php';

$filename = "tty.log";

$handle = fopen($filename, "rb");
$data = stream_get_contents($handle);
fclose($handle);

$p = new \Tty\TtyRec\Parser($data);
$t = new \Tty\Terminal();
$r = new \Tty\Renderer\AsciiRenderer();

$i = 0;
$max = 100024;
$debug = false;
if (!$debug) {
    echo chr(27) . '[2J';
    echo chr(27) . '[;H';
}

$startStamp = null;
while ($frame = $p->getFrame()) {
    if (!$startStamp) {
        $startStamp = $frame->getStamp();
    }
    if ($i<$max) {
        if ($debug) {
            echo "Frame $i: [" . date('d/M/Y H:i:s', $frame->getStamp()) . "] len: " . $frame->getLength() . "\n";
            echo $frame->toAscii() . "\n";
        }
        
        $t->write($frame->getPayload());
        if (!$debug) {
            echo chr(27) . '[;H';
        }
        echo $r->render($t);
        //echo "-------\n";
        usleep(100000 * 3);
    }
    $i++;
}
exit("Done\n");
