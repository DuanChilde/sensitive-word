<?php
/**
 * Created by IntelliJ IDEA.
 * User: duanwei
 * Date: 2018/5/9
 * Time: 上午11:42
 */


require_once __DIR__ . '/../vendor/autoload.php';
use SensitiveService\sensitiveWord;

$s = new sensitiveWord();

var_dump($s->validate(123));