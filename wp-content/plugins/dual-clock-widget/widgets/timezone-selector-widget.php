<?php
if (!defined('ABSPATH')) {
   exit;
}

class Timezone_Selector_Widget extends \Elementor\Widget_Base {
   
   public function get_name() {
       return 'timezone_selector';
   }
   
   public function get_title() {
       return 'Timezone Selector';
   }
   
   public function get_icon() {
       return 'eicon-globe';
   }
   
   public function get_categories() {
       return ['general'];
   }
   
   protected function _register_controls() {
       $this->start_controls_section(
           'section_content',
           [
               'label' => 'Settings',
               'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
           ]
       );

       $this->add_control(
           'selector_width',
           [
               'label' => 'Width',
               'type' => \Elementor\Controls_Manager::SLIDER,
               'size_units' => ['px', '%'],
               'range' => [
                   'px' => ['min' => 100, 'max' => 500],
                   '%' => ['min' => 10, 'max' => 100],
               ],
               'default' => [
                   'unit' => '%',
                   'size' => 100,
               ],
               'selectors' => [
                   '{{WRAPPER}} .dcw-timezone-wrapper' => 'width: {{SIZE}}{{UNIT}};',
               ],
           ]
       );

       $this->end_controls_section();
   }
    
   protected function render() {
    ?>
    <div class="dcw-timezone-wrapper">
        <select id="dcw-timezone-select" class="dcw-timezone-select"></select>
    </div>
    <?php
}
}