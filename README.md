# README

[![Release](https://img.shields.io/github/v/release/jcdenis/DotclearWatch?color=lightblue)](https://github.com/JcDenis/DotclearWatch/releases)
![Date](https://img.shields.io/github/release-date/jcdenis/DotclearWatch?color=red)
[![Dotclear](https://img.shields.io/badge/dotclear-v2.36-137bbb.svg)](https://fr.dotclear.org/download)
[![Repository](https://img.shields.io/badge/repository-official-9ac123.svg)](https://dotclear.org/plugin/detail/DotclearWatch)
[![License](https://img.shields.io/github/license/jcdenis/DotclearWatch?color=white)](https://github.com/JcDenis/DotclearWatch/blob/master/LICENSE)

## ABOUT

_dotclear watch_  is a plugin for the open-source web publishing software called [Dotclear](https://www.dotclear.org).

> Send report about your Dotclear

A [user guide in French](https://github.com/JcDenis/DotclearWatch/blob/master/docs/fr/utilisation.md) is available.

It tracks Dotclear's installation to get stats about it.
Default statistics server is located at https://dotclear.org/statistics

What's being track :
* Dotclear version,
* PHP version,
* Database version,
* Current blog theme,
* number of blogs on multiblogs
* List of installed modules (only id and version)
* Number of blogs. (no name or id or whatever else)

In return, the server sends the compatibility rules of the next Dotclear version.
From the admin page of the plugin (super admin), installed plugins and themes are checked against these rules,
on your server only: modules that will break (alerts) or use deprecated code (notices), with the file, line and replacement.
A summary is shown on the dashboard.

If you want to hide some modules, just enter their IDs in a comma separeted list 
in aboutConfig global parameters called DotclearWatch->hidden_modules

## REQUIREMENTS

* Dotclear 2.39
* PHP 8.1+
* Dotclear super admin permission to intall it

## USAGE

Install DotclearWatch, manualy from a zip package or from 
Dotclear plugins repository. (See Dotclear's documentation to know how do this)

To disable sending stats, just deactivate or uninstall this plugin.

## LINKS

* [User guide (in French)](https://github.com/JcDenis/DotclearWatch/blob/master/docs/fr/utilisation.md)
* [License](https://github.com/JcDenis/DotclearWatch/blob/master/LICENSE)
* [Packages & details](https://github.com/JcDenis/DotclearWatch/releases) (or on [Repository](https://dotclear.org/plugin/detail/DotclearWatch))
* [Sources & contributions](https://github.com/JcDenis/DotclearWatch)
* [Issues & security](https://github.com/JcDenis/DotclearWatch/issues)

## CONTRIBUTORS

* Jean-Christian Denis (author)
* Claude (AI assistant by Anthropic)

You are welcome to contribute to this code.
