[![Latest Stable Version](https://poser.pugx.org/huicui/huicuiapi/v/stable)](https://packagist.org/packages/huicui/huicuiapi)
[![Total Downloads](https://poser.pugx.org/huicui/huicuiapi/downloads)](https://packagist.org/packages/huicui/huicuiapi)
[![Latest Unstable Version](https://poser.pugx.org/huicui/huicuiapi/v/unstable)](https://packagist.org/packages/huicui/huicuiapi)
[![License](https://poser.pugx.org/huicui/huicuiapi/license)](https://packagist.org/packages/huicui/huicuiapi)


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

> 具体使用请参考 doc 目录中的文档