## ttyrec

You can log a tty session with timing details using:

    ttyrec -a tty.log
    
The `-a` flag indicates appending

### Fileformat

Documented here: https://en.wikipedia.org/wiki/Ttyrec

Each chunk consists of a header using 32bit little-endian numbers:

* sec -- seconds, either since the beginning of the recording (0-based) or since the Unix epoch
* usec -- 0..999999 microseconds
* len -- length of the payload

and the actual payload written as text with vt100 control codes.
