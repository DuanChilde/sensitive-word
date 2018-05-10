<?php
/**
 * Created by IntelliJ IDEA.
 * User: duanwei
 * Date: 2018/5/9
 * Time: 上午11:42
 */


require_once __DIR__ . '/../vendor/autoload.php';
use SensitiveService\SensitiveWord;

$s = SensitiveWord::getInstance();
$s->loadWordLib();  //加载词库
//$s->loadWordLib("/Users/a20170407/Workspace/sensitive-word/lib/three.xlsx");  //加载指定词库文件
//$s->loadWordLib(__DIR__."/../lib/three.xlsx");  //加载指定词库文件
//var_dump($s->validate("法轮功,月经痛",SensitiveWord::LEVEL_THREE));
//var_dump($s->validate("法轮功,月经痛",SensitiveWord::LEVEL_TWO));
var_dump($s->validate("法轮功,123"));
var_dump($s->getWarningLevel());




/*
{
    "name": "yiisoft/yii2-app-advanced",
    "description": "Yii 2 Advanced Project Template",
    "keywords": ["yii2", "framework", "advanced", "project template"],
    "homepage": "http://www.yiiframework.com/",
    "type": "project",
    "license": "BSD-3-Clause",
    "support": {
        "issues": "https://github.com/yiisoft/yii2/issues?state=open",
        "forum": "http://www.yiiframework.com/forum/",
        "wiki": "http://www.yiiframework.com/wiki/",
        "irc": "irc://irc.freenode.net/yii",
        "source": "https://github.com/yiisoft/yii2"
    },
    "minimum-stability": "stable",
    "require": {
        "php": ">=5.4.0",
        "yiisoft/yii2": "~2.0.6",
        "yiisoft/yii2-bootstrap": "~2.0.0",
        "yiisoft/yii2-swiftmailer": "~2.0.0 || ~2.1.0",
        "linslin/yii2-curl": "*",
        "predis/predis": "^1.1",
        "yiisoft/yii2-mongodb": "^2.1",
        "duanwei/sensitive-word" : "*"

    },
    "require-dev": {
        "yiisoft/yii2-debug": "~2.0.0",
        "yiisoft/yii2-gii": "~2.0.0",
        "yiisoft/yii2-faker": "~2.0.0",
        "codeception/base": "^2.2.3",
        "codeception/verify": "~0.3.1"
    },
    "config": {
        "process-timeout": 1800,
        "fxp-asset": {
            "enabled": false
        }
    },

    "repositories": [
        {
            "type": "composer",
            "url": "https://packagist.phpcomposer.com"
        },
        {
            "type": "package",
            "package": {
                "name": "duanwei/sensitive-word",
                "version": "0.0.1",
                "type": "package",
                "source": {
                    "url": "http://git.tdf.ministudy.com/duanwei/sensitive-word.git",
                    "type": "git",
                    "reference": "master"
                },
                "autoload": {
                    "psr-4": {
                        "SensitiveService\\": "src/"
                    }
                }
            }
        }
    ]
}



 */