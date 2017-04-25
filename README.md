# HuicuiApi

Assemble human API

## Huicui Api for PHP

Huicui human Api for PHP (SDK) 

## Install

### Via Composer
```
$ composer require huicui/huicuiapi
```
    
    
## Basic Usage

Have to initialize can be used

### Usage Example one.

```
<?php

require __DIR__ . '/../../autoload.php';
$AppId='';
$appKey='';
$AppSecret='';
$hcapi = new HuicuiApi\HuicuiApi($AppId, $appKey, $AppSecret);

```

Usage Example two.

```
<?php

require __DIR__ . '/../../autoload.php';

$AppId='';
$appKey='';
$AppSecret='';

$hcapi = HuicuiApi\HuicuiApi::getInstance($AppId, $appKey, $AppSecret);

```