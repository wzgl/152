<?php
/**
 * Created by PhpStorm.
 * User: baiyf
 * Date: 2018/4/12
 * Time: 10:48
 */

require_once("search_comm_func.php");

//获取SP包升级包列表（所有的SP包log文件名称）
function getSpPacketsLogList($SP_Dir)
{
    $all_sp_log_list = array();
    if (false == ($handle = opendir ( $SP_Dir )))
        return $all_sp_log_list;
    while ( false !== ($file = readdir ( $handle )) )
    {
        if(strcasecmp(substr($file, -4), ".log"))
            continue;

        array_push($all_sp_log_list, $file);
    }
    closedir ( $handle );

    return $all_sp_log_list;
}

//根据客户现场打过的SP包，然后从152上SP包列表中找出日期最近的SP包
function findSpPacketsByLessDate($sp_log_list, $packet_date)
{
    $find_sp_log = "";
    if(count($sp_log_list) <= 0 || strlen($packet_date) <= 0 || !is_numeric($packet_date))
        return $find_sp_log;

    //对SP包升级包列表先进行升序排列
    asort($sp_log_list);
    foreach($sp_log_list as $log_name)
    {
        //分割出log_name中的日期
        $log_date = substr($log_name,7,8);
        if(!is_numeric($log_date))
            continue;

        if($log_date - $packet_date >= 0)
        {
            $find_sp_log = $log_name;
            break;
        }
    }

    return $find_sp_log;
}

//获取某个SP包中补丁包文件列表
function getSpPacketsList($SP_file_path)
{
    $packet_new_list = array();

    if(!file_exists($SP_file_path))
        return $packet_new_list;

    $content = file_get_contents($SP_file_path);
    $packet_list = explode("\r\n", $content);

    foreach($packet_list as $packet)
    {
        $packet = trim($packet);
        if(strlen($packet) < 10)
            continue;

        array_push($packet_new_list, $packet);
    }

    return $packet_new_list;
}

//获取152上所有的补丁包文件信息
function getAllPacketsInfoList()
{
    $packList = readArrayFromFile("fileinfo");
    if(false === $packList || filesize("fileinfo") < 1024 )
    {
        $packList = enum_file_info_from_dir("newasmpublish");
        saveArrayToFile($packList, "fileinfo");
    }

    return $packList;
}

//业务流程1，根据asmpatchinfo.log内容精简升级包列表，找出哪些是SP包，哪些不是SP包
//返回精简后的升级包列表
function AnalyzePatchLog($patch_log_content)
{
    $simple_patch_list = array();
    if(strlen($patch_log_content) < 20)
        return $simple_patch_list;

    $arr_list = explode("\n", $patch_log_content);
    $last_value = "";
    $max_asm_version = "0";
    $mid_asm_version = "0";

    $array_sp_packets = array();
    foreach($arr_list as $value)
    {
        $value=trim($value);
        if(!strcasecmp(substr($value, -8), ".exe.rar"))
        {
            $patch_name = strrchr($value," ");
            if($patch_name == FALSE)
                $patch_name = $value;

            $patch_name = trim($patch_name);
            array_push($array_sp_packets, $patch_name);

            //找出最高ASM版本和次级ASM版本
            $temp = explode("_", $patch_name);
            if($temp[1] >= $max_asm_version)
                $max_asm_version = $temp[1];
            else if($temp[1] > $mid_asm_version)
                $mid_asm_version = $temp[1];

            $last_value = $patch_name;
        }

        if(strlen($value) > 0 && !stristr($value, ".exe.rar"))
        {
            //var_dump($array_sp_packets);
            //echo "<br/>**************SP**********<br/>";

            $split_value = explode("_", $last_value);
            //找出最相近的大版本SP升级包目录位置
            //echo "MaxVersion:".$max_asm_version."lessVersion:".$mid_asm_version."<br/>";

            //$spfile_dir = "D:\\wamp\\www\\201804\\webdata\\newasmpublish\\".$max_asm_version."\\SP";
            $spfile_dir = "/var/www/html/webdata/newasmpublish/".$max_asm_version."/SP";
            $log_list = getSpPacketsLogList($spfile_dir);
            if(count($log_list) <= 0)
                continue;

            //根据日期查找时间相差最短的SP包补丁列表
            $sp_log_file = findSpPacketsByLessDate($log_list, $split_value[2]);
            if(strlen($sp_log_file) <= 0)
                continue;

            //读取SP包中有哪些升级包文件列表
            //$spfile_path = $spfile_dir."\\".$sp_log_file;
            $spfile_path = $spfile_dir."/".$sp_log_file;

            //对应的SP包名字
            $sp_name = "ASMUpdate_".$max_asm_version."_".substr($sp_log_file, 7, 8)."_SP".substr($sp_log_file,11,4).".exe.rar";
            $splist = getSpPacketsList($spfile_path);

            //把不在SP包的中补丁全部列举出来（目前先不考虑升级包日期不匹配问题）
            $bHasFlag = false;
            foreach($array_sp_packets as $packet_log)
            {
                $bFind = false;

                //如果有特殊标记直接跳出循环
                if(false !== mb_stripos($packet_log,"用于升级SP包的升级包",0,"gbk"))
                {
                    $bHasFlag = true;
                    array_push($simple_patch_list,$sp_name);
                    break;
                }

                $temp_packet_log = explode("_",$packet_log);
                if(count($temp_packet_log) < 4)
                    continue;

                //组成新的字符串
                $new_name = "";
                for($i=3; $i<count($temp_packet_log); $i++)
                {
                    if(strlen($new_name) > 0)
                    {
                        $new_name = $new_name."_".$temp_packet_log[$i];
                    }
                    else
                    {
                        $new_name = $temp_packet_log[$i];
                    }
                }

                //echo $new_name."<br/>";
                foreach($splist as $packet_sp)
                {
                    if(false !== mb_stripos($packet_sp, $new_name,0,"gbk"))
                    {
                        //echo $new_name."  has find<br/>";
                        $bFind = true;
                        break;
                    }
                }

                if(!$bFind)
                {
                    array_push($simple_patch_list, $packet_log);
                }
            }

            if(!$bHasFlag)
            {
                array_push($simple_patch_list, $sp_name);
            }


            if(0) {
                //比较升级包列表区别
                $result = array_diff($array_sp_packets, $splist);

                //列出精简后需要打的升级包
                $index = 0;
                foreach ($result as $key => $item) {
                    //带有这个标签的直接把SP加进去就退出掉
                    //if(false !== mb_stripos($item,iconv('utf-8','gbk',"用于升级SP包的升级包"),0,"utf-8"))
                    if (false !== mb_stripos($item, "用于升级SP包的升级包", 0, "gbk")) {
                        array_push($simple_patch_list, $sp_name);
                        break;
                    }

                    if ($key > $index)
                        break;

                    array_push($simple_patch_list, $item);
                    $index++;
                }
            }

            unset($array_sp_packets);
            $array_sp_packets = array();
        }
    }

    return array_merge($simple_patch_list, $array_sp_packets);
}

//业务流程2，查找精简后的升级包具体信息
function getDetailPacketsInfo($asmpatchlog_content)
{
    $simple_patch_detail_info = "";
    $simple_patch_list = AnalyzePatchLog($asmpatchlog_content);

    //var_dump($simple_patch_list);
    $all_packets_list = getAllPacketsInfoList();

    foreach($simple_patch_list as $packet)
    {
        $bFind = false;
        $info = "";
        foreach($all_packets_list as $one_packet)
        {
            if(!strcasecmp($one_packet["name"], $packet))
            {
                $bFind = true;
                $info = $one_packet;
                break;
            }
        }

        if($bFind)
            $info["exist"] = true;
        else
        {
            $info["name"] = $packet;
            $info["exist"] = false;
        }

        $simple_patch_detail_info[] = $info;
    }

    return $simple_patch_detail_info;
}

//制作html页面，展示具体的升级包信息
//echo "<br/>========================start=================<br/>";

//$file_path = "asmpatchinfo.log";
//$log_content = file_get_contents($file_path);
$log_content = $_POST['loglist'];
$detailInfo = getDetailPacketsInfo($log_content);

//$new_detailInfo = array_unique($detailInfo);
$lose_packet_number  = 0;
foreach($detailInfo as $info)
{
    if(!$info["exist"])
        $lose_packet_number ++;
}

//var_dump($detailInfo);

//去掉重复的升级包列表
//var_dump(array_unique($result));

//echo "<br/>========================end=================<br/>";
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <title>补丁包列表</title>
        <META HTTP-EQUIV="Content-Type" CONTENT="text/html; charset=gbk">
        <script src="/_dir/jquery-1.6.4.js"></script>
        <script src="/_dir/jquery.bpopup.js"></script>
        <style type="text/css">
            table{table-layout: fixed;width: 100%;margin:0px auto;}
            em{color:red;font-style:normal;}
            .summary{font-size: 18px;color:green;height: 35px;margin:5px 0 0 65px;text-align: left;}
            .td2{width:30%; text-overflow:ellipsis;overflow: hidden; white-space: nowrap;}
            .td6{width:40%; text-overflow:ellipsis;overflow: hidden;white-space: nowrap;}
            .button{background-color: #2b91af;border-radius: 10px;box-shadow: 0 2px 3px rgba(0,0,0,0.3);color: #fff;cursor: pointer;display: inline-block;padding: 10px 20px;text-align: center;text-decoration: none;}
            .button.b-close,.button.bClose {border-radius: 7px 7px 7px 7px;box-shadow: none;font: bold 131% sans-serif;padding: 0 6px 2px;position: absolute;right: -7px;top: -7px;}
            .button:hover{background-color: #1e1e1e;}
        </style>
        <script type="text/javascript">
            function showfilelist(id)
            {
                var md5path = $('#'+"file_md5Path_" + id).val();

                $("#listfile_dlg_if").attr("src", "/webdata/showfilelist.php?md5path=");
                $('#main_dsc2').html($('#desc_'+id).text());
                $("#listfile_dlg_if").attr("src", "/webdata/showfilelist.php?md5path=" + md5path);
                $('#listfile_dlg').bPopup();
            }

            function showallfile()
            {
                $("#md5path").val("");
                $('.file_md5Path').each(
                    function(){
                        // alert($(this).val())
                        var old =  $("#md5path").val();
                        $("#md5path").val(old + $(this).val() + "," );
                    });
                $("#submit_md5path").click();
                //alert($("#md5path").val());

            }
        </script>
    </head>
    <body>
        <div style="float:right">
            <a  href="###" onclick="showallfile()" title="显示所有升级文件" >
                <img width="40px" style="border:0px;"src="/flist.png" />
            </a>
        </div>
        <?php
        echo "<span style='color:green;padding-left: 65px;'>总共需要打".count($detailInfo)."个升级包</span>";
        if($lose_packet_number>0)
            echo "<span style='color:red;'>，其中有".$lose_packet_number."个升级包未找到[红色部分为未找到的补丁包]</span>";
        ?>
        <table>
            <tr>
                <th width="30px"><img src="/icons/blank.gif" alt="[ICO]" /></th>
                <th class="td2"><a href="#">Name</a></th>
                <th width="200px"><a href="#">Last Modified</a></th>
                <th width="100px"><a href="#">Size</a></th>
                <th width="70px"><a href="#">User</a></th>
                <th align="center" class="td6"><a href="#">Path</a></th>
                <th align="right">Action</th>
            </tr>
            <tr><th colspan="8"><hr /></th></tr>
            <?php
            $i = 0;
            $file_num = 0;
            foreach($detailInfo as $file){
                $i++;
                $id = "desc_$i";
                echo "<input type='hidden' id='file_name_$i' value=\"{$file["name"]}\" />";
                echo "<input type=hidden id=file_path_$i value=\"{$file["path"]}\" />";
                echo "<input type=hidden class=file_md5Path id=file_md5Path_$i value=\"{$file["md5Path"]}\" />";
                echo "\n<tr>\n";
                echo "<td width='60px'>&nbsp;</td>\n";
                $shortName = getShortName($file["name"]) ;
                $link = $file["link"] ;

                //升级包不存在添加删除线
                if($file["exist"] === false)
                {
                    echo "	<td class='td2'><a href=\"#\"  title=\"{$file["name"]}\" style='text-decoration:none;color:#FF0000;'><s>{$file["name"]}</s></a></td>\n";
                    continue;
                }
                echo "	<td class='td2'><a href=\"$link\"  title=\"{$file["name"]}\">$shortName</a></td>\n";
                echo "	<td align=\"right\">{$file["etime"]}</td>\n";
                echo "	<td align=\"right\">{$file["size2"]}</td>\n";

                $desc = "<textarea id=$id style=\"display:none\">{$file["desc"]}</textarea>\n";
                echo "	$desc";
                echo "	<td align=\"left\">{$file["user"]}</td>";

                $tmp1 = $file["path"];
                if( 0 == strncasecmp($file["path"],"/var/www/html/webdata/newasmpublish/",36))
                {
                    $tmp1 = substr($file["path"],36);
                }
                echo "	<td align=\"left\" class='td6' title=\"{$file["path"]}\">{$tmp1}<textarea style=\"display:none\">{$file["md5"]}</textarea></td>\n";
                echo "	<td align=\"right\" width=\"100px\" >";

                if(false !== stripos($file["name"], ".exe.rar" ))
                {
                    echo "	<img width=\"32px\" title=\"显示升级包内容\" src=\"/flist.png\" alt=\"[   ]\"  onclick=showfilelist('$i') style=\"cursor:pointer;\">";
                    //echo "	<img width=\"32px\" title=\"删除\" src=\"/del.png\" alt=\"[   ]\"  onclick=delfile('$i') style=\"cursor:hand;\">\n";
                }

                echo  "</td>\n";
                echo "</tr>\n";
            }
            ?>
        </table>

        <div id="listfile_dlg" style="border:1px solid #2B91AF; background-color:#fff; border-radius:15px;color:#000;display:none;padding:20px;min-width:450px;min-height:180px;">
            <span class="button b-close"><span>X</span></span>
            <p style="margin-left:10px;margin-top: 20px; line-height: 30px;">
                <span id="main_dsc2"></span>
                <br>
                <iframe id="listfile_dlg_if" name="listfile_dlg_if"  style="border:0px solid; width:700px;height:50px" src="/webdata/showfilelist.php" > </iframe>
            </p>
        </div>
        <div style="display:none">
            <form method="post" target="_blank" action="/webdata/showallfilelist.php">
                <input id="md5path" name="md5path" type="text" value="" />
                <input id="submit_md5path" type="submit" />
            </form>
        </div>
        <script type="text/javascript">
            function ResetIframeSize(childHieght) {
                $("#listfile_dlg_if").height(childHieght);
            }
        </script>
    </body>
</html>