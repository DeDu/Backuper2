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
Backuper2 can encrypt your backup with a public key before it transform it to the storages.

It does that wit OpenSSL/Mcrypt and needs the key in the pem-format.

## Install
Open the file "config.php" and configure it to fit your needs.

Enter all databases you want to backup under 'backup > databases > mysql > databases'.

Then enter all directories and folders under 'backup > data'. Do not delete the 'driver' option! All folders and files
will get archived and compressed.

If you want to encrypt your backup, fill in the absolute path to you public key at 'encrypt > public_key'. If you
don't want to encrypt it, delete the encrypt-section from the config.php.

Then head to the storages-section to configure where you want to save your backup.

For Bitcasa you will need to register your app at https://developer.bitcasa.com/admin/applications. Then you have
to generate a long-life access-token manually and enter it under 'storages > bitcasa > access_token'. After that
specify the path where you want to store your backup. Read the documentation from Bitcasa because the foldernames
look a bit spezial. Like this, for example:

    /FOPqySw3ToK_25y-gagUfg/Nf6MVO4mRjiqpqz9RiEm2A

Then start Backuper2 with following command:

    php /path/to/start.php

## Decrypt backup
Us this command to decrypt your backup:

    php /path/to/start.php decrypt /path/to/backup/dir/ /path/to/private_key.key

You will find your decrypted files in the same directory as the enrypted ones.
