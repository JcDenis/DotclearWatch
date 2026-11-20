# DotclearWatch

Notes for AI assistants working on the Dotclear plugin DotclearWatch.

**Read first** the shared conventions of the author's Dotclear modules:
https://raw.githubusercontent.com/JcDenis/gh2da/master/CONVENTIONS.md
(if a web tool refuses the address, use `curl -s` on it from the shell).
The rules below take precedence for this module.

## Module

Send report about your Dotclear It tracks Dotclear's installation to get stats about it. Default statistics server is located at https://dotclear.org/statistics.

Configuration page in the plugins manager, no admin page. Nothing on the public side.

Required Dotclear and PHP versions: see `_define.php`.

## Structure

* `src/Backend.php`: admin side: menu entry, favorites, behaviors registration.
* `src/Config.php`: configuration page of the module (plugins manager).
* `src/Install.php`: install and update, default settings.
* `src/My.php`: module helpers (`My::id()`, settings, permissions).
* `src/Uninstall.php`: cleaners for the plugin Uninstaller.
* `src/Utils.php`: helpers.
* `js/`: `service.js`.
* Behaviors used: `pluginsToolsHeadersV2`, `adminDashboardHeaders`, `adminPageFooterV2`.

## Pitfalls

* Reports are sent through ApiServer to the official server dotclear.org (plugin DotclearWatchServer, formerly on the author's server dotclear.watch): a change of the report format must be done on both sides.
* `src/Install.php` replaces the old default URL (`Utils::OLD_API_URLS`, dotclear.watch) by `Utils::DISTANT_API_URL`; a custom URL is kept.
* `core.modern` (DC_MODERN, Dotclear 2.39+) is always sent: the server uses it to know clients with the report format of DotclearWatch 1.6+, which also sends `plugins_disabled` (all installed plugins not enabled, hidden modules excepted).

## Testing

* Check the configuration page, in English and French.
* CI browses the common admin and public pages; extra pages can be listed in `.github/dotclear-test.txt`.
