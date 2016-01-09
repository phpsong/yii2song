File Storage Extension for Yii 2
================================

This extension provides file storage abstraction layer for Yii2.

For license information check the [LICENSE](LICENSE.md)-file.

[![Latest Stable Version](https://poser.pugx.org/yii2tech/file-storage/v/stable.png)](https://packagist.org/packages/yii2tech/file-storage)
[![Total Downloads](https://poser.pugx.org/yii2tech/file-storage/downloads.png)](https://packagist.org/packages/yii2tech/file-storage)
[![Build Status](https://travis-ci.org/yii2tech/file-storage.svg?branch=master)](https://travis-ci.org/yii2tech/file-storage)


Installation
------------

The preferred way to install this extension is through [composer](http://getcomposer.org/download/).

Either run

```
php composer.phar require --prefer-dist yii2tech/file-storage
```

or add

```json
"yii2tech/file-storage": "*"
```

to the require section of your composer.json.

If you wish to use Amazon S3 storage, you should also install [aws/aws-sdk-php](https://github.com/aws/aws-sdk-php) version 2.
Either run

```
php composer.phar require --prefer-dist aws/aws-sdk-php:~2.0
```

or add

```json
"aws/aws-sdk-php": "~2.0"
```

to the require section of your composer.json.


Usage
-----

This extension provides file storage abstraction layer for Yii2.
This abstraction introduces 2 main terms: 'storage' and 'bucket'.
'Storage' - is a unit, which is able to store files using some particular approach.
'Bucket' - is a logical part of the storage, which has own specific attributes and serves some logical mean.
Each time you need to read/write a file you should do it via particular bucket, which is always belongs to the
file storage.

Example application configuration:

```php
return [
    'components' => [
        'fileStorage' => [
            'class' => 'yii2tech\filestorage\local\Storage',
            'basePath' => '@webroot/files',
            'baseUrl' => '@web/files',
            'filePermission' => 0777,
            'buckets' => [
                'tempFiles' => [
                    'baseSubPath' => 'temp',
                    'fileSubDirTemplate' => '{^name}/{^^name}',
                ],
                'imageFiles' => [
                    'baseSubPath' => 'image',
                    'fileSubDirTemplate' => '{ext}/{^name}/{^^name}',
                ],
            ]
        ],
        // ...
    ],
    // ...
];
```

Following file storages are available with this extension:
 - [[\yii2tech\filestorage\local\Storage]] - stores files on the OS local file system.
 - [[\yii2tech\filestorage\amazon\Storage]] - stores files using Amazon simple storage service (S3).
 - [[\yii2tech\filestorage\hub\Storage]] - allows combination of different file storages.

Please refer to the particualr storage class for more details.
