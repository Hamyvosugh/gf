<?php
namespace Gahshomar;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;

class Elementor_Conversion_Result_Widget extends Widget_Base {

    public function get_name() {
        return 'conversion_result';
    }

    public function get_title() {
        return __('نتیجه برابری', 'gahshomar-converter');
    }

    public function get_icon() {
        return 'eicon-post-list';
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
        <div id="conversion-result-output">
            
            <p>از نگاره بالا برای دگرگون‌کردن تاریخ خود بهره بگیرید، همچنین می‌توانید زادروز خود را در گاهشمارهای گوناگون ایرانی ببینید.</p>
        </div>
        <script>
        jQuery(document).ready(function($) {
            $(document).on('gahshomarConversionResultUpdated', function() {
                var result = window.gahshomarConversionResult || "ما کوشیده‌ایم تا بزرگترین گنجینه داده‌های تاریخی ایران را گردآوری کنیم و در دسترس شما بگذاریم. گاه‌گردان گاهشمار، نخستین دگرگونساز تاریخی برای ایرانیان است که می‌تواند دوران‌های کهن را نیز برآورد کند، اما این مورد که وارد کرده اید، شدنی نیست.";
                $('#conversion-result-output').html('<p>' + result + '</p>');
                console.log("Conversion Result: ", result); // Debugging the result

                // Trigger a custom event with the conversion result
                $(document).trigger('conversionResultAvailable', [result]);
            });
        });
        </script>
        <?php
    }
}

?>