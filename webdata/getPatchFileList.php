<?php
    header('content-type:text/html;charset=gb2312');
    //require_once('search_comm_func.php');
    require_once('getPatch_comm_func.php');

    //ͨ����������־�ļ���ѯ�������ļ��б�
    $patch_list = $_POST['patch_list'];
    if (!empty($patch_list)) {
        //���ַ���ת��Ϊgbk����
        $patch_list = iconv('utf-8','gb2312',$patch_list);
        $patch_info =  getUploadPatchInfoList($patch_list);
        $patch_all_file_list = array();
		// ��������Ӧ���ļ� add by renchen 2020-03-18
		$patch_name_file_lit = array();
        if(!empty($patch_info)) {
            foreach($patch_info as $info) {
                $patch_file_list = getPatchFileListByMd5($info['md5Path']);
				$patch_name_file_lit[$info['name']] = $patch_file_list;
                $patch_all_file_list = array_merge($patch_all_file_list, $patch_file_list);
            }
        }

        $file_list = array_unique($patch_all_file_list);
        sort($file_list);
		echo 'ȥ�غ�������ļ��У�'."\r\n";
        foreach($file_list as $file) {
            if(strlen($file)) {
                echo $file . "\r\n";
            }
        }
		echo "\r\n\r\n";
		echo '############################################################'."\r\n\r\n";
		echo '��������Ӧ���ļ���Ϣ��'."\r\n\r\n";
		foreach($patch_name_file_lit as $file => $list) {
			echo $file."\r\n";
            foreach($list as $item) {
				if(strlen($item)) {
					echo $item."\r\n";
				}
			}
			echo "\r\n\r\n";
        }
    }

    //ͨ�����Ƶ��ı����е��������б�������е��������б�
?>
