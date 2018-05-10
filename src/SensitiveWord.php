<?php
/**
 * Created by IntelliJ IDEA.
 * User: duanwei
 * Date: 2018/5/9
 * Time: 上午11:28
 */
namespace SensitiveService;
use PhpOffice\PhpSpreadsheet\IOFactory;


class SensitiveWord
{
    private static $instance;
    protected $warningLevel;    //警告等级
    protected $matches;    //匹配到的词
    protected $wordLib = [];    //全部敏感词文件
    protected $loadedLib = [];  //已经加载的敏感词文件
    protected $words = [];  //所有的词
    protected $isLoaded = false;

    const LEVEL_ONE = 1;    //一级敏感词
    const LEVEL_TWO = 2;    //二级敏感词
    const LEVEL_THREE = 3;  //三级敏感词

    private function __construct()
    {
    }

    private function __clone()
    {
    }

    public static function getInstance(){
        if (!self::$instance instanceof self) {
            self::$instance = new self();
        }
        return self::$instance;
     }


    public function validate($content, $level = null)
    {
        if(!$this->isLoaded){
            throw new \Exception("没有加载词库");
        }
        if ($level && !$this->matchByLevel($content,$level)) {
            return false;
        } else {
            foreach($this->words as $k=>$v)
            {
                if(!empty($v) && !$this->matchByLevel($content,$k)){
                    return false;
                }
            }
        }
        return true;
    }

    public function matchByLevel($content, $level)
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

    public function getWarningLevel()
    {
        return $this->warningLevel;
    }

    public function loadWordLib($filePath=[],$rules=[])
    {
        $filePath = is_string($filePath) ? [$filePath=>$filePath] : [];
        if(!empty($filePath))   //加载自定义敏感词库
        {
            $filePath = array_filter(array_map(function($v){if(file_exists($v)){return $v;}},$filePath));
            $this->wordLib = array_merge($this->wordLib,$filePath);
        }else{  //加载本地敏感词库
            $this->getLocalLib();
        }

        $diffFile = array_diff(array_unique($this->getLoadedLib()),$this->loadedLib);
        $this->isLoaded = count($diffFile)>0 ? true : false;

        ini_set('memory_limit','128M');
        require_once __DIR__.'/../vendor/autoload.php';
        foreach($diffFile as $file)
        {
            $spreadsheet = IOFactory::load($this->wordLib[$file]);
            $count = $spreadsheet->getSheetCount();
            for($i=0;$i<$count;$i++)
            {
                $sheetData = $spreadsheet->getSheet($i)->toArray(null, true, true, true);
                array_shift($sheetData);
                if(empty($rules)){
                    $this->words[self::LEVEL_THREE] = array_filter(array_unique(array_map(function($v) {if(strstr($v['B'],"三") !== false || strstr($v['B'],"3") !== false){return $v['A'];}},$sheetData)));
                    $this->words[self::LEVEL_TWO] = array_filter(array_unique(array_map(function($v) {if(strstr($v['B'],"二") !== false || strstr($v['B'],"2") !== false){return $v['A'];}},$sheetData)));
                    $this->words[self::LEVEL_ONE] = array_filter(array_unique(array_map(function($v) {if(strstr($v['B'],"一") !== false || strstr($v['B'],"1") !== false){return $v['A'];}},$sheetData)));
                }else{

                }
            }
            $this->loadedLib[] = $file;
        }
    }

    public function getLoadedLib()
    {
        return $this->wordLib ? array_keys($this->wordLib) : [];
    }

    protected function getLocalLib()
    {
        $path = __DIR__."/../lib";
        if (false != ($handle = opendir ($path))) {
            while ( false !== ($file = readdir ( $handle )) ) {
                if ($file != "." && $file != ".." && strpos($file,".") && strpos($file,"~") !== 0) {
                    $this->wordLib[$file]= $path."/".$file;
                }
            }
        }
    }



}