<?php

class TrieTreeNode {

    const ROOT_CHAR = '';

    private $char;
    private $isEnd;
    private $children = [];

    public function __construct($char) {
        $this->char = $char;
        $this->isEnd = false;
    }

    /**
     * @return string
     */
    public function getChar() {
        return $this->char;
    }

    /**
     * @return bool
     */
    public function isEnd() {
        return $this->isEnd;
    }

    /**
     * @param $isEnd bool
     */
    public function setIsEnd($isEnd) {
        $this->isEnd = $isEnd;
    }

    /**
     * @param TrieTreeNode $node
     */
    public function addChild(TrieTreeNode $node) {
        $this->children[$node->getChar()] = $node;
    }
    
    public function getChild() {
       return $this->children;
    }

    /**
     * @param $char
     * @return TrieTreeNode|null
     */
    public function findChildByChar($char) {
        if (isset($this->children[$char])) {
            return $this->children[$char];
        } else {
            return null;
        }
    }
    
}

class TrieTree {

    private $rootNode;

    public function __construct() {
        $this->rootNode = new TrieTreeNode(TrieTreeNode::ROOT_CHAR);
    }

    public function insert($str) {
        $len = mb_strlen($str);
        $searchNode = $this->rootNode;
        for ($i = 0; $i < $len; $i++) {
            //按照每个字符进行遍历
            $char = mb_substr($str, $i, 1);
            $targetNode = $searchNode->findChildByChar($char);
            if (is_null($targetNode)) {
                $targetNode = new TrieTreeNode($char);
                $searchNode->addChild($targetNode);
            }

            $searchNode = $targetNode;
        }

        //标记这是最后一个词
        $searchNode->setIsEnd(true);
        return $searchNode;
    }

    public function search($str) {
        $len = mb_strlen($str);
        $searchNode = $this->rootNode;
        for ($i = 0; $i < $len; $i++) {
            $char = mb_substr($str, $i, 1);
            $targetNode = $searchNode->findChildByChar($char);
            if (!is_null($targetNode)) {

                $searchNode = $targetNode;
                if ($targetNode->isEnd() && $i == $len - 1) {
                    return $targetNode;
                }
            }
        }

        return null;
    }

    public function split($str) {
        $len = mb_strlen($str);
        $searchNode = $this->rootNode;
        $matchResult = [];
        $matchChars = [];
        $lastMatchIndex = 0;
        for ($i = 0; $i < $len; $i++) {
            $char = mb_substr($str, $i, 1);
            $targetNode = $searchNode->findChildByChar($char);
            $searchNode = $targetNode;
            if (!is_null($targetNode)) {

                //记录沿途遍历中的每个匹配的字符
                $matchChars[] = $targetNode->getChar();
                //每次匹配都记录最后一次匹配的字符索引位置
                $lastMatchIndex = $i;
                if ($targetNode->isEnd()) {

                    //遇到结束节点代表匹配了一个词，这里把记录下来
                    //的每个字符组成一个词
                    $matchWord = implode('', $matchChars);
                    //如果直接匹配过则自增计数匹配次数
                    if (isset($matchResult[$matchWord])) {
                        $matchResult[$matchWord] += 1;
                    } else {
                        $matchResult[$matchWord] = 1;
                    }

                    //清空匹配的字符并且把搜索节点重新定位到顶部root节点
                    //以开始新的匹配
                    $matchChars = [];
                    $searchNode = $this->rootNode;
                }
            } else {

                //如果有中途有匹配字符，但是最后没有匹配到词导致结束
                //则倒退到最后一次字符的匹配点再从顶部root节点重新
                //开始匹配新的字符
                if (!empty($matchChars)) {
                    $i = $lastMatchIndex;
                }
                $matchChars = [];
                $searchNode = $this->rootNode;
            }
        }

        return $matchResult;
    }

    public function match($str) {
        $len = mb_strlen($str);
        $searchNode = $this->rootNode;
        $matchChars = [];
        $lastMatchIndex = 0;
        for ($i = 0; $i < $len; $i++) {
            $char = mb_substr($str, $i, 1);
            $targetNode = $searchNode->findChildByChar($char);
            $searchNode = $targetNode;
            if (!is_null($targetNode)) {

                $matchChars[] = $targetNode->getChar();
                $lastMatchIndex = $i;
                if ($targetNode->isEnd()) {
                    return true;
                }
            } else {

                if (!empty($matchChars)) {
                    $i = $lastMatchIndex;
                }
                $matchChars = [];
                $searchNode = $this->rootNode;
            }
        }

        return false;
    }
    
    /* 获取所有字符串--递归 */

    function getChildString($node, $str_array = array(), $str = '') {
        
        if ($node->isEnd() == true) {
            $str_array[] = $str;
        }
        if (empty($node->getChild())) {
            return $str_array;
        } else {
            $child = $node->getChild();
            foreach ($child as $k => $v) {
                $str_array = $this->getChildString($v, $str_array, $str . $v->getChar());
            }
            return $str_array;
        }
    }
    
     /*
    * 获取 xx 开头的所有条目（不包括$str)
    * @param string $str  中国   
    * @return array $result [人,人民,武警]
    * @since 2019-04-17
    * @authro Wing
    */
    function searchString($str) {
        
        $len = mb_strlen($str);
        $searchNode = $this->rootNode;
        for ($i = 0; $i < $len; $i++) {
            $char = mb_substr($str, $i, 1);
            $targetNode = $searchNode->findChildByChar($char);
            if (is_null($targetNode)) {
                return false;
            }

            $searchNode = $targetNode;
        }

        return $this->getChildString($searchNode);
    }
    /*
    * 获取 xx 开头的所有条目（包括$str)
    * @param string $str  中国   
    * @return array $result [中国人,中国人民,中国武警]
    * @since 2019-04-17
    * @authro Wing
    */
    function mapArray($str) {
        $search_array = $this->searchString($str);
        $data = array();
        if(!empty($search_array)){
            foreach ($search_array as $key => $value) {
                $data[] = $str . $value;
            }
        }
        return $data;
    }

}
