<?php

include "TrieTree.php";

$str = file_get_contents('word_list.txt');//将整个文件内容读入到一个字符串中
$str = str_replace("\n", " ", $str);
$str = str_replace("\r", "", $str);
$word = explode(" ", $str);//转换成数组
$data = array_filter($word);


$segmenter = new TrieTree();

foreach($data as $v){
    $segmenter->insert($v);
}


$segmenter->insert('北京王府井');
$segmenter->insert('北京王者荣耀');
$segmenter->insert('中国');
$segmenter->insert('中国人');

//echo "split words:" . implode(array_keys($segmenter->split('我在中国北京王府井打着北京王者荣耀,气温很高')));

$a = $segmenter->mapArray('过去');

var_dump($a);

die();

echo "<br/>";

$illegalWords = new TrieTree();
$illegalWords->insert('违禁');
$illegalWords->insert('非法');
echo "text check:" . intval($illegalWords->match('这是一个包含违禁非法的文本')) . "\n";
echo "text check:" . intval($illegalWords->match('这是一个正常文本')) . "\n";