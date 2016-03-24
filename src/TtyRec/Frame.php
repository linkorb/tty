<?php

namespace Tty\TtyRec;

class Frame
{
    public function __construct($payload, $length, $stamp, $usec)
    {
        $this->payload = $payload;
        $this->length = $length;
        $this->stamp = $stamp;
        $this->usec = $usec;
    }
    
    public function getPayload()
    {
        return $this->payload;
    }
    
    public function getLength()
    {
        return $this->length;
    }
    
    public function getStamp()
    {
        return $this->stamp;
    }
    
    public function getUsec()
    {
        return $this->usec;
    }
    
    public function toAscii()
    {
        $o = '';
        for ($i=0; $i<$this->length; $i++) {
            $char = substr($this->payload, $i, 1);
            switch (ord($char)) {
                case 27:
                    $o .= '^';
                    break;
                case 13:
                    $o .= '^M';
                    break;
                case 10:
                    $o .= '^N';
                    break;
                case 8:
                    $o .= '^H';
                    break;
                default:
                    $o .= $char;
                    break;
            }
        }
        return $o;
    }
}
