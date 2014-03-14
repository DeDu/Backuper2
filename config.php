<?php


return [
    // Where to store the backup:
    'storages' => [
        'bitcasa'   =>  [
            // Check https://developer.bitcasa.com/docs
            'driver'            => 'BitcasaStore',
            'access_token'      =>  'YOUR_ACCESS_TOKEN',
            'base_backup_path'  => 'BITCASA-PATH', // The Path where the Backup will be generated
            'dir_prefix'        => 'Backup_',
        ],
        'local'     =>  [
            'driver'            => 'LocalStore',
            'dir_prefix'        => 'Backup_',
            'localPath'         => '/path/to/local/dir',
        ]
    ],
    // What to store in the backup:
    'backup'        =>  [
        // Databases to store:
        'databases'     =>  [
            'driver'    =>  'DatabaseBackup',
            'mysql'     =>  [
                'user'      => 'MYSQL-USERNAME',
                'password'  => 'MYSQL-PASSWORD',

                'databases' => [
                    'backuptest', // Replace with the databases you want to backup
                    'blog',
                ]
            ]
        ],
        // Files to backup
        'data'     => [
            'driver'    => 'DataBackup',
            '/tmp/', // Replace with the folders and files you want to backup
            '/tmp/tmp',
        ]
    ],
    // Encrypt the backup:
    'encrypt'   => [
        'driver'        => 'PublicKeyEncrypter',
        'public_key'    => '/path/to/key.cert',
    ],
    // Cache directory:
    'cache_dir' => getcwd() . '/cache',
    // Log directory
    'log_dir'   => getcwd() . '/log'
];