<?php

return [

    'side_nav' => [
        [
            'label' => 'MRMS',
            'icon' => 'dashboard',
            'role' => 'Super Admin|Admin|manager|cashiers|supervisor',
            'children' => [
                [
                    'label' => 'Dashboard',
                    'icon' => 'dashboard',
                    'route' => 'admin.dashboard',
                    'role' => 'Super Admin|Admin|manager|cashiers|supervisor',
                ],
                [
                    'label' => 'Properties',
                    'icon' => 'list',
                    'route' => 'admin.properties',
                    'role' => 'Super Admin|Admin|manager|supervisor',
                ],
                [
                    'label' => 'Inaccessible Properties',
                    'icon' => 'list',
                    'route' => 'admin.inaccessibleproperties',
                    'role' => 'Super Admin|Admin|manager|supervisor',
                ],
                [
                    'label' => 'Unfinished Properties',
                    'icon' => 'list',
                    'route' => 'admin.unfinishedproperties',
                    'role' => 'Super Admin|Admin|manager|supervisor',
                ],
                [
                    'label' => 'Payments',
                    'icon' => 'payment',
                    'route' => 'admin.payment',
                    'role' => 'Super Admin|Admin|manager|cashiers|supervisor',
                ],
                [
                    'label' => 'Reversed Payments',
                    'icon' => 'payment',
                    'route' => 'admin.reverse_payment',
                    'role' => 'Super Admin|Admin|Finance|Auditor',
                ],
                [
                    'label' => 'Assign Properties',
                    'icon' => 'assignment_ind',
                    'route' => 'admin.assign.property',
                    'role' => 'Super Admin|Admin',
                ],
                [
                    'label' => 'Demand Note Settings',
                    'icon' => 'payment',
                    'route' => 'admin.districts.index',
                    'role' => 'Super Admin|Admin',
                ],
                [
                    'label' => 'Envelope Settings',
                    'icon' => 'payment',
                    'route' => 'admin.envelopes.index',
                    'role' => 'Super Admin|Admin',
                ],
                [
                    'label' => 'Approval System',
                    'icon' => 'assignment_ind',
                    'role' => 'Super Admin|Admin',
                    'children' => [
                        [
                            'label' => 'Landlord Details Approval',
                            'icon' => 'assignment_ind',
                            'route' => 'admin.verify.landlord'
                        ],
                        [
                            'label' => 'Property Details Approval',
                            'icon' => 'assignment_ind',
                            'route' => 'admin.verify.property'
                        ]
                    ]
                ],
                [
                    'label' => 'Users',
                    'icon' => 'perm_identity',
                    'role' => 'Super Admin|Admin',
                    'children' => [
                        [
                            'label' => 'System User',
                            'icon' => 'accessible',
                            'children' => [
                                [
                                    'label' => 'List',
                                    'route' => 'admin.system-user.list',
                                ],
                                [
                                    'label' => 'Create',
                                    'route' => 'admin.system-user.create',
                                ],
        
                            ]
                        ],
                        [
                            'label' => 'App User',
                            'icon' => 'apps',
                            'children' => [
                                [
                                    'label' => 'List',
                                    'route' => 'admin.app-user.list',
                                ],
                                [
                                    'label' => 'Create',
                                    'route' => 'admin.app-user.create',
                                ],
        
                            ]
                        ],
                        [
                            'label' => 'Guest Users',
                            'icon' => 'apps',
                            'children' => [
                                [
                                    'label' => 'List',
                                    'route' => 'admin.guest-user.list',
                                ],
                                [
                                    'label' => 'Create',
                                    'route' => 'admin.guest-user.create',
                                ],
        
                            ]
                        ],
        
                    ]
                ],
                [
                    'label' => 'Council Adjustment Parameters',
                    'icon' => 'apps',
                    'role' => 'Super Admin',
                    'children' => [
                        [
                            'label' => 'Adjustment',
                            'route' => 'admin.adjustments.index',
        
                        ],
                        [
                            'label' => 'Adjustment Values',
                            'route' => 'admin.adjustmentValues.index',
        
                        ],
                        [
                            'label' => 'Mill Rates',
                            'route' => 'admin.millrates.index',
        
                        ],
                    ]
                ],
                // [
                //     'label' => 'Assessment Parameter Conditions',
                //     'icon' => 'apps',
                //     'role' => 'Super Admin',
                //     'children' => [
                //         [
                //             'label' => 'Characteristic',
                //             'route' => 'admin.propertyCharacteristics.index',
        
                //         ],
                //         [
                //             'label' => 'Characteristic Values',
                //             'route' => 'admin.propertyCharacteristicValues.index',
        
                //         ],
                //     ]
                // ],
                [
                    'label' => 'Assessment Parameters',
                    'icon' => 'assessment',
                    'role' => 'Super Admin',
                    'children' => [
                        [
                            'label' => 'Property Types',
                            'route' => 'admin.list.property.category',
        
                        ],
                        [
                            'label' => 'Habitable Floors',
                            'route' => 'admin.list.property.type',
        
                        ],
                        [
                            'label' => 'Wall Material',
                            'route' => 'admin.list.property.wall-material',
        
                        ],
                        [
                            'label' => 'Roof Material',
                            'route' => 'admin.list.property.roof-material',
        
                        ],
                        [
                            'label' => 'Property Dimensions',
                            'route' => 'admin.list.property.dimension',
        
                        ],
                        [
                            'label' => 'Value Added',
                            'route' => 'admin.list.property.value-added',
        
                        ],
                        [
                            'label' => 'Property Use',
                            'route' => 'admin.list.property.use',
        
                        ],
                        [
                            'label' => 'Zones',
                            'route' => 'admin.list.property.zone',
        
                        ],
                        [
                            'label' => 'Swimming Pool',
                            'route' => 'admin.list.property.swimming',
        
                        ],
                        [
                            'label' => 'Property Inaccessible',
                            'route' => 'admin.list.property.inaccessible',
        
                        ],
                        [
                            'label' => 'Sanitation Type',
                            'route' => 'admin.list.property.sanitation',
        
                        ],
                        [
                            'label' => 'Window Type',
                            'route' => 'admin.list.property.window',
        
                        ]
                    ]
                ],
                [
                    'label' => 'Names Repository',
                    'icon' => 'donut_small',
                    'role' => 'Super Admin',
                    'children' => [
                        [
                            'label' => 'Create Name',
                            'route' => 'admin.meta.value',
        
                        ],
                        [
                            'label' => 'First Name List',
                            'route' => 'admin.meta.value.first-name',
        
                        ],
                        [
                            'label' => 'Surname List',
                            'route' => 'admin.meta.value.surname',
        
                        ],
                        [
                            'label' => 'Street Name List',
                            'route' => 'admin.meta.value.street-name',
        
                        ],
                    ]
                ],
                [
                    'label' => 'Reports',
                    'icon' => 'graphic_eq',
                    'route' => 'admin.report',
                    'role' => 'Super Admin',
                ],
                [
                    'label' => 'Property Owners',
                    'icon' => 'graphic_eq',
                    'route' => 'admin.tax-payer',
                    'role' => 'Super Admin',
                ],
                [
                    'label' => 'System Setting',
                    'icon' => 'settings',
                    'route' => 'admin.config.community',
                    'role' => 'Super Admin',
                ],
                [
                    'label' => 'Forgot Password Request',
                    'icon' => 'change_history',
                    'route' => 'admin.forgot.request',
                    'role' => 'Super Admin',
                ],
                [
                    'label' => 'Audit Trail',
                    'icon' => 'donut_small',
                    'role' => 'Super Admin',
                    'children' => [
                        [
                            'label' => 'App User Login Trail',
                            'route' => 'admin.audit.user',
        
                        ],
                        [
                            'label' => 'System User Login Trail',
                            'route' => 'admin.audit.admin',
        
                        ],
                        [
                            'label' => 'Property  Trail',
                            'route' => 'admin.audit.property',
        
                        ],
                        [
                            'label' => 'Property Assessment Trail',
                            'route' => 'admin.audit.property.assessment',
        
                        ],
                        [
                            'label' => 'Property Payment Trail',
                            'route' => 'admin.audit.property.payment',
        
                        ],
                        [
                            'label' => 'Property Landlord Trail',
                            'route' => 'admin.audit.property.landlord',
        
                        ],
                        [
                            'label' => 'Property Assigned officer Trail',
                            'route' => 'admin.audit.property.property_assigned_officers',
        
                        ],
                        // [
                        //     'label' => 'Property Occupancy Trail',
                        //     'route' => 'admin.audit.property.occupancy',
        
                        // ],
                        // [
                        //     'label' => 'Property Occupancy Detail Trail',
                        //     'route' => 'admin.audit.property.occupancyDetail',
        
                        // ],
                        // [
                        //     'label' => 'Property Geo Registry Trail',
                        //     'route' => 'admin.audit.property.geoRegistry',
        
                        // ],
                        // [
                        //     'label' => 'Property Registry Meter Trail',
                        //     'route' => 'admin.audit.property.registryMeter',
        
                        // ],
                        // [
                        //     'label' => 'Property Categories Trail',
                        //     'route' => 'admin.audit.assessment.property.categories',
        
                        // ],
                        // [
                        //     'label' => 'Property Types Trail',
                        //     'route' => 'admin.audit.assessment.property.types',
                        // ],
                        // [
                        //     'label' => 'Property Wall Material Trail',
                        //     'route' => 'admin.audit.assessment.wall.material',
                        // ],
                        // [
                        //     'label' => 'Property Roof Material Trail',
                        //     'route' => 'admin.audit.assessment.roof.material',
                        // ],
                        // [
                        //     'label' => 'Property Dimensions Trail',
                        //     'route' => 'admin.audit.assessment.property.dimensions',
                        // ],
                        // [
                        //     'label' => 'Property Value Added Trail',
                        //     'route' => 'admin.audit.assessment.value.added',
                        // ],
                        // [
                        //     'label' => 'Property Use Trail',
                        //     'route' => 'admin.audit.assessment.property.use',
                        // ],
                        // [
                        //     'label' => 'Property Zone Trail',
                        //     'route' => 'admin.audit.assessment.property.zones',
                        // ],
                        // [
                        //     'label' => 'Property Swimming Pool Trail',
                        //     'route' => 'admin.audit.assessment.property.swimmingpool',
                        // ],
                        // [
                        //     'label' => 'Property Inaccessible Trail',
                        //     'route' => 'admin.audit.assessment.property.inaccessible',
                        // ],
                    ]
                ],
                [
                    'label' => 'Send SMS Text',
                    'icon' => 'textsms',
                    'route' => 'admin.notification.index',
                    'role' => 'Super Admin',
                ],
                [
                    'label' => 'Notices',
                    'icon' => 'textsms',
                    'route' => '',
                    'role' => 'Super Admin',
                    'children' => [
                        [
                            'label' => 'Council Reminder1 - None Payment',
                            'route' => '',
        
                        ],
                        [
                            'label' => 'Council Reminder2 - None Payment',
                            'route' => '',
        
                        ],
                        [
                            'label' => 'Council Warning 3 - None Payment',
                            'route' => '',
        
                        ],
                        [
                            'label' => 'Landlord Notice - None Payment',
                            'route' => '',
        
                        ],
                        [
                            'label' => 'Tenant Notice - None Payment',
                            'route' => '',
        
                        ],
                        [
                            'label' => 'Legal  Retainer Notice - None Payment',
                            'route' => '',
        
                        ],
                    ]
                ],
              
            ]
        ],
        [
            'label' => 'CEP',
            'icon' => 'textsms',
            'route' => '',
            'role' => 'Super Admin',
            'children' => [
                [
                    'label' => 'Complaints',
                    'route' => 'admin.complaint-listing',

                ],
                [
                    'label' => 'Service Forms & Resources',
                    'route' => 'admin.forms-resourses',

                ],
                [
                    'label' => 'Information & Tips',
                    'route' => 'admin.information-tips',

                ],
                [
                    'label' => 'Newsletter',
                    'route' => 'admin.newsletter',

                ],
                [
                    'label' => 'Garbage Collection',
                    'route' => 'admin.garbage-collection-list',

                ],
                [
                    'label' => 'Emergency Services',
                    'route' => '',

                ],
                [
                    'label' => 'Users',
                    'route' => 'admin.cep-user.list',

                ]
            ]
        ],
        [
            'label' => 'NOTIFICATIONS',
            'icon' => 'textsms',
            'route' => '',
            'role' => 'Super Admin|Admin|manager|cashiers|supervisor',
        ]
    ]
];
