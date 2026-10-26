# CodeIgniter 4 Application Starter

## What is CodeIgniter?

CodeIgniter is a PHP full-stack web framework that is light, fast, flexible and secure.
More information can be found at the [official site](https://codeigniter.com).

This repository holds a composer-installable app starter.
It has been built from the
[development repository](https://github.com/codeigniter4/CodeIgniter4).

More information about the plans for version 4 can be found in [CodeIgniter 4](https://forum.codeigniter.com/forumdisplay.php?fid=28) on the forums.

You can read the [user guide](https://codeigniter.com/user_guide/)
corresponding to the latest version of the framework.

## Installation & updates

`composer create-project codeigniter4/appstarter` then `composer update` whenever
there is a new release of the framework.

When updating, check the release notes to see if there are any changes you might need to apply
to your `app` folder. The affected files can be copied or merged from
`vendor/codeigniter4/framework/app`.

## Setup

Copy `env` to `.env` and tailor for your app, specifically the baseURL
and any database settings.

### Production database and backups

The production database connection reads its hostname, database, username,
and password from `.env`. Configure a dedicated database account with only
the privileges the portal needs; production startup rejects an empty password
or the MySQL `root` account, and SQL debug output is disabled in production.
Never commit `.env` or a database export containing production user data or
password hashes. The checked-in SQL dump is synthetic demo data only; replace
it with schema-only SQL before adding any real database export.

Use an operations-managed, scheduled backup process for production data. Store
backups outside the web root and repository, encrypt them, restrict access, and
keep an off-host copy. One approach on Ubuntu is a root-owned MySQL client
defaults file such as `/root/.tau-portal-backup.cnf` (mode `0600`) with a
`[client]` section containing the backup account's `user`, `password`, and
`host`, then:

```sh
sudo install -d -m 0700 /var/backups/tau-portal
sudo bash -o pipefail -c 'umask 077; mysqldump --defaults-extra-file=/root/.tau-portal-backup.cnf --single-transaction tau_portal_ci4 | gzip | gpg --encrypt --recipient BACKUP_PUBLIC_KEY_ID > /var/backups/tau-portal/tau_portal_ci4-$(date +%F).sql.gz.gpg'
```

Replace `BACKUP_PUBLIC_KEY_ID` with the approved backup recipient key and
configure it for the scheduler account. Schedule the command using the
server's approved scheduler and backup retention/off-host policy. Periodically
test decryption and restoration into a separate non-production database; a
successful dump alone does not confirm recoverability.

## Important Change with index.php

`index.php` is no longer in the root of the project! It has been moved inside the *public* folder,
for better security and separation of components.

This means that you should configure your web server to "point" to your project's *public* folder, and
not to the project root. A better practice would be to configure a virtual host to point there. A poor practice would be to point your web server to the project root and expect to enter *public/...*, as the rest of your logic and the
framework are exposed.

**Please** read the user guide for a better explanation of how CI4 works!

## Repository Management

We use GitHub issues, in our main repository, to track **BUGS** and to track approved **DEVELOPMENT** work packages.
We use our [forum](http://forum.codeigniter.com) to provide SUPPORT and to discuss
FEATURE REQUESTS.

This repository is a "distribution" one, built by our release preparation script.
Problems with it can be raised on our forum, or as issues in the main repository.

## Server Requirements

PHP version 8.2 or higher is required, with the following extensions installed:

- [intl](http://php.net/manual/en/intl.requirements.php)
- [mbstring](http://php.net/manual/en/mbstring.installation.php)

> [!WARNING]
> - The end of life date for PHP 7.4 was November 28, 2022.
> - The end of life date for PHP 8.0 was November 26, 2023.
> - The end of life date for PHP 8.1 was December 31, 2025.
> - If you are still using below PHP 8.2, you should upgrade immediately.
> - The end of life date for PHP 8.2 will be December 31, 2026.

Additionally, make sure that the following extensions are enabled in your PHP:

- json (enabled by default - don't turn it off)
- [mysqlnd](http://php.net/manual/en/mysqlnd.install.php) if you plan to use MySQL
- [libcurl](http://php.net/manual/en/curl.requirements.php) if you plan to use the HTTP\CURLRequest library
