TTY library
===========

This library contains functionality for working with TTY/PTYs and related formats and protocols.

## Included:

* A ttyrec parser (extracts frames with payload and timing from ttyrec files)
* A VT100/ANSI Terminal Emulator. Parses escape codes, CSI, OSI etc commands
* An AsciiRenderer, to render Terminal buffers for replays and debugging
* Examples in [examples/](examples/)

## How to use

    composer install
    ttyrec tty.log
    # execute some commands, like ls, df, etc
    exit
    php examples/ttyrec-player.php
    
## Notes

When starting this project I had no idea what I was getting in to. VT100 is an insane protocol.
I'm keeping notes on things I'm learning along the way in [NOTES.md](NOTES.md)


## TODO / Next steps:

* [ ] Extract executed commands from tty sessions (initial goal of this project)
* [ ] Solid test-cases based on pre-recorded tty sessions
* [ ] Support more escape codes for coloring, scrolling and other more advanced use-cases

## License

MIT. Please refer to the [license file](LICENSE.md) for details.

## Brought to you by the LinkORB Engineering team

<img src="http://www.linkorb.com/d/meta/tier1/images/linkorbengineering-logo.png" width="200px" /><br />
Check out our other projects at [linkorb.com/engineering](http://www.linkorb.com/engineering).

Btw, we're hiring!
