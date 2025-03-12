<?php
namespace Gahshomar;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;

class Elementor_Birthstone_Widget extends Widget_Base {

    public function get_name() {
        return 'gahshomar_birthstone';
    }

    public function get_title() {
        return __('سنگ تولد', 'gahshomar-converter');
    }

    public function get_icon() {
        return 'eicon-jewelry';
    }

    public function get_categories() {
        return ['general'];
    }

    protected function register_controls() {
        $this->start_controls_section(
            'content_section',
            [
                'label' => __('محتوا', 'gahshomar-converter'),
                'tab' => Controls_Manager::TAB_CONTENT,
            ]
        );

        $this->end_controls_section();
    }

    protected function render() {
        ?>
        <div id="gahshomar-birthstone-wrapper">
            <h3>سنگ تولد:</h3>
            <p id="gahshomar-birthstone">منتظر...</p>
            <img id="gahshomar-birthstone-img" src="" alt="تصویر سنگ تولد" style="display:none; max-width:100px;"/>
        </div>
        <script>
        jQuery(document).ready(function($) {
            $(document).on('gahshomarConversionResultUpdated', function() {
                var convertedDate = window.gahshomarConversionResult;
                var month = getConvertedMonth(convertedDate);
                var birthstones = [
                    "گارنت", "آمتیست", "آکوامارین", "الماس", "زمرد", "مروارید/الکساندریت",
                    "یاقوت سرخ", "پریدوت", "یاقوت کبود", "اوپال/تورمالین", "توپاز/سیترین", "فیروزه/زیرکن/تانزانیت"
                ];
                var birthstoneImages = [
                    "https://upload.wikimedia.org/wikipedia/commons/1/17/Corundum-215330.jpg", // Example URL for Garnet
                    "https://upload.wikimedia.org/wikipedia/commons/1/17/Corundum-215330.jpg", // Example URL for Amethyst
                    "https://upload.wikimedia.org/wikipedia/commons/1/17/Corundum-215330.jpg", // Example URL for Aquamarine
                    "https://upload.wikimedia.org/wikipedia/commons/1/17/Corundum-215330.jpg", // Example URL for Diamond
                    "https://upload.wikimedia.org/wikipedia/commons/1/17/Corundum-215330.jpg", // Example URL for Emerald
                    "https://upload.wikimedia.org/wikipedia/commons/1/17/Corundum-215330.jpg", // Example URL for Pearl/Alexandrite
                    "https://upload.wikimedia.org/wikipedia/commons/1/17/Corundum-215330.jpg", // Example URL for Ruby
                    "https://upload.wikimedia.org/wikipedia/commons/1/17/Corundum-215330.jpg", // Example URL for Peridot
                    "https://upload.wikimedia.org/wikipedia/commons/1/17/Corundum-215330.jpg", // Example URL for Sapphire
                    "https://upload.wikimedia.org/wikipedia/commons/1/17/Corundum-215330.jpg", // Example URL for Opal/Tourmaline
                    "https://upload.wikimedia.org/wikipedia/commons/1/17/Corundum-215330.jpg", // Example URL for Topaz/Citrine
                    "https://upload.wikimedia.org/wikipedia/commons/b/be/Logan_Sapphire_SI.jpg"  // Example URL for Turquoise/Zircon/Tanzanite
                ];
                var birthstone = birthstones[month - 1];
                var birthstoneImage = birthstoneImages[month - 1];
                $('#gahshomar-birthstone').text(birthstone);
                $('#gahshomar-birthstone-img').attr('src', birthstoneImage).show();
            });

            function getConvertedMonth(convertedDate) {
                // Extract month from convertedDate. This assumes the date is in the format: "weekday day month year"
                var monthNames = [
                    "فروردین", "اردیبهشت", "خرداد", "تیر", "اَمُرداد", "شهریور",
                    "مهر", "آبان", "آذر", "دی", "بهمن", "اسپند",
                    "ژانویه", "فوریه", "مارس", "آوریل", "مه", "ژوئن",
                    "ژوئیه", "اوت", "سپتامبر", "اکتبر", "نوامبر", "دسامبر"
                ];

                for (var i = 0; i < monthNames.length; i++) {
                    if (convertedDate.includes(monthNames[i])) {
                        return (i % 12) + 1;
                    }
                }
                return null;
            }
        });
        </script>
        <?php
    }
}
?>