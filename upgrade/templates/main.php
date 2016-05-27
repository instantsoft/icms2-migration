<!DOCTYPE html>
<html>
<head>
	<title>Мастер миграции</title>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <link type="text/css" rel="stylesheet" href="css/styles.css">
    <script type="text/javascript" src="js/jquery.js"></script>
    <script type="text/javascript" src="js/interface.js"></script>
    <script type="text/javascript" src="js/upgrade.js"></script>
</head>
<body>

    <div id="layout">

        <div id="header" class="section">
            <div class="logo">
                <span>Мастер миграции</span>
            </div>
        </div>

        <table id="main" class="section">
            <tr>

                <td id="sidebar" valign="top">
                    <ul id="steps">
                        <?php foreach($steps as $num => $step) { ?>
                            <li id="<?php echo $step['id']; ?>" <?php if($num==$current_step) { ?>class="active"<?php } ?>>
                                <?php echo $num+1; ?>. <?php echo $step['title']; ?>
                            </li>
                        <?php } ?>
                    </ul>
                </td>

                <td id="body" valign="top">
                    <?php echo $step_html; ?>
                </td>

            </tr>
        </table>

        <div id="footer" class="section">
            <div id="copyright">
                <a href="http://www.instantsoft.ru">InstantSoft</a> &copy; <?php echo date('Y'); ?>
            </div>
        </div>

    </div>

    <script>
        var current_step = <?php echo $current_step; ?>;
    </script>

</body>
</html>
