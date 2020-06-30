# Aeon

[![Minimum PHP Version](https://img.shields.io/badge/php-%3E%3D%207.4-8892BF.svg)](https://php.net/)
[![License](https://poser.pugx.org/aeon-php/cli/license)](//packagist.org/packages/aeon-php/cli)
![Tests](https://github.com/aeon-php/cli/workflows/Tests/badge.svg?branch=1.x)

Time Management Framework for PHP

> The word aeon /ˈiːɒn/, also spelled eon (in American English), originally meant "life", "vital force" or "being", 
> "generation" or "a period of time", though it tended to be translated as "age" in the sense of "ages", "forever", 
> "timeless" or "for eternity".

[Source: Wikipedia](https://en.wikipedia.org/wiki/Aeon) 

Aeon is a set of libraries that makes easier to work with PHP Date & Time in elegant Object Oriented way.

Please read [Official Documentation](https://aeon-php.org/docs).

# CLI

Aeon CLI application brings few simple functions that helps to work with time related issues. 

* `bin/aeon calendar:ntp:time --compare` - compare system timestamp with NTP server
* `bin/aeon calendar:timezonedb:version` - compare timezonedb used by PHP with the one available at IANA website
