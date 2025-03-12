<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

use Gahshomar\Gahshomar_Converter;

function render_gahshomar_tables($gy, $gm, $gd) {
    $data = Gahshomar_Converter::convert_and_display($gy, $gm, $gd);

    // Include the template file
    include 'template-gahshomar-tables.php';
    ?>
    <script>
        document.getElementById('gregorian-date').innerText = '<?php echo $data['gregorian_date']; ?>';
        document.getElementById('jalaali-date').innerText = '<?php echo $data['jalaali_date']; ?>';
        document.getElementById('leap-year').innerText = '<?php echo $data['is_leap_year'] ? "بله" : "خیر"; ?>';
        document.getElementById('chinese-zodiac').innerText = '<?php echo $data['chinese_zodiac_animal'] . " (" . $data['chinese_zodiac_element'] . ")"; ?>';
        document.getElementById('hindu-year').innerText = '<?php echo $data['hindu_year_name']; ?>';
        document.getElementById('birthstone').innerText = '<?php echo $data['birthstone']; ?>';
        document.getElementById('western-zodiac').innerText = '<?php echo $data['western_zodiac_sign']; ?>';
        document.getElementById('zoroastrian-day').innerText = '<?php echo $data['zoroastrian_day_name']; ?>';
    </script>
    <?php
}
?>