### 1. composer.json文件配置

```
 "repositories": [
        {
            "type": "composer",
            "url": "https://packagist.phpcomposer.com"
        },
        {
            "type": "package",
            "package": {
                "name": "duanwei/sensitive-word",
                "version": "0.0.7",
                "type": "package",
                "source": {
                    "url": "http://git.tdf.ministudy.com/duanwei/sensitive-word.git",
                    "type": "git",
                    "reference": "master"
                },
                "dist": {
                    "url": "http://git.tdf.ministudy.com/duanwei/sensitive-word/archive/0.0.7.zip",
                    "type": "zip"
                },
                "autoload": {
                    "psr-4": {
                        "SensitiveService\\": "src/"
                    }
                },
                "require": {
                    "phpoffice/phpspreadsheet": "*",
                    "predis/predis": "^1.1"
                }
            }
        }
    ]
```

### 2. 安装
```
去除https
composer config secure-http false

引入
composer require --prefer-dist "duanwei/sensitive-word"

更新
composer update "duanwei/sensitive-word" 

移除
composer remove "duanwei/sensitive-word" 


```

### 3. 使用
```
use SensitiveService\SensitiveWord;
//加载redis缓存配置
$s = SensitiveWord::getInstance(['hostname'=>'127.0.0.1','port'=>'6379','database'=>'0']);
//加载默认词库
$s->loadWordLib();  
//加载指定词库文件，参数支持数组
$s->loadWordLib("/Users/xxxx/Workspace/sensitive-word/lib/three.xlsx");  
$s->loadWordLib(__DIR__."/../lib/three.xlsx");
//指定等级校验，不指定默认按三级，二级，一级的顺序校验 true|false
$s->validate("法轮功,月经痛",SensitiveWord::LEVEL_THREE);
$s->validate("法轮功,月经痛",SensitiveWord::LEVEL_TWO));
$s->validate("法轮功,123");
//获取校验等级，返回值为3，2，1，null
$s->getWarningLevel();
//清空词库，用于词库更新
$s->clear();
//替换非法字符，默认替换为*好
var_dump($s->replace("法轮功,123法轮,月经痛"));
//清空词库
$s->clear();

```



