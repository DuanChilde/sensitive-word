

use SensitiveService\SensitiveWord;

$s = SensitiveWord::getInstance();
$s->loadWordLib();  //加载词库
//$s->loadWordLib("/Users/a20170407/Workspace/sensitive-word/lib/three.xlsx");  //加载指定词库文件
//$s->loadWordLib(__DIR__."/../lib/three.xlsx");  //加载指定词库文件
//var_dump($s->validate("法轮功,月经痛",SensitiveWord::LEVEL_THREE));
//var_dump($s->validate("法轮功,月经痛",SensitiveWord::LEVEL_TWO));
var_dump($s->validate("法轮功,123"));
var_dump($s->getWarningLevel());

composer require --prefer-dist "duanwei/sensitive-word"
