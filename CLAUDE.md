# DotclearWatch

Notes for AI assistants working on the Dotclear plugin DotclearWatch.

**Read first** the shared conventions of the author's Dotclear modules:
https://raw.githubusercontent.com/JcDenis/gh2da/master/CONVENTIONS.md
(if a web tool refuses the address, use `curl -s` on it from the shell).
The rules below take precedence for this module.

## Module

Send report about your Dotclear It tracks Dotclear's installation to get stats about it. Default statistics server is located at https://dotclear.org/statistics.

Configuration page in the plugins manager. Admin page (super admin, System menu): compatibility audit of modules with the next Dotclear version. Nothing on the public side.

Required Dotclear and PHP versions: see `_define.php`.

## Structure

* `src/Audit.php`: compatibility audit: rules received with the report answer (`var/DotclearWatch/rules.json`), check of installed modules without distributed ones, result (`var/DotclearWatch/audit.json`), PHP version check.
* `src/Backend.php`: admin side: menu entry, dashboard item of the audit, behaviors registration.
* `src/Config.php`: configuration page of the module (plugins manager).
* `src/Install.php`: install and update, default settings.
* `src/Manage.php`: admin page: compatibility audit (send report, check modules, result and details).
* `src/My.php`: module helpers (`My::id()`, settings, permissions: admin page and menu for super admin only).
* `src/RulesScanner.php`: copy of gh2da `scripts/DotclearRulesScanner.php` with a namespace: change it in gh2da first (with its test), then copy it.
* `src/Uninstall.php`: cleaners for the plugin Uninstaller.
* `src/Utils.php`: helpers.
* `js/`: `service.js`. `css/manage.css`: levels badges (alert red, notice orange, compatible green).
* Admin help: `locales/{en,fr}/help/help.html`.
* Behaviors used: `pluginsToolsHeadersV2`, `adminDashboardHeaders`, `adminDashboardItemsV2`, `adminPageFooterV2`.

## Pitfalls

* Reports are sent through ApiServer to the official server dotclear.org (plugin DotclearWatchServer, formerly on the author's server dotclear.watch): a change of the report format must be done on both sides.
* `src/Install.php` replaces the old default URL (`Utils::OLD_API_URLS`, dotclear.watch) by `Utils::DISTANT_API_URL`; a custom URL is kept.
* Each report asks for the compatibility rules (`rules=1`); the server answers them in `content.rules` (format: gh2da `dotclear/RULES.md`), older servers ignore it.
  The check only runs on demand of a super admin (it reads all module files). Design: `CONCEPTION.md` of DotclearWatchServer.
* `core.modern` (DC_MODERN, Dotclear 2.39+) is always sent: the server uses it to know clients with the report format of DotclearWatch 1.6+, which also sends `plugins_disabled` (all installed plugins not enabled, hidden modules excepted).

## Testing

* Check the configuration page and the audit page, in English and French.
* End to end: a local DotclearWatchServer site with `rules_url` on a copy of gh2da `dotclear/rules.json`, and `distant_api_url` of this plugin on it
  (`http://127.0.0.1:<port>/default/`); a fake old plugin gives alerts.
* CI browses the common admin and public pages; extra pages can be listed in `.github/dotclear-test.txt`.
