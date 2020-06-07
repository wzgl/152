<?php
require_once("/var/www/html/webdata/func.php");

//排序
function fileSort(&$a, $key, $IsAsc){
    if(isset($a)) {
        for($i = 0; $i<count($a); $i++) {
            for($j=0; $j<count($a);$j++) {
                if(!isset($a[$i][$key]) || !isset($a[$j][$key]))
                    continue;
                $t = $a[$i];
                if(
                    //$a[$j]["isdir"]
                    ($IsAsc && 	($a[$i][$key] < $a[$j][$key]))
                    || (!$IsAsc && 	($a[$i][$key] > $a[$j][$key]))

                ) {
                    $a[$i] = $a[$j];
                    $a[$j] = $t;
                }

                // 强制把目录排到前面
                if($a[$i]["isdir"]) {
                    $t = $a[$i];
                    $a[$i] = $a[$j];
                    $a[$j] = $t;
                }
            }
        }
    }
}

function quickSort($a, $key, $IsAsc)
{
    $count = count($a);   //统计出数组的长度
    if ($count <= 1) { // 如果个数为空或者1，则原样返回数组
        return $a;
    }
    $index = $a[0][$key]; // 把第一个元素作为标记
    $left = array();    //定义一个左空数组
    $right = array();    //定义一个右空数组
    for ($i = 1; $i < $count; $i++) {
        if (!isset($a[$i][$key])) {
            continue;
        }
        if (($IsAsc && ($a[$i][$key] < $index)) || (!$IsAsc && ($a[$i][$key] > $index))) {
            $left[] = $a[$i];
        } else {
            $right[] = $a[$i];
        }
    }
    $left = quickSort($left, $key, $IsAsc);      //把left数组再看成一个新参数，再递归调用，执行以上的排序
    $right = quickSort($right, $key, $IsAsc);     //把right数组再看成一个新参数，再递归调用，执行以上的排序
    return array_merge($left, array($a[0]), $right);   //最后把每一次的左数组、标记元素、右数组拼接成一个新数组
}

//枚举一个文件夹下面的所有文件，包括子目录中的文件
function enum_file_from_dir($dir_name){
    static $all_files = array();
    if(!is_dir($dir_name))
        return $all_files;

    $dir_instance = dir($dir_name);
    while($file = $dir_instance->read()){
        if($file == '.' || $file == '..')
            continue;

        $file_path = $dir_name.'/'.$file;
        array_push($all_files, $file_path);

        if(is_dir($file_path)){
            enum_file_from_dir($file_path);
        }
    }

    $dir_instance->close();

    return $all_files;
}

function enum_file_info_from_dir($dir_name){
    static $all_files_info = "";
    if (false == ($handle = opendir ( $dir_name )))
        return $all_files_info;

    while ( false !== ($file = readdir ( $handle )) )
    {
        if($file == '.' || $file == '..')
            continue;

        $file_path = $dir_name.'/'.$file;
        if(is_dir($file_path))
        {
            enum_file_info_from_dir($file_path);
        }
        else
        {
            //对不是升级包的进行过滤
            if(false === stripos($file,".exe.rar") &&
                false === stripos($file,".iso") &&
                false === stripos($file,".exe"))
                continue;

            $f = "";
            $f["path"] = $dir_name . "/" . $file;
            $f["name"] = $file;
            $f["md5Path"] = getMd5Name($file);
            $f["user"] = "-";
            $f["exename"] = "-";
            $f["isdir"] = false;
            $f["desc"] = getpatchreadme($f["path"], $f["user"], $f["exename"], $f);
            $f["link"] = $dir_name."/".urlencode($f["name"]);
            if( 0 == strncasecmp($f["link"],"/var/www/html/",13))
            {
                $f["link"] = substr($f["link"],13);
            }
            $f["ext"] = pathinfo($dir_name . "/" . $file, PATHINFO_EXTENSION);
            $f["ico"] = "/icons/compressed.gif";
            if ($f["isdir"])
                $f["ico"] = "/icons/folder.gif";
            $f["size"] = filesize($dir_name . "/" . $file);
            $f["size2"] = byteFormat($f["size"]);
            if ($f["isdir"])
                $f["size2"] = "-";
            $f["etime"] = date("Y-m-d H:i:s", filemtime($dir_name . "/" . $file));
            $all_files_info[] = $f;
        }
    }
    //关闭句柄
    closedir ( $handle );
    return $all_files_info;
}

//将SrcStr中包含$replaceList列表中的字符串用<em>包括起来
// 如enhanceString(array("ni","hao"),"ni123hao"),返回结果为<em>ni</em>123<em>hao</em>
// 源字符串srcStr保持不变
// 此处使用mb_ereg_replace而不用其他的替换函数是为了防止替换中文出现乱码问题，如搜索“晏”可能会将字符串“标题”分割成"xx晏xx",xx为乱码
function enhanceString($replaceList,$srcStr){
    $tmp = $srcStr;
    foreach($replaceList as $key){
        if($key == "")
            continue;

        //这里有些瑕疵，比如本来是大写的内容可能搜索关键字是小写的，结果把源字符串变成小写了
        $tmp = mb_eregi_replace($key,"<em>".$key."</em>",$tmp);
    }
    return $tmp;
}

//保存文件列表信息
function saveArrayToFile($file_list_info,$file_path){
    return file_put_contents($file_path, serialize($file_list_info));
}
//读取保存的文件列表信息
function readArrayFromFile($file_path){
    if(file_exists($file_path)){
        $content = file_get_contents($file_path);
        return unserialize($content);
    }

    return false;
}
?>