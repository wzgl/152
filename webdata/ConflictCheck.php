<?php
/**
 * Description: 升级包冲突检查（检查某个升级包是否与升级包列表中的升级包存在文件冲突）
 * User: renchen@infogo.com.cn
 * Date: 2020/4/8 11:36
 * Version: $Id$
 */
require_once('getPatch_comm_func.php');

class ConflictCheck
{
    /**
     * 返回数据.
     *
     * @var array
     */
    public $return = array(
        'code' => 1,
        'msg' => 'success',
        'data' => '',
    );

    /**
     * post 升级包列表.
     *
     * @var string
     */
    public $packageList;

    /**
     * 升级包列表详情.
     *
     * @var array
     */
    public $packageListInfo = array();

    /**
     * post 待检查的升级包.
     *
     * @var string
     */
    public $checkItem;

    /**
     * 待检查的升级包详情
     * @var array
     */
    public $checkItemInfo = array();

    /**
     *  检查提交的数据
     */
    public function postCheck()
    {
        // 接收传过来的参数
        $this->packageList = isset($_POST['packageList']) ? iconv('UTF-8','GBK',$_POST['packageList']) : '';
        $this->checkItem = isset($_POST['checkItem']) ? iconv('UTF-8','GBK',$_POST['checkItem']) : '';

        if(!$this->packageList || !$this->checkItem) {
            $this->return['code'] = -1;
            $this->return['msg'] = '请输入升级包列表和需要检查的升级包名称';
        }

        // 目前只支持一次检查一个包
        $isCheckOneItem = array_unique(explode("\n",trim($this->checkItem)));
        if(count(array_filter($isCheckOneItem)) > 1) {
            $this->return['code'] = -2;
            $this->return['msg'] = '只支持一次检查一个升级包，请在最下方的输入框输入一个升级包名称进行检查';
        }

        // 如果检查结构不通过直接返回响应信息
        if($this->return['code'] !== 1) {
            $this->response();
        }
    }

    /**
     * 检查是否有冲突.
     *
     * @throws Exception
     */
    public function check()
    {
        // 去除空行 重复的
        $packageList = array_filter(array_unique(explode("\n",trim($this->packageList))));
        // 过滤打重复升级包的
        foreach ($packageList as $k => $v) {
            $item = explode(' ',$v);
            $packageList[$k] = end($item);
        }
        $packageList = array_values(array_unique($packageList));

        $this->packageListInfo = getUploadPatchInfoList($packageList);
        $packageNames = arrayColumn($this->packageListInfo,'name');
        $this->checkItemInfo = getUploadPatchInfoList($this->checkItem);
        if (!isset($this->checkItemInfo[0])) {
            throw new Exception('系统中没有找到待检查的文件包');
        }
        if (!$this->packageListInfo) {
            throw new Exception('系统中没有找到升级包文件信息');
        }

        // 有哪些包没有找到
        foreach ($packageList as $k => $v) {
            if(strlen($v)<9 || strpos($v, '.exe') === false) {
                continue;
            }

            if(!in_array($v,$packageNames,true)) {
                $this->return['data'] .= $v.PHP_EOL;
            }
        }
        if ($this->return['data']) {
           $this->return['data'] = '请注意，以下这些包在系统中未找到：'.PHP_EOL.$this->return['data'].PHP_EOL;
        }
        $checkItemFiles = getPatchFileListByMd5($this->checkItemInfo[0]['md5Path']);

        $conflict = false;
        foreach ($this->packageListInfo as $key => $item) {
            // 获取文件信息
            $files = getPatchFileListByMd5($item['md5Path']);
            // 如果在升级包列表中包含待检查的列表
            if ($item['name'] === $this->checkItemInfo[0]['name']) {
                $conflict = true;
                $this->return['data'] = PHP_EOL.'请注意：升级包列表中已包含待检查的升级包'.PHP_EOL;
                break;
            }
            $this->return['data'] .= '在【'.$item['name'].'】中'.PHP_EOL;
            $result = '';
            foreach ($files as $k => $v) {
                if (in_array($v,$checkItemFiles,true)) {
                    $conflict = true;
                    $result .= $v.' 已存在'.PHP_EOL;
                }
            }
            $this->return['data'] .= $result ? $result.PHP_EOL : '没有冲突文件'.PHP_EOL.PHP_EOL;
        }

        // 没有冲突
        if(!$conflict) {
            $this->return['data'] = '恭喜你，检查的升级包与其他升级包没有冲突'.PHP_EOL;
        }

        return true;
    }

    /**
     * 请求响应.
     */
    public function response()
    {
        $this->return['msg'] = iconv('GBK','UTF-8',$this->return['msg']);
        $this->return['data'] = $this->return['data'] !== '' ? $this->return['data'] : PHP_EOL.'没有冲突';
        $this->return['data'] = iconv('GBK','UTF-8',$this->return['data']);
        die(json_encode($this->return));
    }
}

$obj = new ConflictCheck();
try {
    $obj->postCheck();
    $obj->check();
} catch (Exception $e) {
    $obj->return['code'] = $e->getCode();
    $obj->return['msg'] = $e->getMessage();
}
$obj->response();
