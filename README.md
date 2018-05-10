### 1.composer.json文件配置

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
```

### 2.安装
```
composer require --prefer-dist "duanwei/sensitive-word"
```

### 3.使用
```
use SensitiveService\SensitiveWord;

$s = SensitiveWord::getInstance();
$s->loadWordLib();  //加载默认词库
//$s->loadWordLib("/Users/a20170407/Workspace/sensitive-word/lib/three.xlsx");  //加载指定词库文件
//$s->loadWordLib(__DIR__."/../lib/three.xlsx");  //加载指定词库文件
//var_dump($s->validate("法轮功,月经痛",SensitiveWord::LEVEL_THREE));
//var_dump($s->validate("法轮功,月经痛",SensitiveWord::LEVEL_TWO));
var_dump($s->validate("法轮功,123"));
var_dump($s->getWarningLevel());


```



