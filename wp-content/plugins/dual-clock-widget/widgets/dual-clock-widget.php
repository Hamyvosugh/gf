<?php
if (!defined('ABSPATH')) {
   exit;
}

class Dual_Clock_Widget extends \Elementor\Widget_Base {
   
   public function get_name() {
       return 'dual_clock';
   }
   
   public function get_title() {
       return 'Dual Clock';
   }
   
   public function get_icon() {
       return 'eicon-clock-o';
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
           'show_local',
           [
               'label' => 'Show Local Clock',
               'type' => \Elementor\Controls_Manager::SWITCHER,
               'default' => 'yes'
           ]
       );

       $this->add_control(
           'show_international',
           [
               'label' => 'Show International Clock',
               'type' => \Elementor\Controls_Manager::SWITCHER,
               'default' => 'yes'
           ]
       );

       $this->end_controls_section();
   }
   
   protected function render() {
    ?>
    <div class="dcw-clock-container">
        <div id="dcw-local-time" class="dcw-clock">
            <img src="https://gahshomar.com/wp-content/uploads/2024/08/Asset-6.svg" class="dcw-clock-logo" alt="Logo">
            <div class="dcw-hour-hand"></div>
            <div class="dcw-minute-hand"></div>
            <div class="dcw-second-hand"></div>
            <div class="dcw-digital-clock"></div>
            <div class="dcw-center-circle"></div>
        </div>
        <div id="dcw-international-time" class="dcw-clock">
            <div class="dcw-hour-hand"></div>
            <div class="dcw-minute-hand"></div>
            <div class="dcw-second-hand"></div>
            <div class="dcw-digital-clock"></div>
            <div class="dcw-center-circle"></div>
        </div>
    </div>
    <?php
}}