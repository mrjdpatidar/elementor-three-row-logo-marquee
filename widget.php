<?php
if (!defined('ABSPATH')) exit;
class TRLM_Logo_Marquee_Widget extends \Elementor\Widget_Base {
    public function get_name() { return 'trlm_logo_marquee'; }
    public function get_title() { return '3 Row Logo Marquee'; }
    public function get_icon() { return 'eicon-slider-push'; }
    public function get_categories() { return ['general']; }
    public function get_keywords() { return ['logo','marquee','slider','brands','responsive']; }
    public function get_style_depends() { return ['trlm-style']; }
    public function get_script_depends() { return ['trlm-marquee']; }
    protected function register_controls() {
        $c=\Elementor\Controls_Manager::class;
        for ($r=1;$r<=3;$r++) {
            $this->start_controls_section('row'.$r,['label'=>'Row '.$r]);
            $rep=new \Elementor\Repeater();
            $rep->add_control('image',['label'=>'Logo','type'=>$c::MEDIA]);
            $rep->add_control('link',['label'=>'Logo Link','type'=>$c::URL]);
            $rep->add_control('alt',['label'=>'Alt Text','type'=>$c::TEXT]);
            $this->add_control('logos'.$r,['label'=>'Logos','type'=>$c::REPEATER,'fields'=>$rep->get_controls(),'title_field'=>'Logo {{{ _id }}}']);
            $this->add_control('direction'.$r,['label'=>'Direction','type'=>$c::SELECT,'options'=>['left'=>'Right to Left','right'=>'Left to Right'],'default'=>$r===2?'right':'left']);
            $this->add_control('speed'.$r,['label'=>'Marquee duration (seconds)','type'=>$c::NUMBER,'min'=>3,'max'=>300,'default'=>30,'condition'=>['animation_mode'=>'marquee']]);
            $this->add_control('interval'.$r,['label'=>'Slide time per logo (seconds)','type'=>$c::NUMBER,'min'=>0.5,'max'=>30,'step'=>0.5,'default'=>2.5,'description'=>'Rows use the shared slide interval below; this legacy per-row value is retained for compatibility.','condition'=>['animation_mode'=>'slide']]);
            $this->end_controls_section();
        }
        $this->start_controls_section('behavior',['label'=>'Slider Settings']);
        $this->add_control('animation_mode',['label'=>'Animation Type','type'=>$c::SELECT,'options'=>['marquee'=>'Smooth Marquee','slide'=>'Slide (Step by Step)'],'default'=>'slide','description'=>'Slide: clear logo-by-logo movement. Smooth Marquee: constant movement.']);
        $this->add_control('sync_interval',['label'=>'Synchronized Slide Interval (seconds)','type'=>$c::NUMBER,'min'=>0.5,'max'=>15,'step'=>0.5,'default'=>1.8,'condition'=>['animation_mode'=>'slide'],'description'=>'All rows move together on one shared clock.']);
        $this->add_control('motion_fraction',['label'=>'Slide Movement (%)','type'=>$c::SLIDER,'range'=>['px'=>['min'=>20,'max'=>90]],'default'=>['size'=>85],'condition'=>['animation_mode'=>'slide'],'description'=>'Percentage of each interval spent moving; remaining time is the pause.']);
        $this->add_control('autoplay',['label'=>'Autoplay','type'=>$c::SWITCHER,'return_value'=>'yes','default'=>'yes']);
        $this->add_control('pause',['label'=>'Pause on Hover','type'=>$c::SWITCHER,'return_value'=>'yes','default'=>'yes']);
        $this->add_control('grayscale',['label'=>'Grayscale Logos','type'=>$c::SWITCHER,'return_value'=>'yes','default'=>'yes']);
        $this->add_control('hover_color',['label'=>'Color on Hover','type'=>$c::SWITCHER,'return_value'=>'yes','default'=>'yes']);
        $this->add_control('open_new',['label'=>'Open Links in New Tab','type'=>$c::SWITCHER,'return_value'=>'yes','default'=>'']);
        $this->end_controls_section();
        $this->start_controls_section('responsive',['label'=>'Responsive Logo Count']);
        $this->add_responsive_control('visible',['label'=>'Visible Logos per Row','type'=>$c::NUMBER,'min'=>1,'max'=>12,'step'=>1,'default'=>['size'=>6],'tablet_default'=>['size'=>4],'mobile_default'=>['size'=>2], 'selectors'=>['{{WRAPPER}} .trlm'=>'--trlm-visible: {{VALUE}};']]);
        $this->end_controls_section();
        $this->start_controls_section('style',['label'=>'Style','tab'=>$c::TAB_STYLE]);
        $this->add_responsive_control('logo_height',['label'=>'Logo Height','type'=>$c::SLIDER,'size_units'=>['px'],'range'=>['px'=>['min'=>20,'max'=>250]],'default'=>['size'=>75,'unit'=>'px'],'selectors'=>['{{WRAPPER}} .trlm'=>'--trlm-height: {{SIZE}}{{UNIT}};']]);
        $this->add_responsive_control('logo_gap',['label'=>'Logo Gap','type'=>$c::SLIDER,'size_units'=>['px'],'range'=>['px'=>['min'=>0,'max'=>120]],'default'=>['size'=>24,'unit'=>'px'],'selectors'=>['{{WRAPPER}} .trlm'=>'--trlm-gap: {{SIZE}}{{UNIT}};']]);
        $this->add_responsive_control('row_gap',['label'=>'Row Gap','type'=>$c::SLIDER,'size_units'=>['px'],'range'=>['px'=>['min'=>0,'max'=>120]],'default'=>['size'=>25,'unit'=>'px'],'selectors'=>['{{WRAPPER}} .trlm'=>'--trlm-row-gap: {{SIZE}}{{UNIT}};']]);
        $this->add_responsive_control('logo_padding',['label'=>'Logo Inner Padding','type'=>$c::SLIDER,'size_units'=>['px'],'range'=>['px'=>['min'=>0,'max'=>60]],'default'=>['size'=>5,'unit'=>'px'],'selectors'=>['{{WRAPPER}} .trlm'=>'--trlm-inner-padding: {{SIZE}}{{UNIT}};']]);
        $this->add_control('background',['label'=>'Background','type'=>$c::COLOR,'selectors'=>['{{WRAPPER}} .trlm'=>'background-color: {{VALUE}};']]);
        $this->add_responsive_control('padding',['label'=>'Section Padding','type'=>$c::DIMENSIONS,'size_units'=>['px','em','%'],'selectors'=>['{{WRAPPER}} .trlm'=>'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};']]);
        $this->end_controls_section();
    }
    private function logo_html($logos) {
        $html='';
        $seen_images=[];
        foreach ($logos as $logo) {
            $image=$logo['image']??[];
            $id=absint($image['id']??0);
            $url=$id?wp_get_attachment_image_url($id,'medium_large'):($image['url']??'');
            if (!$url) continue;
            // Prevent repeated identical images within one row before looping.
            $image_key = $id ? 'id:' . $id : 'url:' . strtok($url, '?');
            if (isset($seen_images[$image_key])) continue;
            $seen_images[$image_key] = true;
            $alt=trim($logo['alt']??'');
            if (!$alt && $id) $alt=get_post_meta($id,'_wp_attachment_image_alt',true);
            $img='<img src="'.esc_url($url).'" alt="'.esc_attr($alt).'" loading="lazy" decoding="async">';
            $link=$logo['link']??[];
            if (!empty($link['url'])) {
                $new=(!empty($link['is_external'])||$this->get_settings_for_display('open_new')==='yes');
                $rel=[];
                if ($new) $rel[]='noopener noreferrer';
                if (!empty($link['nofollow'])) $rel[]='nofollow';
                $img='<a href="'.esc_url($link['url']).'"'.($new?' target="_blank"':'').($rel?' rel="'.esc_attr(implode(' ',$rel)).'"':'').'>'.$img.'</a>';
            }
            $html.='<div class="trlm__item">'.$img.'</div>';
        }
        return $html;
    }
    protected function render() {
        $s=$this->get_settings_for_display();
        $mode=($s['animation_mode']??'marquee')==='slide'?'slide':'marquee';
        $classes='trlm trlm--'.$mode;
        foreach (['pause','grayscale','hover_color'] as $setting) {
            if (($s[$setting]??'')==='yes') $classes.=' trlm--'.(['grayscale'=>'gray','hover_color'=>'hover-color','pause'=>'pause'][$setting]);
        }
        if (($s['autoplay']??'yes')!=='yes') $classes.=' trlm--stopped';
        echo '<div class="'.esc_attr($classes).'" data-sync-interval="'.esc_attr(max(.5,min(15,(float)($s['sync_interval']??2.5)))).'" data-motion-fraction="'.esc_attr(max(.2,min(.9,(float)($s['motion_fraction']['size']??55)/100))).'" aria-label="Brand logos">';
        for ($r=1;$r<=3;$r++) {
            $logos=$s['logos'.$r]??[];
            $html=$this->logo_html($logos);
            if (!$html) continue;
            $count=substr_count($html,'class="trlm__item"');
            if (!$count) continue;
            $direction=($s['direction'.$r]??'left')==='right'?'right':'left';
            $duration=$mode==='slide'?max(.5,min(30,(float)($s['interval'.$r]??3)))*$count:max(3,min(300,(float)($s['speed'.$r]??30)));
            echo '<div class="trlm__row trlm__row--'.esc_attr($direction).'" data-mode="'.esc_attr($mode).'" data-interval="'.esc_attr(max(.5,min(30,(float)($s['interval'.$r]??2.5)))).'" style="--trlm-duration:'.esc_attr($duration).'s;--trlm-steps:'.esc_attr($count).'">';
            echo '<div class="trlm__track">';
            echo '<div class="trlm__set">'.$html.'</div>';
            echo '</div></div>';
        }
        echo '</div>';
    }
}
