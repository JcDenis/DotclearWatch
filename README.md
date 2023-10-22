# README

[![Release](https://img.shields.io/badge/release-0.9.3-a2cbe9.svg)](https://git.dotclear.watch/dw/DotclearWatch/releases)
![Date](https://img.shields.io/badge/date-2023.10.22-c44d58.svg)
[![Dotclear](https://img.shields.io/badge/dotclear-v2.28-137bbb.svg)](https://fr.dotclear.org/download)
[![Dotaddict](https://img.shields.io/badge/dotaddict-official-9ac123.svg)](https://plugins.dotaddict.org/dc2/details/DotclearWatch)
[![License](https://img.shields.io/badge/license-GPL--2.0-ececec.svg)](https://git.dotclear.watch/dw/DotclearWatch/src/branch/master/LICENSE)

## ABOUT

_dotclear watch_  is a plugin for the open-source web publishing software called [Dotclear](https://www.dotclear.org).

> Send report about your Dotclear

It tracks Dotclear's installation to get stats about it.
Default statistics server is located at https://stat.dotclear.watch

What's being track :
* Dotclear version,
* PHP version,
* Database version,
* Current blog theme,
* number of blogs on multiblogs
* List of installed modules (only id and version)
* Number of blogs. (no name or id or whatever else)

If you want to hide some modules, just enter their IDs in a comma separeted list 
in aboutConfig global parameters called DotclearWatch->hidden_modules

## REQUIREMENTS

* Dotclear 2.28
* PHP 8.1+
* Dotclear super admin permission to intall it

## USAGE

Install DotclearWatch, manualy from a zip package or from 
Dotaddict repository. (See Dotclear's documentation to know how do this)

To disable sending stats, just deactivate or uninstall this plugin.

## LINKS

* [License](https://git.dotclear.watch/dw/DotclearWatch/src/branch/master/LICENSE)
* [Packages & details](https://git.dotclear.watch/dw/DotclearWatch/releases) (or on [Dotaddict](https://plugins.dotaddict.org/dc2/details/DotclearWatch))
* [Sources & contributions](https://git.dotclear.watch/dw/DotclearWatch) (or on [GitHub](https://github.com/JcDenis/DotclearWatch))
* [Issues & security](https://git.dotclear.watch/dw/DotclearWatch/issues) (or on [GitHub](https://github.com/JcDenis/DotclearWatch/issues))

## CONTRIBUTORS

* Jean-Christian Denis (author)

You are welcome to contribute to this code.
