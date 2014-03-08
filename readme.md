# Backuper2
Backuper2 is a small PHP-Programm which makes backups and stores them where ever you want.

It is easy to configure and to extend.

## Backup
Backuper2 can make Backups from:
- the Filesystem (directories, files)
- Databases (only MySQL at the moment)

## Storages
Following storages are supported:
- Bitcasa

## Security
Backuper2 can encrypt your backup with a public key before it transform it to the storages.

It does that wit OpenSSL and needs the key in the pem-format.

If you use encryption your backup will also be compressed.

## Install
Open the file "config.php" and configure it to fit your needs.

Start Backuper2 with following command:

 php /path/to/start.php