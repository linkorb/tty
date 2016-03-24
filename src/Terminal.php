<?php

namespace Tty;

use RuntimeException;

class Terminal
{
    private $mode = 'character';
    private $commandBuffer;
    private $width = 120;
    private $height = 25;
    private $x = 0;
    private $y = 0;
    private $character = [];
    private $foregroundColor = [];
    private $backgroundColor = [];
    private $cursorForegroundColor = 7;
    private $cursorBackgroundColor = 0;
    
    public function __construct()
    {
        $this->clearScreen();
    }

    public function write($string)
    {
        $i = 0;
        while ($i<strlen($string)) {
            $this->writeCharacter($string[$i]);
            $i++;
        }
    }
    
    public function writeCharacter($char)
    {
        switch ($this->mode) {
            case 'character':
                switch ($char) {
                    case chr(27):
                        $this->mode = 'escape';
                        break;
                    case chr(13):
                        $this->handleCarriageReturn();
                        break;
                    case chr(10): // linefeed
                        // skip
                        break;
                    case chr(9): // tab
                        $this->moveRight();
                        while (($this->x % 8) >0) {
                            $this->moveRight();
                        }
                        break;
                    case chr(8): // backspace
                        $this->x--;
                        if ($this->x < 0) {
                            $this->x = 0;
                        }
                        break;
                    default:
                        $this->character[$this->x][$this->y] = $char;
                        $this->foregroundColor[$this->x][$this->y] = $this->cursorForegroundColor;
                        $this->backgroundColor[$this->x][$this->y] = $this->cursorBackgroundColor;
                        $this->moveRight();
                        break;
                }
                return;
                break;
            case 'csi':
                $this->commandBuffer .= $char;
                if ((ord($char)>=64) && (ord($char)<=126)) {
                    echo "CSI: [" . $this->commandBuffer . "]\n";
                    $this->handleCsi($this->commandBuffer, $char);
                    $this->mode = 'character';
                }
                break;
            case 'osc':
                if (ord($char)==7) {
                    echo "OSC: [" . $this->commandBuffer . "]\n";
                    $this->mode = 'character';
                } else {
                    $this->commandBuffer .= $char;
                }
                break;
            case 'escape':
                $this->commandBuffer = '';
                switch ($char) {
                    case 'c': // Full Reset (RIS).
                        $this->clearScreen();
                        break;
                    case '[':
                        $this->mode = 'csi';
                        return;
                        break;
                    case ']':
                        $this->mode = 'osc';
                        return;
                        break;
                    case 'H':
                        $this->clearScreen();
                        $this->mode = 'character';
                        break;
                    case '>':
                        // Exit alternate keypad mode.
                        break;
                    case '-':
                    case '(':
                    case '=':
                    case chr(10):
                    case chr(35): // #
                    case chr(32):
                    case chr(13):
                        // TODO: what does this do?
                        break;
                    case chr(27):
                        // TODO: ignore double escape?
                        break;
                    default:
                        throw new RuntimeException("Unsupported char following escape: " . $char . '/' . ord($char));
                }
                $this->mode = 'character';
                return;
                break;
            default:
                throw new RuntimException("Unsupported mode: " . $this->mode);
        }
    }
    
    public function handleCsi($command)
    {
        $char = substr($command, -1);
        $parameters = substr($command, 0, -1);
        $numbers = explode(';', $parameters);
        //print_r($number);
        //echo "Handling CSI [$command] ($char:$parameters)\n";
        switch ($command) {
            
            case '2J':
            case '!p':
                // soft reset
                $this->clearScreen();
                return;
                break;
            case '?1049h': //  Save cursor as in DECSC and use Alternate Screen Buffer, clearing it first.
                return;
            case '?1034h':
                // Interpret "meta" key, sets eighth bit.
                return;
                break;
        }
        
        switch ($char) {
            case 'C': // Cursor Forward Ps Times (default = 1) (CUF).
                // TODO: consider parameters
                $this->moveRight();
                return;
            case 'P': // Delete Ps Character(s) (default = 1) (DCH).
                return; // TODO: implement this!!
            case 'H': // Home
                return; // TODO: implement this!!
            case 'l': // Reset Mode (RM) / DEC Private Mode Reset (DECRST).
                return; // TODO: implement this!!
            case 'g': // Tab Clear (TBC).
                return; // TODO: implement this!!
            case 'h': // DEC Private Mode Set (DECSET).
                return; // TODO: implement this!!
            case 'r': // Set scrolling region
                return; // TODO: implement this!!
            case 'K': // Erase in Line (EL).
                $number = 0;
                if (isset($numbers[0])) {
                    $number = $numbers[0];
                }
                switch ($number) {
                    default:
                        return; // TODO: implement this!!
                }
                break;
            case '@': // insert character
                return; // TODO: implement this!!
            case 'm': // Character Attributes (SGR)
                foreach ($numbers as $number) {
                    switch ($number) {
                        case 0:
                            // reset
                            // TODO: configurable defaults?
                            $this->cursorForegroundColor = 7;
                            $this->cursorBackgroundColor = 0;
                            break;
                        case 1: // bold
                        case 5: // blink
                            break;
                        case 27: // Positive (not inverse).
                        case 38: // ??
                        case 48: // ??
                        case 107: // ??
                        case 130: // ??
                        case 228: // ??
                        case 231: // ??
                        case 243: // ??
                        case 251: // ??
                            break;
                        case 30: // fg: black
                            $this->cursorForegroundColor = 0;
                            break;
                        case 31: // fg: red
                            $this->cursorForegroundColor = 1;
                            break;
                        case 32: // fg: green
                            $this->cursorForegroundColor = 2;
                            break;
                        case 33: // fg: yellow
                            $this->cursorForegroundColor = 3;
                            break;
                        case 34: // fg: blue
                            $this->cursorForegroundColor = 4;
                            break;
                        case 35: // fg: magenta
                            $this->cursorForegroundColor = 5;
                            break;
                        case 36: // fg: cyan
                            $this->cursorForegroundColor = 6;
                            break;
                        case 37: // fg: white
                            $this->cursorForegroundColor = 7;
                            break;
                        case 39: // fg: default
                            $this->cursorForegroundColor = 7;
                            break;
                        case 40: // bg: black
                            $this->cursorBackgroundColor = 0;
                            break;
                        case 41: // bg: red
                            $this->cursorBackgroundColor = 1;
                            break;
                        case 42: // bg: green
                            $this->cursorBackgroundColor = 2;
                            break;
                        case 43: // bg: yellow
                            $this->cursorBackgroundColor = 3;
                            break;
                        case 44: // bg: blue
                            $this->cursorBackgroundColor = 4;
                            break;
                        case 45: // bg: magenta
                            $this->cursorBackgroundColor = 5;
                            break;
                        case 46: // bg: cyan
                            $this->cursorBackgroundColor = 6;
                            break;
                        case 47: // bg: white
                            $this->cursorBackgroundColor = 7;
                            break;
                        case 49: // bg: default
                            $this->cursorBackgroundColor = 7;
                            break;
                        default:
                            //throw new RuntimeException("Unsupported SGR number: " . $number);
                            break;
                    }
                }
                return;
                break;
        }
        //throw new RuntimeException("Unsupported CSI: [$command]\n");
    }
    
    public function clearScreen()
    {
        for ($y=0; $y<$this->height; $y++) {
            for ($x=0; $x<$this->width; $x++) {
                $this->character[$x][$y] = null;
                $this->foregroundColor[$x][$y] = 7;
                $this->backgroundColor[$x][$y] = 0;
            }
        }
        $this->x = 0;
        $this->y = 0;
    }
    
    public function moveRight()
    {
        $this->x++;
        if ($this->x > $this->width) {
            $this->x=0;
            $this->y++;
            if ($this->y>$this->height) {
                $this->scrollUp();
            }
        }
    }
    public function handleCarriageReturn()
    {
        $this->x=0;
        $this->y++;
        if ($this->y>=$this->height) {
            $this->scrollUp();
        }
    }
    
    public function scrollUp()
    {
        for ($y=0; $y<$this->height-1; $y++) {
            for ($x=0; $x<$this->width; $x++) {
                $this->character[$x][$y] = $this->character[$x][$y+1];
                $this->foregroundColor[$x][$y] = $this->foregroundColor[$x][$y+1];
                $this->backgroundColor[$x][$y] = $this->backgroundColor[$x][$y+1];
            }
        }
        for ($x=0; $x<$this->width; $x++) {
            $this->character[$x][$this->height-1] = null;
        }
        $this->y--;
    }
    
    public function getX()
    {
        return $this->x;
    }
    public function getY()
    {
        return $this->y;
    }
    public function getWidth()
    {
        return $this->width;
    }
    
    public function setWidth($width)
    {
        $this->width = $width;
        return $this;
    }
    
    public function getHeight()
    {
        return $this->height;
    }
    
    public function setHeight($height)
    {
        $this->height = $height;
        return $this;
    }
    
    public function getCharacter($x, $y)
    {
        if (!isset($this->character[$x][$y])) {
            return ' ';
        }
        $char = $this->character[$x][$y];
        return $char;
    }
    public function getForegroundColor($x, $y)
    {
        $color = $this->foregroundColor[$x][$y];
        return $color;
    }
    
    public function getBackgroundColor($x, $y)
    {
        $color = $this->backgroundColor[$x][$y];
        return $color;
    }
}
