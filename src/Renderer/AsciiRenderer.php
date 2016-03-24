<?php

namespace Tty\Renderer;

use Tty\Terminal;

class AsciiRenderer
{
    private $escape;
    
    public function __construct()
    {
        $this->escape = chr(0x1b);
    }
    
    public function render(Terminal $terminal)
    {
        $o = '';
        $o .= '=== X:' . $terminal->getX() . ' Y:' . $terminal->getY() . " ===\n";
        for ($y=0; $y<$terminal->getHeight(); $y++) {
            for ($x=0; $x<$terminal->getWidth(); $x++) {
                $color = $terminal->getForegroundColor($x, $y);
                $o .= $this->escape . "[3" . $color . "m";
                
                $color = $terminal->getBackgroundColor($x, $y);
                $o .= $this->escape . "[4" . $color . "m";
                
                $o .= $terminal->getCharacter($x, $y);
                $o .= $this->escape . "[0m";
            }
            $o .= "\n";
        }
        return $o;
    }
}
