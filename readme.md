# Backuper2
Backuper2 is a small PHP-Programm which makes backups and stores them where ever you want.

It's currently under development and should not be used.

It is easy to configure and to extend.

## Backup
Backuper2 can make Backups from:
- the Filesystem (directories, files)
- Databases (only MySQL at the moment)

## Storages
Following storages are supported:
- Bitcasa
- Local

## Security
Backuper2 can encrypt your backup with a public key before it transport it to the storages.

It does that wit OpenSSL/Mcrypt and needs the public key in the pem-format.

You can decrypt your backup with the `decrypt` command of Backuper2.

## Install
First downlaod Backuper2 from here: https://github.com/DeDu/Backuper2/raw/master/bin/backuper2.phar

To install Backuper2 globally on a linux machine use this command:

	mv backuper2.phar /usr/local/bin/backuper2

Then make sure that Backuper2 is executable.

Get a copy of the file `config.php` from https://github.com/DeDu/Backuper2/raw/master/config.php and configure it to fit your needs:

Enter all databases you want to backup under `backup > databases > mysql > databases`.

Then enter all directories and folders under `backup > data`. Do not delete the `driver` option! All folders and files
will get archived and compressed.

If you want to encrypt your backup, fill in the absolute path to you public key at `encrypt > public_key`. If you
don't want to encrypt it, delete the encrypt-section from your `config.php`.

Then head to the storages-section to configure where you want to save your backup.

For Bitcasa you will need to register your app at https://developer.bitcasa.com/admin/applications. Then you have
to generate a long-life access-token manually and enter it under 'storages > bitcasa > access_token'. After that
specify the path where you want to store your backup. Read the documentation from Bitcasa because the folder names
are a bit special. Like this, for example:

    /FOPqySw3ToK_25y-gagUfg/Nf6MVO4mRjiqpqz9RiEm2A

## Commands
To get a full list of commands run `backuper2 --help` if you have Backuper2 installed globally. Otherwise use `backuper2.phar --help`.

To make the backup:

	backuper2 -c /path/to/config.php backup

To decrypt a backup made with Backuper2:

	backuper2 -c /path/to/config.php -k /path/to/private_key.key decrypt /path/to/backup/directory

You will find your decrypted files in the same directory as the enrypted ones.
