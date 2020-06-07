<?php
/**
 * Description: ��������ͻ��飨���ĳ���������Ƿ����������б��е������������ļ���ͻ��
 * User: renchen@infogo.com.cn
 * Date: 2020/4/8 11:36
 * Version: $Id$
 */
require_once('getPatch_comm_func.php');

class ConflictCheck
{
    /**
     * ��������.
     *
     * @var array
     */
    public $return = array(
        'code' => 1,
        'msg' => 'success',
        'data' => '',
    );

    /**
     * post �������б�.
     *
     * @var string
     */
    public $packageList;

    /**
     * �������б�����.
     *
     * @var array
     */
    public $packageListInfo = array();

    /**
     * post ������������.
     *
     * @var string
     */
    public $checkItem;

    /**
     * ����������������
     * @var array
     */
    public $checkItemInfo = array();

    /**
     *  ����ύ������
     */
    public function postCheck()
    {
        // ���մ������Ĳ���
        $this->packageList = isset($_POST['packageList']) ? iconv('UTF-8','GBK',$_POST['packageList']) : '';
        $this->checkItem = isset($_POST['checkItem']) ? iconv('UTF-8','GBK',$_POST['checkItem']) : '';

        if(!$this->packageList || !$this->checkItem) {
            $this->return['code'] = -1;
            $this->return['msg'] = '�������������б����Ҫ��������������';
        }

        // Ŀǰֻ֧��һ�μ��һ����
        $isCheckOneItem = array_unique(explode("\n",trim($this->checkItem)));
        if(count(array_filter($isCheckOneItem)) > 1) {
            $this->return['code'] = -2;
            $this->return['msg'] = 'ֻ֧��һ�μ��һ�����������������·������������һ�����������ƽ��м��';
        }

        // ������ṹ��ͨ��ֱ�ӷ�����Ӧ��Ϣ
        if($this->return['code'] !== 1) {
            $this->response();
        }
    }

    /**
     * ����Ƿ��г�ͻ.
     *
     * @throws Exception
     */
    public function check()
    {
        // ȥ������ �ظ���
        $packageList = array_filter(array_unique(explode("\n",trim($this->packageList))));
        // ���˴��ظ���������
        foreach ($packageList as $k => $v) {
            $item = explode(' ',$v);
            $packageList[$k] = end($item);
        }
        $packageList = array_values(array_unique($packageList));

        $this->packageListInfo = getUploadPatchInfoList($packageList);
        $packageNames = arrayColumn($this->packageListInfo,'name');
        $this->checkItemInfo = getUploadPatchInfoList($this->checkItem);
        if (!isset($this->checkItemInfo[0])) {
            throw new Exception('ϵͳ��û���ҵ��������ļ���');
        }
        if (!$this->packageListInfo) {
            throw new Exception('ϵͳ��û���ҵ��������ļ���Ϣ');
        }

        // ����Щ��û���ҵ�
        foreach ($packageList as $k => $v) {
            if(strlen($v)<9 || strpos($v, '.exe') === false) {
                continue;
            }

            if(!in_array($v,$packageNames,true)) {
                $this->return['data'] .= $v.PHP_EOL;
            }
        }
        if ($this->return['data']) {
           $this->return['data'] = '��ע�⣬������Щ����ϵͳ��δ�ҵ���'.PHP_EOL.$this->return['data'].PHP_EOL;
        }
        $checkItemFiles = getPatchFileListByMd5($this->checkItemInfo[0]['md5Path']);

        $conflict = false;
        foreach ($this->packageListInfo as $key => $item) {
            // ��ȡ�ļ���Ϣ
            $files = getPatchFileListByMd5($item['md5Path']);
            // ������������б��а����������б�
            if ($item['name'] === $this->checkItemInfo[0]['name']) {
                $conflict = true;
                $this->return['data'] = PHP_EOL.'��ע�⣺�������б����Ѱ���������������'.PHP_EOL;
                break;
            }
            $this->return['data'] .= '�ڡ�'.$item['name'].'����'.PHP_EOL;
            $result = '';
            foreach ($files as $k => $v) {
                if (in_array($v,$checkItemFiles,true)) {
                    $conflict = true;
                    $result .= $v.' �Ѵ���'.PHP_EOL;
                }
            }
            $this->return['data'] .= $result ? $result.PHP_EOL : 'û�г�ͻ�ļ�'.PHP_EOL.PHP_EOL;
        }

        // û�г�ͻ
        if(!$conflict) {
            $this->return['data'] = '��ϲ�㣬����������������������û�г�ͻ'.PHP_EOL;
        }

        return true;
    }

    /**
     * ������Ӧ.
     */
    public function response()
    {
        $this->return['msg'] = iconv('GBK','UTF-8',$this->return['msg']);
        $this->return['data'] = $this->return['data'] !== '' ? $this->return['data'] : PHP_EOL.'û�г�ͻ';
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
