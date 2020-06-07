<?php
$start = microtime(true);
$count = 0;
require_once("search_comm_func.php");

//调用获取整个文件列表
$all_files = array();
$filters = $_REQUEST['search'];
$isdetailed = isset($_REQUEST['isdetailed']) ? $_REQUEST['isdetailed'] : 0;

//如果直接刷新页面则什么都不操作
if (isset($filters) && $filters !== "") {
    $files = readArrayFromFile("fileinfo");
    //如果文件不存在或者文件太小，重新获取文件列表
    if (false === $files || filesize("fileinfo") < 1024) {
        //将遍历到的文件列表保存到文件中
        $files = enum_file_info_from_dir("newasmpublish");
        saveArrayToFile($files, "fileinfo");
    }

    $filters = trim($filters); //去除两边空格
    $split_filter = explode(" ", $filters);
    $split_filter = array_unique($split_filter); //去除重复项
    $time1 = microtime(true);
    if ($isdetailed == 1) {
        foreach ($files as $file) {
            if (file_exists($file["path"])) {
                $tmpPath = "/var/www/html/_dir/patchreadme/" . $file["md5Path"] . "/flist";
                $packet_file_list = file_get_contents($tmpPath);
                foreach ($split_filter as $key) {
                    if ($key == "") {
                        continue;
                    }
                    if (false !== mb_stripos($packet_file_list, $key, 0, "gbk") ||
                        false !== mb_stripos($file["path"], $key, 0, "gbk") ||
                        false !== mb_stripos($file["user"], $key, 0, "gbk")) {
                        $all_files[] = $file;
                        break;
                    }
                }
            }
        }
    } else {
        foreach ($files as $file) {
            if (file_exists($file["path"])) {
                foreach ($split_filter as $key) {
                    if ($key == "") {
                        continue;
                    }
                    if (false !== mb_stripos($file["path"], $key, 0, "gbk") ||
                        false !== mb_stripos($file["user"], $key, 0, "gbk")) {

                        $all_files[] = $file;
                        break;
                    }
                }
            }
        }
    }
}


//排序
$keyName = isset($_REQUEST['C']) ? $_REQUEST['C'] : 'etime';
$bIsAsc = isset($_REQUEST['D']) ? $_REQUEST['D'] : 0;
$all_files = quickSort($all_files, $keyName, $bIsAsc);
$bIsAsc = $bIsAsc ? 0 : 1;
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
        "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <title>ASM补丁包列表</title>
    <META HTTP-EQUIV="Content-Type" CONTENT="text/html; charset=gbk">
    <script src="/_dir/jquery-1.6.4.js"></script>
    <script src="/_dir/jquery.bpopup.js"></script>
    <style type="text/css">
        table {
            table-layout: fixed;
            width: 100%;
            margin: 0px auto;
        }

        em {
            color: red;
            font-style: normal;
        }

        .summary {
            font-size: 18px;
            color: green;
            height: 35px;
            margin: 5px 0 0 65px;
            text-align: left;
        }

        .td2 {
            width: 30%;
            text-overflow: ellipsis;
            overflow: hidden;
            white-space: nowrap;
        }

        .td6 {
            width: 40%;
            text-overflow: ellipsis;
            overflow: hidden;
            white-space: nowrap;
        }

        .button {
            background-color: #2b91af;
            border-radius: 10px;
            box-shadow: 0 2px 3px rgba(0, 0, 0, 0.3);
            color: #fff;
            cursor: pointer;
            display: inline-block;
            padding: 10px 20px;
            text-align: center;
            text-decoration: none;
        }

        .button.b-close, .button.bClose {
            border-radius: 7px 7px 7px 7px;
            box-shadow: none;
            font: bold 131% sans-serif;
            padding: 0 6px 2px;
            position: absolute;
            right: -7px;
            top: -7px;
        }

        .button:hover {
            background-color: #1e1e1e;
        }
    </style>
    <script type="text/javascript">
        function showfilelist(id) {
            var md5path = $('#' + "file_md5Path_" + id).val();

            $("#listfile_dlg_if").attr("src", "/webdata/showfilelist.php?md5path=");
            $('#main_dsc2').html($('#desc_' + id).text());
            $("#listfile_dlg_if").attr("src", "/webdata/showfilelist.php?md5path=" + md5path);
            $('#listfile_dlg').bPopup();
        }

        function showallfile() {
            $("#md5path").val("");
            $('.file_md5Path').each(
                function () {
                    // alert($(this).val())
                    var old = $("#md5path").val();
                    $("#md5path").val(old + $(this).val() + ",");
                });
            $("#submit_md5path").click();
            //alert($("#md5path").val());

        }
    </script>
</head>
<body>
<div style="float:right">
    <a href="###" onclick="showallfile()" title="显示所有升级文件">
        <img width="40px" style="border:0px;" src="/flist.png"/>
    </a>
</div>
<?php
echo "<span style='color:green;padding-left: 65px;'>关键字:" . $filters . "</span>";
echo "<div class=\"summary\">概要:共有" . count($all_files) . "个相似升级包文件</div>";
?>
<table>
    <tr>
        <th width="30px"><img src="/icons/blank.gif" alt="[ICO]"/></th>
        <th class="td2"><a href="<?php echo "?C=link&D=$bIsAsc&search=$filters&isdetailed=$isdetailed"; ?>">Name</a>
        </th>
        <th width="200px"><a href="<?php echo "?C=etime&D=$bIsAsc&search=$filters&isdetailed=$isdetailed"; ?>">Last
                Modified</a></th>
        <th width="100px"><a href="<?php echo "?C=size&D=$bIsAsc&search=$filters&isdetailed=$isdetailed"; ?>">Size</a>
        </th>
        <th width="70px"><a href="<?php echo "?C=user&D=$bIsAsc&search=$filters&isdetailed=$isdetailed"; ?>">User</a>
        </th>
        <th align="center" class="td6"><a href="<?php echo "?C=path&D=$bIsAsc&search=$filters"; ?>">Path</a></th>
        <th align="right">Action</th>
    </tr>
    <tr>
        <th colspan="8">
            <hr/>
        </th>
    </tr>
    <?php
    $i = 0;
    $file_num = 0;
    foreach ($all_files as $file) {
        $i++;
        $id = "desc_$i";
        echo "<input type='hidden' id='file_name_$i' value=\"{$file["name"]}\" />";
        echo "<input type=hidden id=file_path_$i value=\"{$file["path"]}\" />";
        echo "<input type=hidden class=file_md5Path id=file_md5Path_$i value=\"{$file["md5Path"]}\" />";
        echo "\n<tr>\n";
        echo "<td width='60px'>&nbsp;</td>\n";
        $shortName = getShortName($file["name"]);
        $link = $file["link"];

        //将匹配的关键字变红
        $tmp = enhanceString($split_filter, $shortName);
        echo "	<td class='td2'><a href=\"$link\"  title=\"{$file["name"]}\">$tmp</a></td>\n";

        $tmp = enhanceString($split_filter, $file["etime"]);
        echo "	<td align=\"right\">$tmp</td>\n";

        $tmp = enhanceString($split_filter, $file["size2"]);
        echo "	<td align=\"right\">{$tmp}</td>\n";

        $desc = "<textarea id=$id style=\"display:none\">{$file["desc"]}</textarea>\n";
        echo "	$desc";

        $tmp = enhanceString($split_filter, $file["user"]);
        echo "	<td align=\"left\">{$tmp}</td>";

        $tmp1 = $file["path"];
        if (0 == strncasecmp($file["path"], "/var/www/html/webdata/newasmpublish/", 36)) {
            $tmp1 = substr($file["path"], 36);
        }
        $tmp = enhanceString($split_filter, $tmp1);
        echo "	<td align=\"left\" class='td6' title=\"{$file["path"]}\">{$tmp}<textarea style=\"display:none\">{$file["md5"]}</textarea></td>\n";
        echo "	<td align=\"right\" width=\"100px\" >";

        if (false !== stripos($file["name"], ".exe.rar")) {
            echo "	<img width=\"32px\" title=\"显示升级包内容\" src=\"/flist.png\" alt=\"[   ]\"  onclick=showfilelist('$i') style=\"cursor:pointer;\">";
            //echo "	<img width=\"32px\" title=\"删除\" src=\"/del.png\" alt=\"[   ]\"  onclick=delfile('$i') style=\"cursor:hand;\">\n";
        }

        echo "</td>\n";
        echo "</tr>\n";
    }
    ?>
</table>

<div id="listfile_dlg"
     style="border:1px solid #2B91AF; background-color:#fff; border-radius:15px;color:#000;display:none;padding:20px;min-width:450px;min-height:180px;">
    <span class="button b-close"><span>X</span></span>
    <p style="margin-left:10px;margin-top: 20px; line-height: 30px;">
        <span id="main_dsc2"></span>
        <br>
        <iframe id="listfile_dlg_if" name="listfile_dlg_if" style="border:0px solid; width:700px;height:50px"
                src="/webdata/showfilelist.php"></iframe>
    </p>
</div>
<div style="display:none">
    <form method="post" target="_blank" action="/webdata/showallfilelist.php">
        <input id="md5path" name="md5path" type="text" value=""/>
        <input id="submit_md5path" type="submit"/>
    </form>
</div>
<script type="text/javascript">
    function ResetIframeSize(childHieght) {
        $("#listfile_dlg_if").height(childHieght);
    }
</script>
</body>
</html>
<?php
$end = microtime(true);
echo '耗时' . $count . '秒';
echo '<br/>';
echo '总耗时' . ($end - $start) . '秒';