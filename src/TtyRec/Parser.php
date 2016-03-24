<?php

namespace Tty\TtyRec;

class Parser
{
    private $pos = 0;
    private $contents;
    
    public function __construct($contents)
    {
        $this->contents = $contents;
    }
    
    public function getFrame()
    {
        if ($this->pos >= strlen($this->contents)) {
            return null;
        }
        
        $data = unpack("V", substr($this->contents, $this->pos, 4));
        $stamp = $data[1];
        $this->pos += 4;

        $data = unpack("V", substr($this->contents, $this->pos, 4));
        $usec = $data[1];
        $this->pos += 4;


        $data = unpack("V", substr($this->contents, $this->pos, 4));
        $length = $data[1];
        $this->pos +=4;
        
        $payload = substr($this->contents, $this->pos, $length);
        
        $frame = new Frame($payload, $length, $stamp, $usec);
        
        $this->pos += $length;

        return $frame;
    }
}
