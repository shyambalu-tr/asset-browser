<?php

class Group {

    var $name;
    var $files;
    var $size;
    var $modTime;
    var $image;
    var $len;


    function Group($name, $files)
    {
        $this->name = $name;
        $this->files = $files;
        $this->len = count($files);

        $tempsize = 0;
        $tempmodtime = 0;
        $pattern = "/_n/";

        foreach ($files as $key => $file) {
            $tempsize += filesize($file);
            $tempmodtime = filemtime($file) > $tempmodtime ? filemtime($file) : $tempmodtime;

            if (preg_match($pattern, $file)) {
                $this->image = $file;
            }
        }

        $this->size = Group::formatSize($tempsize);
        $this->modTime = date("d M Y G:i", $tempmodtime);
    }

    public static function formatSize($size)
    {
        $sizes = Array('B', 'KB', 'MB', 'GB', 'TB', 'PB', 'EB');
        $y     = $sizes[0];
        for ($i = 1; (($i < count($sizes)) && ($size >= 1024)); $i++) {
            $size = $size / 1024;
            $y    = $sizes[$i];
        }
        return round($size, 2) . " " . $y;
    }

    function getGroupHtml()
    {
        return <<<EOD
<tr>
    <td>
        <img src="{$this->image}">
    </td>
    <td><a href="?group={$this->name}">{$this->name}</a></td>
    <td>{$this->len}</td>
    <td>{$this->size}</td>
    <td>{$this->modTime}</td>
</tr>

EOD;
    }

    function getFilesHtml()
    {
        $string = "";

        foreach ($this->files as $key => $file) {
            $size = Group::formatSize(filesize($file));
            $modTime = date("d M Y G:i", filemtime($file));
            
            $string .= <<<EOD
<tr>
    <td>
        <img src="{$file}">
    </td>
    <td><a href="$file">{$file}</a></td>
    <td></td>
    <td>$size</td>
    <td>$modTime</td>
</tr>

EOD;
        }
        return $string;
    }

}


function ZipFiles($files, $filename) {

  $valid_files = array();
    //if files were passed in...
    if(is_array($files)) {
        //cycle through each file
        foreach($files as $file) {
            //make sure the file exists
            if(file_exists($file)) {
                $valid_files[] = $file;
            }
        }
    }
    //if we have good files...
    if(count($valid_files)) {
        //create the archive
        $zip = new ZipArchive();
        if($zip->open($filename,ZIPARCHIVE::CREATE) !== true) {
            return false;
        }
        //add the files
        foreach($valid_files as $file) {
            $zip->addFile($file,$file);
        }
        //debug
        //echo 'The zip archive contains ',$zip->numFiles,' files with a status of ',$zip->status;
        
        //close the zip -- done!
        $zip->close();
    }

      header("Content-type: application/zip");
      header("Content-Disposition: attachment; filename=$filename");
      header("Pragma: no-cache");
      header("Expires: 0");
      readfile("$filename");
      unlink($filename);
      exit;

}


$ALLOWED_TYPES = array("png", "jpeg", "ico", "gif");

$dir     = '.';
$pattern = "/^[^_]*_[^_]*/";

$dirty_files = scandir($dir);
$clean_files = array();
$file_groups = array();
$groups      = array();

$showGroup = false;
$group = null;

foreach ($dirty_files as $key => $file) {
    $type = strtolower(pathinfo($file, PATHINFO_EXTENSION));
    
    if (in_array($type, $ALLOWED_TYPES)) {

        preg_match($pattern, $file, $matches);
        // echo $file . ' ';
        // print_r($matches);
        // echo '<br>';
        $clean_files[] = $file;
        $file_groups[$matches[0]][] = $file;
    }
}

foreach ($file_groups as $name => $files) {
    $groups[] = new Group($name, $files);
}

if (isset($_GET["group"]) && trim($_GET["group"]) !== "") {
    $showGroup = true;
    foreach ($groups as $key => $group_i) {
        // echo $group_i->name;
        // echo $_GET["group"];

        if ($group_i->name == $_GET["group"]) {
            $group = $group_i;
        }
    }
}

if (isset($_GET["download"])) {
    if ($showGroup === true && $_GET["download"] === "group") {
        ZipFiles($group->files, $group->name . ".zip");
    }

    if ($_GET["download"] === "all") {
        ZipFiles($clean_files, "shared_assets.zip");
    }
}

// print_r($groups);
// print_r($showGroup);
// print_r($group);

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title>Shared Assets</title>
    <link rel="stylesheet" href="style.css">
    <link href="//netdna.bootstrapcdn.com/font-awesome/4.0.1/css/font-awesome.css" rel="stylesheet">
    <link href='http://fonts.googleapis.com/css?family=Open+Sans:400,300,600,700' rel='stylesheet' type='text/css'>
</head>

<body>

    <div id="header" class="cl">

        <?php if ($showGroup == false): ?>
        <i class="fa fa-folder"></i> 
        <span>Shared Assets</span>
        <a href="?download=all"class="download"><i class="fa fa-cloud-download"></i><span>Download All</span></a>
        <?php endif ?>

        <?php if ($showGroup == true): ?>
        <a id= "back" href="?"><i class="fa fa-arrow-circle-left"></i></a>
        <img src="<?php echo $group->image ?>"/>
        <span><?php echo $group->name ?></span>
        <a href="?group=<?php echo $group->name ?>&download=group" class="download"><i class="fa fa-cloud-download"></i><span>Download All</span></a>
        <?php endif ?>
        
    </div>
    <table>
        <col class="col1">
        <col class="col2">
        <col class="col3">
        <col class="col3">
        <col class="col5">
        <thead>
            <tr>
                <th></th>
                <th>Name</th>
                <th>Related Files</th>
                <th>Size</th>
                <th>Last Modified</th>
            </tr>
        </thead>
        <tbody>
            <?php
                if ($showGroup == true) {
                    echo $group->getFilesHtml();
                }
                else
                {
                    foreach ($groups as $key => $group) {
                        echo $group->getGroupHtml();
                    }
                }
            ?>
        </tbody>
    </table>

</body>

</html>
