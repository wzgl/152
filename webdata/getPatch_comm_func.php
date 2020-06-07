<?php
    require_once("search_comm_func.php");

    //获取所有的升级包详细信息列表
    function getAllPatchInfoList()
    {
        $files = readArrayFromFile("fileinfo");
        if(false === $files || filesize("fileinfo") < 1024)
        {
            $files = enum_file_info_from_dir("newasmpublish");
            saveArrayToFile($files,"fileinfo");
        }

        return $files;
    }

    //通过升级包日志内容获取打过哪些升级包名字
    function getUploadPatchNameList($patch_log_content)
    {
        $patch_name_list = array();
        //var_dump($patch_log_content);
        //echo "end 日志列表<br/>";
        $arr_list = is_array($patch_log_content) ? $patch_log_content : explode( "\n", $patch_log_content );
        foreach($arr_list as $value)
        {
	        $value = trim($value);
           /* if(strlen($value)<9 || strcasecmp(substr($value, -8), '.exe.rar')) {
                continue;
            }*/

            if(strlen($value)<9 || strpos($value, '.exe') === false) {
                 continue;
            }

            $patch_name = strrchr($value,' ');
            if ($patch_name === false) {
                $patch_name = $value;
            }
            $patch_name_list[] = trim($patch_name);
        }

        return $patch_name_list;
    }

    //通过升级包日志内容获取打过哪些升级包的详细信息
    function getUploadPatchInfoList($patch_log_content)
    {
        $patch_detail_info = "";
        //var_dump($patch_log_content);
        $patch_name_list = getUploadPatchNameList($patch_log_content);
       // echo "打过的升级包文件列表<br/>";
       // var_dump($patch_name_list);
       // echo "end<br/>";
        $all_patch = getAllPatchInfoList();
        foreach($all_patch as $info)
        {
            foreach($patch_name_list as $patch)
            {
                if(0 == strcasecmp($info['name'],$patch))
                {
                    $patch_detail_info[] = $info;
                }
               //if(false !== mb_stripos($patch,$info["name"], 0, "gbk"))
               //{
               //     $patch_detail_info[] = $info;
               //}
            }
        }
        return $patch_detail_info;
    }

    //根据升级包MD5获取文件列表,返回字符串
    function getPatchFileListByMd5($md5Path)
    {
        $file_list = array();
        $tmpPath = "/var/www/html/_dir/patchreadme/".$md5Path."/flist";
        //echo $tmpPath;
        $packet_file_list = file_get_contents($tmpPath);

        // var_dump($packet_file_list);
        if($packet_file_list && count($packet_file_list)>0)
        {
            $fs = array_filter(explode("\n",$packet_file_list));
            foreach($fs as $one_file)
            {
                $one_file = substr($one_file, 1);
                array_push($file_list,$one_file);
            }
        }
        return $file_list;
    }
?>
