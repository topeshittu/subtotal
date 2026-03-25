<?php

return [
[
    'label' => 'manufacturing::lang.manufacturing',
    'url' => '/manufacturing/recipe',
    'icon_type' => 'svg',
    'icon_svg' => '<svg xmlns="http://www.w3.org/2000/svg" class="icon-size-20" width="20" height="20"  stroke-width="32" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 576 512"><path d="M64 32C46.3 32 32 46.3 32 64l0 240 0 48 0 80c0 26.5 21.5 48 48 48l416 0c26.5 0 48-21.5 48-48l0-128 0-151.8c0-18.2-19.4-29.7-35.4-21.1L352 215.4l0-63.2c0-18.2-19.4-29.7-35.4-21.1L160 215.4 160 64c0-17.7-14.3-32-32-32L64 32z"/></svg>',
    'permission' => 'manufacturing.access_recipe||manufacturing.access_production||@Admin',
    'sort_order' => 21,
    'module' => 'Manufacturing',
],
];