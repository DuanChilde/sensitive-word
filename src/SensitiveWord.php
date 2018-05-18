<?php
/**
 * Created by IntelliJ IDEA.
 * User: duanwei
 * Date: 2018/5/9
 * Time: 上午11:28
 */
namespace SensitiveService;

use PhpOffice\PhpSpreadsheet\IOFactory;
use Predis\Client;

class SensitiveWord
{
    private static $instance;
    private $redis;

    protected $warningLevel;    //警告等级
    protected $matches;    //匹配到的词
    protected $wordLib = [];    //全部敏感词文件
    protected $loadedLib = [];  //已经加载的敏感词文件
    protected $words = [];  //所有的词

    const LEVEL_ONE = 1;    //一级敏感词
    const LEVEL_TWO = 2;    //二级敏感词
    const LEVEL_THREE = 3;  //三级敏感词

    const REDIS_PREFIX = "sensitive_word:";
    const LOADED_LIB = "loaded_lib";



    private function __construct($config)
    {
        ini_set('memory_limit','-1');
        if(!isset($config['hostname']) || !isset($config['port']) || !isset($config['database'])){
            throw new \Exception("请加载redis配置");
        }
        $this->redis = new Client([
            'host' => $config['hostname'],
            'port' => $config['port'],
            'database' => $config['database'],
        ]);
    }

    private function __clone()
    {
    }

    public static function getInstance($config){
        if (!self::$instance instanceof self) {
            self::$instance = new self($config);
        }
        return self::$instance;
     }

    /**
     * 校验是否包含敏感词
     * @param $content
     * @param null $level
     * @return bool
     */
    public function validate($content, $level = null)
    {
        if ($level && !$this->matchByLevel($content,$level)) {
            return false;
        } elseif(!$level) {
            foreach($this->words as $k=>$v)
            {
                if(!empty($v) && !$this->matchByLevel($content,$k)){
                    return false;
                }
            }
        }
        return true;
    }


    /**
     * 替换敏感词
     * @param $content
     * @param string $replace
     * @param null $level
     * @return string
     */
    public function replace($content, $replace="*", $level = null)
    {
        if ($level && $content = $this->replaceByLevel($content,$replace,$level)) {
            return $content;
        } else {
            foreach($this->words as $k=>$v)
            {
                if(!empty($v)){
                    $content = $this->replaceByLevel($content,$replace,$k);
                }
            }
        }
        return $content;
    }

    protected function matchByLevel($content, $level)
    {
        if(isset($this->words[$level]) && !empty($this->words[$level]))
        {
            $badWord = array_combine($this->words[$level],array_fill(0,count($this->words[$level]),'~!@#$^'));
            $str = strtr($content, $badWord);
            if(strstr($str,"~!@#$^") !== false){
                $this->warningLevel = $level;
                return false;
            }
        }
        return true;
    }

    protected function replaceByLevel($content, $replace, $level)
    {
        if(isset($this->words[$level]) && !empty($this->words[$level]))
        {
            $badWord = array_combine($this->words[$level],array_fill(0,count($this->words[$level]),$replace));
            $content = strtr($content, $badWord);
        }
        return $content;
    }

    /**
     * 获取警告等级
     * @return mixed
     */
    public function getWarningLevel()
    {
        return $this->warningLevel;
    }

    /**
     * 加载词库
     * @param array $filePath
     * @throws \PhpOffice\PhpSpreadsheet\Exception
     */
    public function loadWordLib($filePath=[])
    {
        $filePath = is_string($filePath) ? [$filePath] : [];
        if(!empty($filePath))   //加载自定义敏感词库
        {
            foreach($filePath as $file)
            {
                if(!file_exists($file)){
                    continue;
                }
                if(!$this->redis->sismember(self::REDIS_PREFIX.self::LOADED_LIB,$file))
                {
                    $this->redis->sadd(self::REDIS_PREFIX.self::LOADED_LIB,$file);
                    $this->wordLib[]= $file;
                }
            }
        }else{  //加载本地敏感词库
            $this->getLocalLib();
        }
        $diffFile = array_diff(array_unique($this->wordLib),$this->getLoadedLib());

        if($diffFile)
        {
            foreach($diffFile as $file)
            {
                $spreadsheet = IOFactory::load($file);
                $count = $spreadsheet->getSheetCount();
                for($i=0;$i<$count;$i++)
                {
                    $sheetData = $spreadsheet->getSheet($i)->toArray(null, true, true, true);
                    array_shift($sheetData);

                    $three = array_filter(array_map(function($v) {if(strstr($v['B'],"三") !== false || strstr($v['B'],"3") !== false){return $v['A'];}},$sheetData));
                    if($three){
                        $this->redis->sadd(self::REDIS_PREFIX."sensitive_".self::LEVEL_THREE,$three);
                        $this->words[self::LEVEL_THREE] = array_filter(array_unique(array_map(function($v) {if(strstr($v['B'],"三") !== false || strstr($v['B'],"3") !== false){return $v['A'];}},$sheetData)));
                    }

                    $two = array_filter(array_map(function($v) {if(strstr($v['B'],"二") !== false || strstr($v['B'],"2") !== false){return $v['A'];}},$sheetData));
                    if($two){
                        $this->redis->sadd(self::REDIS_PREFIX."sensitive_".self::LEVEL_TWO,$two);
                        $this->words[self::LEVEL_TWO] = array_filter(array_unique(array_map(function($v) {if(strstr($v['B'],"二") !== false || strstr($v['B'],"2") !== false){return $v['A'];}},$sheetData)));
                    }

                    $one = array_filter(array_map(function($v) {if(strstr($v['B'],"一") !== false || strstr($v['B'],"1") !== false){return $v['A'];}},$sheetData));
                    if($one){
                        $this->redis->sadd(self::REDIS_PREFIX."sensitive_".self::LEVEL_ONE,$one);
                        $this->words[self::LEVEL_ONE] = array_filter(array_unique(array_map(function($v) {if(strstr($v['B'],"一") !== false || strstr($v['B'],"1") !== false){return $v['A'];}},$sheetData)));
                    }
                }
                $this->redis->sadd(self::REDIS_PREFIX.self::LOADED_LIB,$file);
                $this->loadedLib[] = $file;
            }
        }else{
            $this->words[self::LEVEL_THREE] = $this->redis->smembers(self::REDIS_PREFIX."sensitive_".self::LEVEL_THREE);
            $this->words[self::LEVEL_TWO] = $this->redis->smembers(self::REDIS_PREFIX."sensitive_".self::LEVEL_TWO);
            $this->words[self::LEVEL_ONE] = $this->redis->smembers(self::REDIS_PREFIX."sensitive_".self::LEVEL_ONE);
        }

    }

    protected function getLoadedLib()
    {
        if($this->redis->exists(self::REDIS_PREFIX.self::LOADED_LIB)){
            return $this->redis->smembers(self::REDIS_PREFIX.self::LOADED_LIB);
        }else{
            return $this->loadedLib;
        }

    }

    protected function getLocalLib()
    {
        $path = __DIR__."/../lib";
        if (false != ($handle = opendir ($path))) {
            while ( false !== ($file = readdir ( $handle )) ) {
                if ($file != "." && $file != ".." && strpos($file,".") && strpos($file,"~") !== 0) {
                    if(!$this->redis->sismember(self::REDIS_PREFIX.self::LOADED_LIB,$path."/".$file))
                    {
                        $this->wordLib[] = $path."/".$file;
                    }
                }
            }

        }

    }

    /**
     * 清空词库
     * @return int
     */
    public function clear()
    {
        if($this->redis->keys(self::REDIS_PREFIX."*")){
            $this->redis->del($this->redis->keys(self::REDIS_PREFIX."*"));
        }
        return true;
    }

}