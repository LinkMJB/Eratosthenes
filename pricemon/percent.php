here's some stuff

<?php

if (ob_get_level() == 0) {
    ob_start();
}
echo str_pad('Loading... ',4096)."<br />\n";
for ($i = 0; $i < 25; $i++) {
    $d = $d + 11;
    $m=$d+10;
    //This div will show loading percents
    echo '<div class="percents">' . $i*4 . '%&nbsp;complete</div>';
    //This div will show progress bar
    echo '<div class="blocks" style="left: '.$d.'px">&nbsp;</div>';
    flush();
    ob_flush();
    sleep(1);
}
ob_end_flush();
?>

here's some stuff
