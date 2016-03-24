<?php

require_once __DIR__ . '/../vendor/autoload.php';


$filename = __DIR__ . '/../tty.log';

$handle = fopen($filename, "rb");
$data = stream_get_contents($handle);
fclose($handle);

$p = new \Tty\TtyRec\Parser($data);

$i = 0;
while ($frame = $p->getFrame()) {
    echo "Frame $i: [" . date('d/M/Y H:i:s', $frame->getStamp()) . "] len: " . $frame->getLength() . "\n";
    echo $frame->toAscii() . "\n";
    usleep(500);
    $i++;
}
exit("Done\n");
