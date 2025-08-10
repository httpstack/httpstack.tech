<?php
trait TemplateMwRoutines{
    public function array2Nav(array $array, $arrCfg = []){
        if(!isset($arrCfg)){
            $arrCfg = [
                'ul_class' => 'nav-list',
                'li_class' => 'list-item',
                'a_class' => 'item-link',
                'i_class' => 'link-icon',
                'active_a_class' => 'active'
            ];
        }
        extract($arrCfg);
        $frgNav = '<ul class="' . $ul_class . '">';
            foreach($array as $page => $settings){
                extract($settings);
                
                $active = $active ? $active_a_class : 'active';
                $frgNav .= `<li class="$li_class">
                                <a href="$url" class="$a_class $active_a_class">
                                    <i class="$i_class  $icon">$page</i>
                                </a>
                            </li>`;
            }
        $frgNav .= '</ul>';
    }

}

?>