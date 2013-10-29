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

    function formatSize($size)
    {
        $sizes = Array('B', 'KB', 'MB', 'GB', 'TB', 'PB', 'EB');
        $y     = $sizes[0];
        for ($i = 1; (($i < count($sizes)) && ($size >= 1024)); $i++) {
            $size = $size / 1024;
            $y    = $sizes[$i];
        }
        return round($size, 2) . " " . $y;
    }

    function getHtml()
    {
        return <<<EOD
<tr>
    <td>
        <img src="{$this->image}">
    </td>
    <td>{$this->name}</td>
    <td>{$this->len}</td>
    <td>{$this->size}</td>
    <td>{$this->modTime}</td>
</tr>

EOD;
    }

}


$ALLOWED_TYPES = array("png", "jpeg", "ico", "gif");

$dir     = '.';
$pattern = "/^[^_]*_[^_]*/";

$files   = scandir($dir);
$file_groups  = array();
$groups = array();

foreach ($files as $key => $file) {
    $type = strtolower(pathinfo($file, PATHINFO_EXTENSION));
    
    if (in_array($type, $ALLOWED_TYPES)) {

        preg_match($pattern, $file, $matches);
        // echo $file . ' ';
        // print_r($matches);
        // echo '<br>';

        $file_groups[$matches[0]][] = $file;
    }
}

foreach ($file_groups as $name => $files) {
    $groups[] = new Group($name, $files);
}

// print_r($groups);

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
        <i class="fa fa-folder"></i> 
        <span>Shared Assets</span>
        <button><i class="fa fa-cloud-download"></i><span>Download All</span></button>
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
                                    foreach ($groups as $key => $group) {
                                        echo $group->getHtml();
                                    }
                                ?>
                            </tbody>
    </table>

</body>

</html>
