<?php
/**
 * Created by PhpStorm.
 * User: zhkbadmin
 * Date: 2019/11/30
 * Time: 14:29
 */

function isHacFile($patchname)
{
    $patchname = str_ireplace(array(" ","\n","\t"),"",$patchname);
    $patchname = iconv("UTF8","GBK",$patchname);
    $aPName = explode("_",$patchname);
    $patchroot = "/var/www/html/webdata/newasmpublish/".$aPName[1]."/".$patchname;
  // echo $patchroot."=======start\n";
    $patchroot_c = "/var/www/html/webdata/newasmpublish/".$aPName[1]."/custom/".$patchname;
   //echo $patchroot_c."\n";
    $patchroot_cc1 = "/var/www/html/webdata/newasmpublish/".$aPName[1]."/custom/浪潮/".$patchname;
   // echo $patchroot_cc1."======end\n";
    $patchroot_cc2 = "/var/www/html/webdata/newasmpublish/".$aPName[1]."/custom/oppo/".$patchname;
  //  echo $patchroot_cc2."======end\n";
    $patchroot_cc3 = "/var/www/html/webdata/newasmpublish/".$aPName[1]."/custom/烟草/".$patchname;
   // echo $patchroot_cc3."======end\n";
    $patchroot_cc4 = "/var/www/html/webdata/newasmpublish/".$aPName[1]."/custom/国网/".$patchname;
    $patchroot_cc5 = "/var/www/html/webdata/newasmpublish/".$aPName[1]."/custom/石化/".$patchname;
   // echo $patchroot_cc4."======end\n";
    $patchroot_p = "/var/www/html/webdata/newasmpublish/".$aPName[1]."/production/".$patchname;
    //echo $patchroot_p."======end\n";
    $patchroot_del = "/var/www/html/del_patch/webdata/newasmpublish/".$aPName[1]."/".$patchname;
    $patchroot_del_c = "/var/www/html/del_patch/webdata/newasmpublish/".$aPName[1]."/custom/".$patchname;
    $patchroot_del_cc1 = "/var/www/html/del_patch/webdata/newasmpublish/".$aPName[1]."/custom/浪潮/".$patchname;
    $patchroot_del_cc2 = "/var/www/html/del_patch/webdata/newasmpublish/".$aPName[1]."/custom/oppo/".$patchname;
    $patchroot_del_cc3 = "/var/www/html/del_patch/webdata/newasmpublish/".$aPName[1]."/custom/烟草/".$patchname;
    $patchroot_del_cc4 = "/var/www/html/del_patch/webdata/newasmpublish/".$aPName[1]."/custom/国网/".$patchname;
    $patchroot_del_cc5 = "/var/www/html/del_patch/webdata/newasmpublish/".$aPName[1]."/custom/石化/".$patchname;
    $patchroot_del_p = "/var/www/html/del_patch/webdata/newasmpublish/".$aPName[1]."/production/".$patchname;
    if(file_exists($patchroot))
    {
        return $patchroot;
    }
    if(file_exists($patchroot_c))
    {
        return $patchroot_c;
    }

    if(file_exists($patchroot_cc1))
    {
        return $patchroot_cc1;
    }if(file_exists($patchroot_cc2))
    {
        return $patchroot_cc2;
    }if(file_exists($patchroot_cc3))
    {
        return $patchroot_cc3;
    }if(file_exists($patchroot_cc4))
    {
        return $patchroot_cc4;
    }if(file_exists($patchroot_cc5))
    {
        return $patchroot_cc5;
    }
    if(file_exists($patchroot_p))
    {
        return $patchroot_p;
    }
    if(file_exists($patchroot_del))
    {
        return $patchroot_del;
    }
    if(file_exists($patchroot_del_c))
    {
        return $patchroot_del_c;
    }
    if(file_exists($patchroot_del_p))
    {
        return $patchroot_del_p;
    }
    if(file_exists($patchroot_del_cc1))
    {
        return $patchroot_del_cc1;
    }
    if(file_exists($patchroot_del_cc2))
    {
        return $patchroot_del_cc2;
    }
    if(file_exists($patchroot_del_cc3))
    {
        return $patchroot_del_cc3;
    }
    if(file_exists($patchroot_del_cc4))
    {
        return $patchroot_del_cc4;
    }
    if(file_exists($patchroot_del_cc5))
    {
        return $patchroot_del_cc5;
    }
    return "";
}
function getDownPatch($path,$cus)
{
    $pathc= "/root/patch/";
    shell_exec("rm -f /root/patch/*");
    //ASMPatch_v5.2.6038.9650_20190605_设备所在位置未显示问题修复.exe.rar
    //ASMPatch_v5.2.6038.9650_20190627_山东省中医院-填写注册信息时，去除设备位置-下级位置可选择_个性化.exe.rar
    $file = file_get_contents($path);
    $aFile = explode("\n",$file);
    $i = 0;
    foreach ($aFile as $patchname)
    {
        if(trim($patchname) == "")
            continue;
        $patchname  = substr($patchname,20);
        $p_path = isHacFile($patchname,$cus);
        //$h_patch = str_ireplace("/var/www/html/","",$p_path);
        if($p_path == "")
        {
            echo $patchname."\n";
            $i +=1;
            continue;
        }

        $i +=1;
        $j = $i;
        if($i <10)
        {
            $j = '00'.$i;
        }
        if($i <100 && $i >9)
        {
            $j = '0'.$i;
        }
        $cmd = "cp ".$p_path ."  ".$pathc.$j.$patchname ;

        shell_exec($cmd);
    }
}
$name = $argv[1]!="" ? $argv[1] : $_REQUEST["patchname"];
//ECHO $name."\n";
echo substr(isHacFile($name),13);

?>