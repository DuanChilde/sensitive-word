<?php
/**
 * Created by IntelliJ IDEA.
 * User: duanwei
 * Date: 2018/5/9
 * Time: 上午11:42
 */


require_once __DIR__ . '/../vendor/autoload.php';
use SensitiveService\SensitiveWord;

$s = SensitiveWord::getInstance(['hostname'=>'127.0.0.1','port'=>'6379','database'=>'0']);
$s->loadWordLib();  //加载词库
//$s->loadWordLib("/Users/a20170407/Workspace/sensitive-word/lib/three.xlsx");  //加载指定词库文件
//$s->loadWordLib(__DIR__."/../lib/three.xlsx");  //加载指定词库文件
//var_dump($s->validate("法轮功,月经痛",SensitiveWord::LEVEL_THREE));
//var_dump($s->validate("法轮功,月经痛",SensitiveWord::LEVEL_TWO));
var_dump($s->validate("法轮功,123"));
var_dump($s->getWarningLevel());

//var_dump($s->replace("法轮功,123法轮,月经痛"));
//$s->clear();




