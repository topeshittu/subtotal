<?php

return [
[
    'label' => 'desktopapp::lang.desktopapp',
    'icon_type' => 'svg',
    'icon_svg' => '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" stroke-width="1.5" class="icon-size-20" viewBox="0 0 24 24" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path d="M3.5 8c0-.957.001-1.624.069-2.128.066-.49.186-.748.37-.933.185-.184.444-.304.933-.37C5.376 4.5 6.043 4.5 7 4.5h10c.957 0 1.624.001 2.128.069.49.066.748.186.933.37.184.185.305.444.37.933.068.504.069 1.171.069 2.128v8.5h-17zm.167 8.5c-.645 0-1.167.522-1.167 1.167 0 1.012.82 1.833 1.833 1.833h15.334c1.012 0 1.833-.82 1.833-1.833 0-.645-.522-1.167-1.167-1.167z" /></svg>',
    'permission' => 'superadmin||desktopapp.access||@Admin',
    'sort_order' => 89,
    'module' => 'Desktopapp',
    'children' => [
        [
            'label' => 'desktopapp::lang.clients',
            'url' => '/desktopapp/client',
            'permission' => 'superadmin',
            'sort_order' => 1
        ],
        [
            'label' => 'desktopapp::lang.documentation',
            'url' => '/docs',
            'permission' => 'superadmin||desktopapp.access',
            'sort_order' => 2
        ]
    ]
],
];