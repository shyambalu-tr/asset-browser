<?php

class Group {

    var $name;
    var $files;

    function Group($name, $files)
    {
        $this->name = $name;
        $this->files = $files;
    }

    
}


$ALLOWED_TYPES = array("png", "jpeg", "ico", "gif");

$dir     = '.';
$pattern = "/^[^_]*_[^_]*/";

$files   = scandir($dir);
$groups  = array();

foreach ($files as $key => $file) {
    $type = strtolower(pathinfo($file, PATHINFO_EXTENSION));
    
    if (in_array($type, $ALLOWED_TYPES)) {

        preg_match($pattern, $file, $matches);
        // echo $file . ' ';
        // print_r($matches);
        // echo '<br>';

        $groups[$matches[0]][] = $file;
    }
}

print_r($groups);

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
                                <tr>
                                    <td>
                                        <img src="ico_alert_16_n.png" />
                                    </td>
                                    <td>ico_alert</td>
                                    <td>4</td>
                                    <td>32kb</td>
                                    <td>18 Sep 2013 15:23</td>
                                </tr>

                                <tr>
                                    <td>
                                        <img src="ico_alert_16_n.png" />
                                    </td>
                                    <td>ico_alert</td>
                                    <td>4</td>
                                    <td>32kb</td>
                                    <td>18 Sep 2013 15:23</td>
                                </tr>

                                <tr>
                                    <td>
                                        <img src="ico_alert_16_n.png" />
                                    </td>
                                    <td>ico_alert</td>
                                    <td>4</td>
                                    <td>32kb</td>
                                    <td>18 Sep 2013 15:23</td>
                                </tr>

                                <tr>
                                    <td>
                                        <img src="ico_alert_16_n.png" />
                                    </td>
                                    <td>ico_alert</td>
                                    <td>4</td>
                                    <td>32kb</td>
                                    <td>18 Sep 2013 15:23</td>
                                </tr>
                            </tbody>
    </table>

</body>

</html>
